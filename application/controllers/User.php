<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('post_model');
        $this->load->model('profile_model');

        session_start();
        if ( ! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login/'));
            exit(0);
        }
        else {
            // Check whether the user hasn't been logged out from some where else.
            $this->user_model->confirm_logged_in();
        }
    }

    public function dummy()
    {
        $users = array(
            array(
                'dob'=>'1996-05-11',
                'firstname'=>'Robert',
                'lastname'=>'Odoch',
                'email'=>'rodoch@cess.mak.ac.ug',
                'gender'=>'M',
                'username'=>'xizo',
                'password'=>'frey'
            ),
            array(
                'dob'=>'1996-01-01',
                'firstname'=>'Alex',
                'lastname'=>'Moruleng',
                'email'=>'malex@cess.mak.ac.ug',
                'gender'=>'M',
                'username'=>'moru',
                'password'=>'alex'
            ),
            array(
                'dob'=>'1996-02-26',
                'firstname'=>'Ronald',
                'lastname'=>'Gubi',
                'email'=>'gronald@cess.mak.ac.ug',
                'gender'=>'M',
                'username'=>'gubi',
                'password'=>'badd'
            ),
            array(
                'dob'=>'1994-10-09',
                'firstname'=>'Pius',
                'lastname'=>'Owaro',
                'email'=>'opius@cess.mak.ac.ug',
                'gender'=>'M',
                'username'=>'obbo',
                'password'=>'pius'
            )
        );

        foreach($users as $user)
        {
            $this->user_model->create_dummy_user($user);
        }
    }

    private function initialize_user()
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);

        return $data;
    }

    public function index($user_id, $offset=0)
    {
        $this->load->model('posts_model');

        $data = $this->initialize_user();
        $data['visitor'] = ($_SESSION['user_id'] === $user_id) ? FALSE : TRUE;
        if ($data['visitor']) {
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['secondary_user'] = $this->user_model->get_full_name($user_id);
            $data['title'] = "{$data['secondary_user']}'s Posts";
            $data['suid'] = $user_id;
        }
        else {
            $data['title'] = "{$data['primary_user']}'s Posts";
        }

        $this->load->view('common/header', $data);

        $data['post_errors'] = array();
        if (isset($_SESSION['post_errors']) && ! empty($_SESSION['post_errors'])) {
            $data['post_errors'] = $_SESSION['post_errors'];
            unset($_SESSION['post_errors']);
        }

        $limit = 10;
        $data['has_next'] = FALSE;
        $num_posts = $this->posts_model->get_num_posts($user_id);
        if (($num_posts - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['posts'] = $this->posts_model->get_posts($user_id, $offset, $limit);
        $this->load->view('show-user', $data);
        $this->load->view('common/footer');
    }

    public function chat($offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "Chat With Friends";
        $this->load->view('common/header', $data);

        $limit = 10;
        $data['has_next'] = FALSE;
        if (($data['num_active_friends'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $this->load->view('chat', $data);
        $this->load->view('common/footer');
    }

    public function send_message($user_id, $offset=0)
    {
        $data = $this->initialize_user();
        $data['suid'] = $user_id;
        $data['secondary-user'] = $this->user_model->get_full_name($user_id);
        $data['title'] = "Send a message to {$data['secondary-user']}";
        $data['message_errors'] = array();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($this->input->post('message')))) {
                $data['message_errors']['message'] = "Message can't be empty!";
            }
            else {
                $message = $this->input->post('message');
                $this->user_model->send_message($user_id, $message);
            }
        }

        $this->load->view('common/header', $data);

        $limit = 10;
        $num_convo = $this->user_model->get_num_conversation($user_id);
        $data['has_next'] = FALSE;
        if (($num_convo - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['messages'] = $this->user_model->get_conversation($user_id, $offset, $limit, TRUE);
        $this->load->view('message', $data);
        $this->load->view('common/footer');
    }

    public function new_post($audience='timeline')
    {
        $post_errors = array();
        if (empty(trim($this->input->post('post')))) {
            $post_errors['post'] = 'Please enter what to post!';
            $_SESSION['post_errors'] = $post_errors;
            if ($audience === 'group') {
                redirect(base_url('group/index/' . $_SESSION['group_id']));
            }
            else {
                redirect(base_url('user/index/' . $_SESSION['user_id']));
            }
            exit(1);
        }

        $post = $this->input->post('post');
        if ($audience === 'group') {
            $audience_id = $_SESSION['group_id'];
        }
        else {
            $audience_id = $_SESSION['user_id'];
        }

        if ($this->post_model->post($post, $audience_id) && $audience === 'group') {
            redirect(base_url('group/index/' . $_SESSION['group_id']));
        }
        else {
            redirect(base_url('user/index/'. $_SESSION['user_id']));
        }
        exit(0);
    }

    public function post($post_id)
    {
        $data = $this->initialize_user();
        $data['title'] = "{$data['primary_user']}'s Post";
        $this->load->view('common/header', $data);

        $data['post'] = $this->post_model->get_post($post_id);

        $offset = 0;
        $limit = 10;
        $data['has_next'] = FALSE;
        if (($data['post']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['comments'] = $this->post_model->get_comments($post_id, $offset, $limit);
        $this->load->view('show-post', $data);
        $this->load->view('common/footer');
    }

    public function notifications($offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "Your Notifications";
        $this->load->view('common/header', $data);

		$limit = 10;
		$data['has_next'] = FALSE;
		if ($data['num_new_notifs'] > 0) {
		    // First show only the new notifications.
			$data['notifications'] = $this->user_model->get_notifications($offset, $limit, TRUE, TRUE);
			if (($data['num_new_notifs'] - $offset) > $limit) {
				$data['has_next'] = TRUE;
				$data['next_offset'] = ($offset + $limit);
			}
			else {
			    $num_notifications = $this->user_model->get_num_notifs(FALSE);
			    if ($num_notifications > $data['num_new_notifs']) {
			        $data['has_next'] = TRUE;
			        $data['next_offset'] = ($offset + $data['num_new_notifs']);
			    }
			}
		}
		else {
		    $num_notifications = $this->user_model->get_num_notifs(FALSE);
		    $data['notifications'] = $this->user_model->get_notifications($offset, $limit, TRUE, FALSE);
		    if (($num_notifications - $offset) > $limit) {
		        $data['has_next'] = TRUE;
		        $data['next_offset'] = ($offset + $limit);
		    }
		}

        $this->load->view('show-notifications', $data);
        $this->load->view('common/footer');
    }

    public function find_friends($offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "Find Friends";
        $this->load->view('common/header', $data);

        $limit = 10;
        $num_suggested_users = $this->user_model->get_num_suggested_users();
        $data['has_next'] = FALSE;
        if (($num_suggested_users - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $this->load->view('find-friends', $data);
        $this->load->view('common/footer');
    }

    public function friend_requests($offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "Your Friend Requests";
        $this->load->view('common/header', $data);

        $limit = 10;
        $data['has_next'] = FALSE;
        if (($data['num_friend_requests'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($limit + $offset);
        }

        $data['friend_requests'] = $this->user_model->get_friend_requests();
        $this->load->view('friend-requests', $data);
        $this->load->view('common/footer');
    }

    public function add_friend($user_id)
    {
        $this->user_model->send_friend_request($user_id);
        redirect(base_url("user/index/{$user_id}"));
    }

    public function accept_friend($user_id)
    {
        $this->user_model->confirm_friend_request($user_id);
        redirect(base_url("user/index/{$user_id}"));
    }
    public function messages($offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "Your Messages";
        $this->load->view('common/header', $data);

        $limit = 10;
        $data['has_next'] = FALSE;
        if ($data['num_new_messages'] > 0) {  // There are new messages.
            // First show only the new messages.
            $data['messages'] = $this->user_model->get_messages($offset, $limit, TRUE, TRUE);
            if (($data['num_new_messages'] - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }
            else {
                $num_messages = $this->user_model->get_num_messages(FALSE);
                if ($num_messages > 0) {
                    $data['has_next'] = TRUE;
                    $data['next_offset'] = ($offset + $data['num_new_messages']);
                }
            }
        }
        else {
            $num_messages = $this->user_model->get_num_messages(FALSE);
            $data['messages'] = $this->user_model->get_messages($offset, $limit, TRUE, FALSE);
            if (($num_messages - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }
        }

        $this->load->view('show-messages', $data);
        $this->load->view('common/footer');
    }

    public function friends($user_id=null, $offset=0)
    {
        if ($user_id === null) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->initialize_user();
        $data['visitor'] = ($_SESSION['user_id'] === $user_id) ? FALSE : TRUE;
        if ($data['visitor']) {
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['secondary_user'] = $this->user_model->get_full_name($user_id);
            $data['title'] = "{$data['secondary_user']}'s Friends";
            $data['suid'] = $user_id;
        }
        else {
            $data['title'] = "{$data['primary_user']}'s Friends";
        }

        $this->load->view("common/header", $data);

        $limit = 10;
        $data['has_next'] = FALSE;
        $num_friends = $this->user_model->get_num_friends($user_id);
        if (($num_friends - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($limit + $offset);
        }

        $data['friends'] = $this->user_model->get_friends($user_id, TRUE, $offset, $limit);
        $this->load->view("show-friends", $data);
        $this->load->view("common/footer");
    }

    public function edit_profile()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your profile";
        $this->load->view('common/header', $data);
        $this->load->view('edit-profile', $data);
        $this->load->view('common/footer');
    }

    public function edit_college()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your college";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['error_message'] = array();
            $data['college_id'] = $this->input->post("college");
            $data['school_id'] = $this->input->post("school");

            $start_month = $this->input->post("start-month");
            $start_year = $this->input->post("start-year");
            $data['start_date'] = "{$start_month}-{$start_year}";

            $end_month = $this->input->post("end-month");
            $end_year = $this->input->post("end-year");
            $data['end_date'] = "{$end_month}-{$end_year}";
            if ($end_year < $start_year ||
                $start_month < 1 || $start_month > 12 ||
                $end_month < 1 || $end_month > 12) {
                $data['error_message'][] = "Invalid years entered! Please try again.";
            }

            if ( ! $this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                $data['error_message'][] = "Your college and school do not match! Please try again.";
            }

            if ($data['error_message']) {
                $data['colleges'] = $this->profile_model->get_colleges();
                $data['schools'] = $this->profile_model->get_schools();
            }
            else {
                $this->profile_model->add_college($data);
                $this->profile_model->add_school($data);

                $data['success_message'] = "Your college and school have been succesfully saved.";
            }
        }
        else {
            $data['colleges'] = $this->profile_model->get_colleges();
            $data['schools'] = $this->profile_model->get_schools();
        }

        $this->load->view("edit-college", $data);
        $this->load->view("common/footer");
    }

    public function edit_programme()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your programme";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['programme_id'] = $this->input->post("programme");
            $data['year_of_study'] = $this->input->post("ystudy");

            $start_month = $this->input->post("start-month");
            $start_year = $this->input->post("start-year");
            $data['start_date'] = "{$start_month}-{$start_year}";

            $end_month = $this->input->post("end-month");
            $end_year = $this->input->post("end-year");
            $data['end_date'] = "{$end_month}-{$end_year}";
            if ($end_year < $start_year ||
                $start_month < 1 || $start_month > 12 ||
                $end_month < 1 || $end_month > 12) {
                $data['error_message'] = "Invalid years entered! Please try again.";
                $data['programmes'] = $this->profile_model->get_programmes();
            }
            else {
                $this->profile_model->add_programme($data);
                $data['success_message'] = "Your programme details have been successfully saved.";
            }
        }
        else {
            $data['programmes'] = $this->profile_model->get_programmes();
        }
        $this->load->view("edit-programme", $data);
        $this->load->view("common/footer");
    }

    public function edit_hall()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit hall of attachment";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['hall_id'] = $this->input->post("hall");
            $resident = $this->input->post("resident");
            $data['resident'] = ($resident) ? 1 : 0;

            $start_month = $this->input->post("start-month");
            $start_year = $this->input->post("start-year");
            $data['start_date'] = "{$start_month}-{$start_year}";

            $end_month = $this->input->post("end-month");
            $end_year = $this->input->post("end-year");
            $data['end_date'] = "{$end_month}-{$end_year}";
            if ($end_year < $start_year ||
                $start_month < 1 || $start_month > 12 ||
                $end_month < 1 || $end_month > 12) {
                $data['error_message'] = "Invalid years entered! Please try again.";
                $data['halls'] = $this->profile_model->get_halls();
            }
            else {
                $this->profile_model->add_hall($data);
                $data['success_message'] = "Your hall details have been successfully saved.";
            }
        }
        else {
            $data['halls'] = $this->profile_model->get_halls();
        }
        $this->load->view("edit-hall", $data);
        $this->load->view("common/footer");
    }

    public function edit_hostel()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit hostel";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['hostel_id'] = $this->input->post("hostel");

            $start_month = $this->input->post("start-month");
            $start_year = $this->input->post("start-year");
            $data['start_date'] = "{$start_month}-{$start_year}";

            $end_month = $this->input->post("end-month");
            $end_year = $this->input->post("end-year");
            $data['end_date'] = "{$end_month}-{$end_year}";
            if ($end_year < $start_year ||
                $start_month < 1 || $start_month > 12 ||
                $end_month < 1 || $end_month > 12) {
                $data['error_message'] = "Invalid years entered! Please try again.";
                $data['hostels'] = $this->profile_model->get_hostels();
            }
            else {
                $this->profile_model->add_hostel($data);
                $data['success_message'] = "Your hostel details have been successfully saved.";
            }
        }
        else {
            $data['hostels'] = $this->profile_model->get_hostels();
        }
        $this->load->view("edit-hostel", $data);
        $this->load->view("common/footer");
    }

    public function edit_country()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your country of origin";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['country_id'] = $this->input->post("country");
            if ($this->input->post("country_unavailable") == TRUE) {
                // Display a form allowing the user to enter his/her country
                // and notifiy the admin.
            }
            else {
                $this->profile_model->add_country($data);
                $data['success_message'] = "Your country details have been successfully saved.";
            }
        }
        else {
            $data['countries'] = $this->profile_model->get_countries();
        }
        $this->load->view("edit-country", $data);
        $this->load->view("common/footer");
    }

    public function edit_district($district_name=null, $save_district=FALSE, $district_id=null)
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your district";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['district'] = $this->input->post("district");
            if (empty(trim($data['district']))) {
                $data['error_message'] = "Please enter a district/state";
            }
            else {
                //$data['districts'] = $this->profile_model->get_districts($district);
                $data['districts'] = [array('district_id' => 1, 'district_name' => 'Oyam'), array('district_id' => 2, 'district_name' => 'Kampala')];
            }
        }
        elseif ($save_district) {
            $this->profile_model->add_district($district_id, $district_name);
            $data['success_message'] = "Your district details have been successfully updated";
        }

        $this->load->view("edit-district", $data);
        $this->load->view("common/footer");
    }
}
?>
