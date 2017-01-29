<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model('user_model');
        $this->load->model('post_model');
        $this->load->model('profile_model');

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
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
        $data['primary_user'] = $this->user_model->get_name($_SESSION['user_id']);
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests(TRUE);
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
            $data['secondary_user'] = $this->user_model->get_name($user_id);
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

        // Maximum number of users to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

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
        $data['secondary-user'] = $this->user_model->get_name($user_id);
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

        // Maximum number of messages to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $num_convo = $this->user_model->get_num_conversation($user_id);
        $data['has_next'] = FALSE;
        if (($num_convo - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['messages'] = $this->user_model->get_conversation($user_id, $offset, $limit);
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
        }

        $post = $this->input->post('post');
        if ($audience === 'group') {
            $audience_id = $_SESSION['group_id'];
        }
        else {
            $audience_id = $_SESSION['user_id'];
        }

        if ($this->post_model->post($post, $audience_id) && $audience === 'group') {
            redirect(base_url("group/index/{$_SESSION['group_id']}"));
        }
        else {
            redirect(base_url("user/index/{$_SESSION['user_id']}"));
        }
    }

    public function post($post_id, $offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "{$data['primary_user']}'s Post";
        $this->load->view('common/header', $data);

        $data['post'] = $this->post_model->get_post($post_id);

        $limit = 10;
        $data['has_next'] = FALSE;
        if (($data['post']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['comments'] = $this->post_model->get_comments($post_id, $offset, $limit);
        $this->load->view('show-post', $data);
        $this->load->view('common/footer');
    }

    public function notifications($offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "Your Notifications";
        $this->load->view('common/header', $data);

        // Maximum number of notifications to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

		$data['has_next'] = FALSE;
		if ($data['num_new_notifs'] > 0) {
		    // First show only the new notifications.
			$data['notifications'] = $this->user_model->get_notifications($offset, $limit, TRUE);
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
		    $data['notifications'] = $this->user_model->get_notifications($offset, $limit, FALSE);
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
        $num_friend_requests = $this->user_model->get_num_friend_requests(FALSE);
        if (($num_friend_requests - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($limit + $offset);
        }

        $data['friend_requests'] = $this->user_model->get_friend_requests($offset, $limit);
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

        // Maximum number of messages to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        if ($data['num_new_messages'] > 0) {  // There are new messages.
            // First show only the new messages.
            $data['messages'] = $this->user_model->get_messages($offset, $limit, TRUE);
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
            $data['messages'] = $this->user_model->get_messages($offset, $limit, FALSE);
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
            $data['secondary_user'] = $this->user_model->get_name($user_id);
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

        $data['friends'] = $this->user_model->get_friends($user_id, $offset, $limit);
        $this->load->view("show-friends", $data);
        $this->load->view("common/footer");
    }

    public function profile($user_id=NULL)
    {
        if ($user_id === NULL) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->initialize_user();
        $data['visitor'] = ($_SESSION['user_id'] === $user_id) ? FALSE : TRUE;
        if ($data['visitor']) {
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['secondary_user'] = $this->user_model->get_name($user_id);
            $data['title'] = "About {$data['secondary_user']}";
            $data['suid'] = $user_id;
        }
        else {
            $data['title'] = "About {$data['primary_user']}";
        }

        $this->load->view("common/header", $data);

        $data['profile'] = $this->profile_model->get_profile($user_id);
        $this->load->view("profile", $data);
        $this->load->view("common/footer");
    }

    public function add_college()
    {
        $data = $this->initialize_user();
        $data['title'] = "Add your college";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['error_messages'] = array();
            $data['college_id'] = $this->input->post("college");
            $data['school_id'] = $this->input->post("school");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_messages'][] = "Invalid dates entered!<br>Please check the order of the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_messages'][] = "Invalid dates entered!<br>Please check the dates and try again.";
            }

            if ( ! $this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                $data['error_messages'][] = "Your college and school do not match!<br>Please try again.";
            }

            if ($data['error_messages']) {
                $data['colleges'] = $this->profile_model->get_colleges();
                $data['schools'] = $this->profile_model->get_schools();
            }
            else {
                if ($this->profile_model->add_college($data)) {
                    $data['success_message'] = "Your college and school have been succesfully saved.";
                }
                else {
                    $data['error_messages'][] = "The years you entered conflict with one of your records.<br><strong>Remember</strong> that " .
                                                "you cannot be at two colleges or schools at the same time.";
                    $data['colleges'] = $this->profile_model->get_colleges();
                    $data['schools'] = $this->profile_model->get_schools();
                }
            }
        }
        else {
            $data['colleges'] = $this->profile_model->get_colleges();
            $data['schools'] = $this->profile_model->get_schools();
        }

        $data['heading'] = "Add College";
        $data['form_action'] = "user/add-college";
        $this->load->view("edit-college", $data);
        $this->load->view("common/footer");
    }

    public function edit_college($user_college_id=NULL)
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your college";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['user_college_id'] = $this->input->post("user-college-id");
            $data['college_id'] = $this->input->post("college");
            $data['school_id'] = $this->input->post("school");

            $data['error_messages'] = array();
            if ( ! $this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                $data['error_messages'][] = "Your college and school do not match!<br>Please try again.";
            }

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");

            $data['old_start_date'] = $this->input->post("old-start-date");
            $data['old_end_date'] = $this->input->post("old-end-date");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_messages'][] = "Invalid dates entered!<br>Please check the order of the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_messages'][] = "Invalid dates entered!<br>Please check the dates and try again.";
            }

            if ($data['error_messages']) {
                $college_info = $this->profile_model->get_user_college($data['user_college_id']);
                $data['colleges'] = $college_info['colleges'];
                $data['schools'] = $college_info['schools'];
            }
            else {
                if ($this->profile_model->update_college($data)) {
                    $data['success_message'] = "Your edits have been succesfully saved.";
                }
                else {
                    $data['error_messages'][] = "Sorry, but something went wrong.";
                    $college_info = $this->profile_model->get_user_college($data['user_college_id']);
                    $data['colleges'] = $college_info['colleges'];
                    $data['schools'] = $college_info['schools'];

                    $data['start_year'] = $data['colleges'][0]['start_year'];
                    $data['start_month'] = $data['colleges'][0]['start_month'];
                    $data['start_day'] = $data['colleges'][0]['start_day'];

                    $data['end_year'] = $data['colleges'][0]['end_year'];
                    $data['end_month'] = $data['colleges'][0]['end_month'];
                    $data['end_day'] = $data['colleges'][0]['end_day'];
                }
            }
        }
        else {
            $college_info = $this->profile_model->get_user_college($user_college_id);
            if ($college_info) {
                // Convert it to a form compatible with code in the view.
                $data['user_college_id'] = $user_college_id;
                $data['colleges'] = $college_info['colleges'];
                $data['schools'] = $college_info['schools'];

                $data['start_year'] = $data['colleges'][0]['start_year'];
                $data['start_month'] = $data['colleges'][0]['start_month'];
                $data['start_day'] = $data['colleges'][0]['start_day'];

                $data['end_year'] = $data['colleges'][0]['end_year'];
                $data['end_month'] = $data['colleges'][0]['end_month'];
                $data['end_day'] = $data['colleges'][0]['end_day'];

                $data['old_start_date'] = $college_info['colleges'][0]['date_from'];
                $data['old_end_date'] = $college_info['colleges'][0]['date_to'];
            }
            else {
                $data['error_messages'][] = "Sorry, but something went wrong.";
            }
        }

        $data['heading'] = "Edit College";
        $data['form_action'] = "user/edit-college";
        $this->load->view("edit-college", $data);
        $this->load->view("common/footer");
    }

    public function add_programme($user_college_id=NULL)
    {
        $data = $this->initialize_user();
        $data['title'] = "Add your programme";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['programme_id'] = $this->input->post("programme");
            $data['year_of_study'] = $this->input->post("ystudy");

            $start_date = $this->input->post("start-date");
            $end_date = $this->input->post("end-date");
            if (date_create($start_date) && date_create($end_date)) {
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
            }
            else {
                $data['error_message'] = "Sorry, but something went wrong.";
            }

            if (isset($data['error_message'])) {
                $programmes = $this->profile_model->get_programmes($user_college_id);
                if ($programmes) {
                    $data['programmes'] = $programmes;
                    $college_info = $this->profile_model->get_user_college($user_college_id);
                    $data['start_date'] = $college_info['colleges'][0]['date_from'];
                    $data['end_date'] = $college_info['colleges'][0]['date_to'];
                }
            }
            else {
                if ($this->profile_model->add_programme($data)) {
                    $data['success_message'] = "Your programme details have been successfully saved.";
                }
                else {
                    $data['error_message'] = "Sorry, but something went wrong.";
                }
            }
        }
        else {
            $programmes = $this->profile_model->get_programmes($user_college_id);
            if ($programmes) {
                $data['programmes'] = $programmes;
                $college_info = $this->profile_model->get_user_college($user_college_id);
                $data['start_date'] = $college_info['colleges'][0]['date_from'];
                $data['end_date'] = $college_info['colleges'][0]['date_to'];
            }
            else {
                $data['error_message'] = "Sorry, but something went wrong.";
            }
        }

        $data['heading'] = "Add Programme";
        $data['form_action'] = "user/add-programme";
        $this->load->view("edit-programme", $data);
        $this->load->view("common/footer");
    }

    public function edit_programme($user_programme_id=NULL)
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your programme";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['user_programme_id'] = $this->input->post("user-programme-id");
            $data['programme_id'] = $this->input->post("programme");
            $data['year_of_study'] = $this->input->post("ystudy");

            $programme_info = $this->profile_model->get_user_programme($data['user_programme_id']);
            if ($programme_info) {
                $data['programmes'] = $programme_info['programmes'];
                $data['programme_id'] = $data['programmes'][0]['programme_id'];
                $data['start_date'] = $data['programmes'][0]['date_from'];
                $data['end_date'] = $data['programmes'][0]['date_to'];

                if ($this->profile_model->update_programme($data)) {
                    $data['success_message'] = "Your edits have been successfully saved.";
                }
                else {
                    $data['error_message'] = "Sorry, but someting went wrong.";
                    $data['year_of_study'] = $data['programmes'][0]['year_of_study'];
                }
            }
            else {
                $data['error_message'] = "Sorry, but someting went wrong.";
            }
        }
        else {
            $programme_info = $this->profile_model->get_user_programme($user_programme_id);
            if ($programme_info) {
                $data['programmes'] = $programme_info['programmes'];
                $data['user_programme_id'] = $user_programme_id;
                $data['year_of_study'] = $data['programmes'][0]['year_of_study'];
            }
            else {
                $data['error_message'] = "Sorry, but something went wrong.";
            }
        }

        $data['heading'] = "Edit Programme Details";
        $data['form_action'] = "user/edit-programme";
        $this->load->view("edit-programme", $data);
        $this->load->view("common/footer");
    }

    public function add_hall()
    {
        $data = $this->initialize_user();
        $data['title'] = "Add hall of attachment";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['hall_id'] = $this->input->post("hall");
            $data['resident'] = $this->input->post("resident");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered!<br>Please check the order of the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered!<br>Please check the dates and try again.";
            }

            if (isset($data['error_message'])) {
                $data['halls'] = $this->profile_model->get_halls();
            }
            else {
                if ($this->profile_model->add_hall($data)) {
                    $data['success_message'] = "Your hall details have been successfully saved.";
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br><strong>Remember</strong> that " .
                                             "you cannot be attached to/a resident of two halls at the same time.";
                    $data['halls'] = $this->profile_model->get_halls();
                }
            }
        }
        else {
            $data['halls'] = $this->profile_model->get_halls();
        }

        $data['heading'] = "Add Hall";
        $data['form_action'] = "user/add-hall";
        $this->load->view("edit-hall", $data);
        $this->load->view("common/footer");
    }

    public function edit_hall($user_hall_id=NULL)
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit hall of attachment";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['hall_id'] = $this->input->post("hall");
            $data['resident'] = $this->input->post("resident");

            $data['user_hall_id'] = $this->input->post("user-hall-id");

            $data['old_start_date'] = $this->input->post("old-start-date");
            $data['old_end_date'] = $this->input->post("old-end-date");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered!<br>Please check the order of the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered!<br>Please check the dates and try again.";
            }

            if (isset($data['error_message'])) {
                $hall_info = $this->profile_model->get_user_hall($data['user_hall_id']);
                if ($hall_info) {
                    $data['halls'] = $hall_info['halls'];
                }
            }
            else {
                if ($this->profile_model->update_hall($data)) {
                    $data['success_message'] = "Your edits have been successfully saved.";
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br><strong>Remember</strong> that " .
                                             "you cannot be attached to/a resident of two halls at the same time.";
                    $hall_info = $this->profile_model->get_user_hall($data['user_hall_id']);
                    if ($hall_info) {
                        $data['halls'] = $hall_info['halls'];
                    }
                }
            }
        }
        else {
            $hall_info = $this->profile_model->get_user_hall($user_hall_id);
            if ($hall_info) {
                $data['halls'] = $hall_info['halls'];
                $data['user_hall_id'] = $user_hall_id;

                $data['resident'] = $data['halls'][0]['resident'];

                $data['start_day'] = $data['halls'][0]['start_day'];
                $data['start_month'] = $data['halls'][0]['start_month'];
                $data['start_year'] = $data['halls'][0]['start_year'];

                $data['end_day'] = $data['halls'][0]['end_day'];
                $data['end_month'] = $data['halls'][0]['end_month'];
                $data['end_year'] = $data['halls'][0]['end_year'];

                $data['old_start_date'] = $data['halls'][0]['date_from'];
                $data['old_end_date'] = $data['halls'][0]['date_to'];
            }
            else {
                $data['error_message'] = "Sorry, but something went wrong.";
            }
        }

        $data['heading'] = "Edit Hall";
        $data['form_action'] = "user/edit-hall";
        $this->load->view("edit-hall", $data);
        $this->load->view("common/footer");
    }

    public function add_hostel()
    {
        $data = $this->initialize_user();
        $data['title'] = "Add hostel";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['hostel_id'] = $this->input->post("hostel");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered!<br>Please check the order of the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered!<br>Please check the dates and try again.";
            }

            if (isset($data['error_message'])) {
                $data['hostels'] = $this->profile_model->get_hostels();
            }
            else {
                if ($this->profile_model->add_hostel($data)) {
                    $data['success_message'] = "Your hostel details have been successfully saved.";
                }
                else {
                    $data['error_message'] = "The hostel you entered conflicts with one of your records.<br>" .
                                             "Either you indicated that you are a resident of a hall, Or<br>" .
                                             "The date overlaps with that of one of the hostels you have been to.";
                    $data['hostels'] = $this->profile_model->get_hostels();
                }
            }
        }
        else {
            $data['hostels'] = $this->profile_model->get_hostels();
        }

        $data['heading'] = "Add Hostel";
        $data['form_action'] = "user/add-hostel";
        $this->load->view("edit-hostel", $data);
        $this->load->view("common/footer");
    }

    public function edit_hostel($user_hostel_id=NULL)
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit hostel";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['hostel_id'] = $this->input->post("hostel");
            $data['user_hostel_id'] = $this->input->post("user-hostel-id");

            $data['old_start_date'] = $this->input->post("old-start-date");
            $data['old_end_date'] = $this->input->post("old-end-date");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered!<br>Please check the order of the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered!<br>Please check the dates and try again.";
            }

            if (isset($data['error_message'])) {
                $hostel_info = $this->profile_model->get_user_hostel($data['user_hostel_id']);
                if ($hostel_info) {
                    $data['hostels'] = $hostel_info['hostels'];
                }
            }
            else {
                if ($this->profile_model->update_hostel($data)) {
                    $data['success_message'] = "Your edits have been successfully saved.";
                }
                else {
                    $data['error_message'] = "The hostel you entered conflicts with one of your records.<br>" .
                                             "Either you indicated that you are a resident of a hall, Or<br>" .
                                             "The date overlaps with that of one of the hostels you have been to.";
                    $data['hostels'] = $this->profile_model->get_user_hostel($data['user_hostel_id']);
                }
            }
        }
        else {
            $hostel_info = $this->profile_model->get_user_hostel($user_hostel_id);
            if ($hostel_info) {
                $data['hostels'] = $hostel_info['hostels'];
                $data['user_hostel_id'] = $user_hostel_id;

                $data['start_day'] = $data['hostels'][0]['start_day'];
                $data['start_month'] = $data['hostels'][0]['start_month'];
                $data['start_year'] = $data['hostels'][0]['start_year'];

                $data['end_day'] = $data['hostels'][0]['end_day'];
                $data['end_month'] = $data['hostels'][0]['end_month'];
                $data['end_year'] = $data['hostels'][0]['end_year'];

                $data['old_start_date'] = $data['hostels'][0]['date_from'];
                $data['old_end_date'] = $data['hostels'][0]['date_to'];
            }
            else {
                $data['error_message'] = "Sorry, but something went wrong.";
            }
        }

        $data['heading'] = "Edit Hostel";
        $data['form_action'] = "user/edit-hostel";
        $this->load->view("edit-hostel", $data);
        $this->load->view("common/footer");
    }

    public function edit_country()
    {
        $data = $this->initialize_user();
        $data['title'] = "Edit your country of origin";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $country_id = $this->input->post("country");
            if ($country_id == "none") {
                // Display a form allowing the user to enter his/her country
                // and notifiy the admin.
                redirect(base_url("request-admin/add-country"));
            }
            else {
                $this->profile_model->add_country($country_id);
                $data['success_message'] = "Your country details have been successfully saved.";
            }
        }
        else {
            $data['countries'] = $this->profile_model->get_countries();
        }
        $this->load->view("edit-country", $data);
        $this->load->view("common/footer");
    }

    public function add_district($district_id=null)
    {
        $data = $this->initialize_user();
        $data['title'] = "Add your district";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['district'] = $this->input->post("district");
            if (empty(trim($data['district']))) {
                $data['error_message'] = "Please enter the name of your district or state and try again.";
            }
            else {
                $data['districts'] = $this->profile_model->get_districts($data['district']);
            }
        }
        elseif ($district_id) {
            if ($this->profile_model->add_district($district_id)) {
                $data['success_message'] = "Your district details have been successfully updated.";
            }
            else {
                $data['error_message'] = "Sorry, but an error occured.";
            }
        }

        $data['heading'] = "Add District";
        $this->load->view("edit-district", $data);
        $this->load->view("common/footer");
    }
}
?>
