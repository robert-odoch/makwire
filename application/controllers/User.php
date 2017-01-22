<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login/'));
            exit(0);
        }
        else {
            // Check whether the user hasn't been logged out from some where else.
            $this->confirm_logged_in();
        }

        $this->load->model('user_model');
        $this->load->model('post_model');
        $this->load->model("college_model");
        $this->load->model("school_model");
        $this->load->model("programme_model");
    }

    private function confirm_logged_in()
    {
        $q = sprintf("SELECT logged_in FROM users WHERE user_id=%d",
                     $_SESSION['user_id']);
        $query = $this->db->query($q);
        if ( ! $query) {
            $error = $this->db->error();
            echo $error;
            exit(1);
        }

        if ($query->row()->logged_in == 0) {
            unset($_SESSION['user_id']);
            $_SESSION = array();
            $_SESSION['message'] = "This account was logged out from another location, " .
                                   "please log in again to continue using this account. " .
                                   "We are sorry for bothering you.";
            redirect(base_url("login/"));
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

    public function index($user_id, $offset=0)
    {
        $this->load->model('posts_model');

        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['visitor'] = ($_SESSION['user_id'] === $user_id) ? FALSE : TRUE;
        if ($data['visitor']) {
            $data['suid'] = $user_id;
            $data['secondary_user'] = $this->user_model->get_full_name($user_id);
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['title'] = "{$data['secondary_user']}'s Posts";
        }
        else {
            $data['title'] = "{$data['primary_user']}'s Posts";
        }
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $data['post_errors'] = array();
        if (isset($_SESSION['post_errors']) && ! empty($_SESSION['post_errors'])) {
            $data['post_errors'] = $_SESSION['post_errors'];
            unset($_SESSION['post_errors']);
        }
        $limit = 10;
        $data['posts'] = $this->posts_model->get_posts($user_id, $offset, $limit);
        $data['has_next'] = FALSE;
        $num_posts = $this->posts_model->get_num_posts($user_id);
        if (($num_posts - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view('show-user', $data);
        $this->load->view('common/footer');
    }

    public function chat($offset=0)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Chat With Friends";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $limit = 10;
        $data['has_next'] = FALSE;
        if (($data['num_active_friends'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $this->load->view('chat', $data);
        $this->load->view('common/footer');
    }

    public function send_message($user_id, $offset=0)
    {
        $data['message_errors'] = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($this->input->post('message'))) {
                $data['message_errors']['message'] = "Message can't be empty!";
            }
            else {
                $message = $this->input->post('message');
                $this->user_model->send_message($user_id, $message);
            }
        }

        $data['suid'] = $user_id;
        $data['secondary-user'] = $this->user_model->get_full_name($user_id);
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Send a message to {$data['secondary-user']}";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
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
        $this->load->model('post_model');

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
        $this->load->model('post_model');

        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "{$data['primary_user']}'s Post";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $this->load->view('common/header', $data);

        $data['post'] = $this->post_model->get_post($post_id);

        $offset = 0;
        $limit = 10;
        $data['comments'] = $this->post_model->get_comments($post_id, $offset, $limit);
        $data['has_next'] = FALSE;
        if (($data['post']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view('show-post', $data);
        $this->load->view('common/footer');
    }

    public function notifications($offset=0)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Your Notifications";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
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
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Find Friends";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users($offset, $limit, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);

        $limit = 10;
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $num_suggested_users = $this->user_model->get_num_suggested_users();
        $data['has_next'] = FALSE;
        if (($num_suggested_users - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view('find-friends', $data);
        $this->load->view('common/footer');
    }

    public function friend_requests($offset=0)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Your Friend Requests";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
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
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Your Messages";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
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

    public function edit_college()
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Edit your college";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $college_id = $this->input->post("college");
            $school_id = $this->input->post("school");
            if ($this->college_model->college_and_school_exists($college_id, $school_id)) {
                $this->user_model->add_college($college_id);
                $this->school_model->add_school($school_id);

                $data['success_message'] = "Your college and school have been succesfully saved.";
            }
            else {
                $data['error_message'] = "Your college and school do not match! Please try again.";
                $data['colleges'] = $this->college_model->get_colleges();
                $data['schools'] = $this->school_model->get_schools();
            }
        }
        else {
            $data['colleges'] = $this->college_model->get_colleges();
            $data['schools'] = $this->school_model->get_schools();
        }

        $this->load->view("edit-college", $data);
        $this->load->view("common/footer");
    }

    public function edit_programme()
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['title'] = "Edit your programme";
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $this->load->view('common/header', $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['programme_id'] = $this->input->post("programme");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            $data['year_of_study'] = $this->input->post("ystudy");
            if ($data['end_year'] < $data['start_year'] ||
                $data['end_year'] == $data['start_year']) {
                $data['error_message'] = "Invalid years entered! Please try again.";
                $data['programmes'] = $this->programme_model->get_programmes();
            }
            else {
                $this->user_model->add_programme($data);
                $data['success_message'] = "Your programme details have been successfully saved.";
            }
        }
        else {
            $data['programmes'] = $this->programme_model->get_programmes();
        }
        $this->load->view("edit-programme", $data);
        $this->load->view("common/footer");
    }
}
?>
