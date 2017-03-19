<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model([
            'user_model', 'post_model',
            'profile_model', 'photo_model',
            'utility_model'
        ]);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    // TODO: Delete this funtion when everything is done.
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

    public function news_feed($offset = 0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Makwire | News Feed";
        $this->load->view('common/header', $data);

        if (isset($_SESSION['post_error']) && !empty($_SESSION['post_error'])) {
            $data['post_error'] = $_SESSION['post_error'];
            unset($_SESSION['post_error']);
        }

        $limit = 10;  // Maximum number of posts to show.
        $data['has_next'] = FALSE;
        $num_posts_and_photos = $this->user_model->get_num_news_feed_posts_and_photos();
        if (($num_posts_and_photos - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['posts_and_photos'] = $this->user_model->get_news_feed_posts_and_photos($offset, $limit);
        $data['page'] = 'news-feed';
        $data['is_visitor'] = FALSE;
        $this->load->view('show-user', $data);
        $this->load->view('common/footer');
    }

    public function index($user_id, $offset=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = format_name($data['primary_user']) . ' posts';

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;;
        if ($data['is_visitor']) {
            try {
                $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
            }
            catch (UserNotFoundException $e) {
                show_404();
            }

            $data['suid'] = $user_id;
            $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['title'] = format_name($data['secondary_user']) . ' posts';
        }

        $this->load->view('common/header', $data);

        if (isset($_SESSION['post_error']) && !empty($_SESSION['post_error'])) {
            $data['post_error'] = $_SESSION['post_error'];
            unset($_SESSION['post_error']);
        }

        $limit = 10;  // Maximum number of posts to show.
        $data['has_next'] = FALSE;
        $num_posts_and_photos = $this->user_model->get_num_timeline_posts_and_photos($user_id);
        if (($num_posts_and_photos - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['posts_and_photos'] = $this->user_model->get_timeline_posts_and_photos($user_id, $offset, $limit);
        $data['page'] = 'index';
        $data['user_id'] = $user_id;
        $this->load->view('show-user', $data);
        $this->load->view('common/footer');
    }

    public function birthday($user_id, $age, $offset=0)
    {
        if (!$this->user_model->can_view_birthday($user_id, $age)) {
            show_404();
        }

        if (!$this->user_model->are_friends($user_id)) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['user'] = $this->user_model->get_profile_name($user_id);
        $data['title'] = format_name($data['user']) . ' birthday';
        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;

        $this->load->view("common/header", $data);

        $limit = 10;
        $num_birthday_messages = $this->user_model->get_num_birthday_messages($user_id);
        $data['has_next'] = FALSE;
        if (($num_birthday_messages - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        if (isset($_SESSION['error_message'])) {
            $data['error_message'] = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }

        $data['birthday_messages'] = $this->user_model->get_birthday_messages($user_id, $age, $offset, $limit);
        $data['user_id'] = $user_id;
        $data['user_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
        $data['dob'] = $this->user_model->get_dob($user_id);
        $data['age'] = $age;
        $this->load->view("show-birthday", $data);
        $this->load->view("common/footer");
    }

    public function send_birthday_message($user_id, $age)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->utility_model->show_permission_denied("You don't have the proper permissions.");
            return;
        }

        $message = trim(strip_tags($this->input->post('birthday-message')));
        if (!$message) {
            $_SESSION['error_message'] = "Message can't be empty!";
        }
        else {
            $this->user_model->send_birthday_message($message, $user_id, $age);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function chat($offset=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Chat With Friends";
        $this->load->view('common/header', $data);

        $limit = 10;  // Maximum number of users to show.
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
        $data = $this->user_model->initialize_user();
        try {
            $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
        }
        catch (UserNotFoundException $e) {
            show_404();
        }

        if (!$this->user_model->are_friends($user_id)) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to send a message to this user.");
            return;
        }

        $data['suid'] = $user_id;
        $data['title'] = "Send a message to {$data['secondary_user']}";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($this->input->post('message')))) {
                $data['error_message'] = "Message can't be empty!";
            }
            else {
                $message = strip_tags($this->input->post('message'));
                $this->user_model->send_message($message, $user_id);
            }
        }

        $limit = 10;  // Maximum number of previous messages to show.
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

    public function new_post()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->utility_model->show_permission_denied("You don't have the proper permissions.");
            return;
        }

        $post = trim(strip_tags($this->input->post('post')));
        if (!$post) {
            $_SESSION['post_error'] = "Post can't be empty!";
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }

        $this->post_model->post($post, 'timeline', $_SESSION['user_id']);
        redirect(base_url("user/{$_SESSION['user_id']}"));
    }

    public function post($post_id, $offset=0)
    {
        try {
            $post = $this->post_model->get_post($post_id);
        }
        catch (PostNotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = format_name($post['author']) . ' post';

        $this->load->view('common/header', $data);

        $data['post'] = $post;

        $limit = 10;  // Maximum number of comments to show.
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
        $data = $this->user_model->initialize_user();
        $data['title'] = "Notifications";
        $this->load->view('common/header', $data);

        $limit = 10;  // Maximum number of notifications to show.
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
			    $num_notifications = $this->user_model->get_num_notifications(FALSE);

                // Here, we are determining if there are older notifications.
                // And if they are there, get the correct offset to use
                // in the view older notifications link.
			    if (($num_notifications - ($offset + $data['num_new_notifs'])) > 0) {
			        $data['has_next'] = TRUE;
                    // To indicate the the notifications that follow are older.
                    $data['older'] = TRUE;
			        $data['next_offset'] = ($offset + $data['num_new_notifs']);
			    }
			}
		}
		else {
		    $num_notifications = $this->user_model->get_num_notifications(FALSE);
		    $data['notifications'] = $this->user_model->get_notifications($offset, $limit, FALSE);
		    if (($num_notifications - $offset) > $limit) {
		        $data['has_next'] = TRUE;
		        $data['next_offset'] = ($offset + $limit);
		    }
		}

        $this->load->view('show-notifications', $data);
        $this->load->view('common/footer');
    }

    public function photo($photo_id, $offset=0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id);
        }
        catch (PhotoNotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = format_name($photo['author']) . ' photo';

        $this->load->view("common/header", $data);

        $data['photo'] = $photo;

        $limit = 10;  // Maximum number of comments to display.
        $data['has_next'] = FALSE;
        if (($data['photo']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['comments'] = $this->photo_model->get_comments($photo_id, $offset, $limit);

        $this->load->view("show-photo", $data);
        $this->load->view("common/footer");
    }

    public function find_friends($offset=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Find Friends";
        $this->load->view('common/header', $data);

        $limit = 10;  // Maximum number of suggested users to show.
        $data['has_next'] = FALSE;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_SESSION['search_results'])) {
            if (isset($_SESSION['search_results'])) {
                $query = $_SESSION['query'];
            }
            else {
                $query = trim($this->input->post('query'));
            }

            if (!$query) {
                $data['error'] = "Query can't be empty!";
            }
            else {
                if (!isset($_SESSION['search_results'])) {
                    $_SESSION['query'] = $query;
                    $_SESSION['search_results'] = TRUE;
                }

                $data['search_results'] = $this->user_model->get_searched_user($query, $offset, $limit);
                if ((count($data['search_results']) - $offset) > $limit) {
                    $data['has_next'] = TRUE;
                    $data['next_of'] = ($offset + $limit);
                }
                else {
                    unset($_SESSION['search_results']);
                    unset($_SESSION['query']);
                }
            }
        }
        else {
            $data['suggested_users'] = $this->user_model->get_suggested_users($offset, $limit);
            if ((count($data['suggested_users']) - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }
        }

        $this->load->view('find-friends', $data);
        $this->load->view('common/footer');
    }

    public function friend_requests($offset=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Friend Requests";
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
        try {
            $this->user_model->send_friend_request($user_id);
            $this->utility_model->show_success("Friend request sent.");
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_permission_denied("Either the two of you are already friends, " .
                                                            "or there exists a pending freind request.");
        }
    }

    public function accept_friend($user_id)
    {
        try {
            $this->user_model->confirm_friend_request($user_id);
            redirect(base_url("user/{$user_id}"));
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_permission_denied("This user didn't send you a friend request.");
        }
    }

    public function messages($offset=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Messages";
        $this->load->view('common/header', $data);

        $limit = 10;  // Maximum number of messages to show.
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

                // Here, we are determining if there are older messages.
                // And if they are there, get the correct offset to use
                // in the view older messages link.
                if (($num_messages - ($offset + $data['num_new_messages'])) > 0) {
                    $data['has_next'] = TRUE;

                    // To indicate that we are showing older messages.
                    $data['older'] = TRUE;
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

    public function friends($user_id=NULL, $offset=0)
    {
        if ($user_id === NULL) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = format_name($data['primary_user']) . ' friends';

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        if ($data['is_visitor']) {
            try {
                $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
            }
            catch (UserNotFoundException $e) {
                show_404();
            }

            $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['suid'] = $user_id;
            $data['title'] = format_name($data['secondary_user']) . ' friends';
        }

        $this->load->view("common/header", $data);

        $limit = 10;  // Maximum number of friends to show.
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

        $data = $this->user_model->initialize_user();
        $data['title'] = "Edit Profile";

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        if ($data['is_visitor']) {
            try {
                $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
            }
            catch (UserNotFoundException $e) {
                show_404();
            }

            $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['suid'] = $user_id;
            $data['title'] = "About {$data['secondary_user']}";
        }

        $this->load->view("common/header", $data);

        $data['profile'] = $this->profile_model->get_profile($user_id);
        $this->load->view("profile", $data);
        $this->load->view("common/footer");
    }

    public function photos($user_id=NULL, $offset=0)
    {
        if ($user_id === NULL) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = format_name($data['primary_user']) . ' photos';

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        if ($data['is_visitor']) {
            try {
                $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
            }
            catch (UserNotFoundException $e) {
                show_404();
            }

            $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
            $data['friendship_status'] = $this->user_model->get_friendship_status($user_id);
            $data['suid'] = $user_id;
            $data['title'] = format_name($data['secondary_user']) . ' photos';
        }

        $this->load->view("common/header", $data);

        $limit = 12;  // Maximum number of photos to show.
        $data['has_next'] = FALSE;
        $num_photos = $this->user_model->get_num_photos($user_id);
        if (($num_photos - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($limit + $offset);
        }

        $data['photos'] = $this->user_model->get_photos($user_id, $offset, $limit);
        $data['user_id'] = $user_id;  // Used in view more photos.
        $this->load->view("show-photos", $data);
        $this->load->view("common/footer");
    }

    public function add_college()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Add your college";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");

            // Validate the dates.
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }

            if (!isset($data['error_message'])) {
                $data['college_id'] = $this->input->post("college");
                $data['school_id'] = $this->input->post("school");

                // Make sure college and school exist.
                if (!$this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                    $data['error_message'] = "Your college and school do not match!<br>Please try again.";
                }
            }

            if (!isset($data['error_message'])) {
                // Try saving the college and school.
                if ($this->profile_model->add_college($data)) {
                    $this->utility_model->show_success("Your college and school have been succesfully saved.");
                    return;
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br>" .
                                                "You can't be at two colleges or schools at the same time.";
                }
            }
        }

        // User reaches here if he has just opened this page, or
        // there is an error in submitted form data.
        $data['colleges'] = $this->profile_model->get_colleges();
        $data['schools'] = $this->profile_model->get_schools();

        $data['heading'] = "Add College";
        $data['form_action'] = base_url("user/add-college");
        $this->load->view("edit-college", $data);
        $this->load->view("common/footer");
    }

    public function edit_college($user_college_id=NULL)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Edit your college";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // DON'T try to remove this line,
            // $user_college_id useful when re-displaying the form.
            $user_college_id = $this->input->post("user-college-id");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");

            $data['old_start_date'] = $this->input->post("old-start-date");
            $data['old_end_date'] = $this->input->post("old-end-date");

            // Validate the dates.
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }

            if (!isset($data['error_message'])) {
                $data['user_college_id'] = $user_college_id;
                $data['college_id'] = $this->input->post("college-id");
                $data['school_id'] = $this->input->post("school-id");

                // Check whether college and school exist.
                if (!$this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                    $data['error_message'] = "Your college and school do not match!<br>Please try again.";
                }
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->update_college($data)) {
                    $this->utility_model->show_success("Your edits have been succesfully saved.");
                    return;
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br>" .
                                                "You can't be at two colleges or schools at the same time.";
                }
            }
        }

        try {
            $user_college = $this->profile_model->get_user_college($user_college_id);
        }
        catch (CollegeNotFoundException $e) {
            show_404();
        }

        $data['user_college'] = $user_college;

        if (!isset($data['error_message'])) {  // So that we can retain the dates entered in the form.
            $data['start_year'] = $user_college['start_year'];
            $data['start_month'] = $user_college['start_month'];
            $data['start_day'] = $user_college['start_day'];

            $data['end_year'] = $user_college['end_year'];
            $data['end_month'] = $user_college['end_month'];
            $data['end_day'] = $user_college['end_day'];
        }

        $data['heading'] = "Edit College";
        $data['form_action'] = base_url("user/edit-college");
        $this->load->view("edit-college", $data);
        $this->load->view("common/footer");
    }

    public function add_programme($user_college_id=NULL)
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['user_college_id']= $this->input->post('user-college-id');
            $data['programme_id'] = $this->input->post("programme");
            $data['year_of_study'] = $this->input->post("year-of-study");

            $this->profile_model->add_programme($data);
            $this->utility_model->show_success("Your programme details have been " .
                                                "successfully saved.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "Add your programme";
        $this->load->view('common/header', $data);

        try {
            $user_college = $this->profile_model->get_user_college($user_college_id);
        }
        catch (CollegeNotFoundException $e) {
            show_404();
        }

        $data['user_college'] = $user_college;
        $data['programmes'] = $this->profile_model->get_programmes($user_college['school']['school_id']);

        $data['heading'] = "Add Programme";
        $data['form_action'] = base_url("user/add-programme");
        $this->load->view("edit-programme", $data);
        $this->load->view("common/footer");
    }

    public function edit_programme($user_programme_id=NULL)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Edit your programme";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['user_programme_id'] = $this->input->post("user-programme-id");
            $data['year_of_study'] = $this->input->post("year-of-study");

            $this->profile_model->update_programme($data);
            $this->utility_model->show_success("Your edits have been successfully saved.");
            return;
        }

        try {
            $user_programme = $this->profile_model->get_user_programme($user_programme_id);
        }
        catch (ProgrammeNotFoundException $e) {
            show_404();
        }

        $data['user_programme'] = $user_programme;
        $data['year_of_study'] = $user_programme['year_of_study'];

        $data['heading'] = "Edit Programme Details";
        $data['form_action'] = base_url("user/edit-programme");
        $this->load->view("edit-programme", $data);
        $this->load->view("common/footer");
    }

    public function add_hall()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Add hall of attachment/residence";
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
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->add_hall($data)) {
                    $this->utility_model->show_success("Your hall details have been successfully saved.");
                    return;
                }
                else {
                    $data['error_message'] = "The years you entered either conflict with one of your records.<br>" .
                                             "Either you indicated that you were in a hostel during that period, or<br>" .
                                             "The dates overlap with one of your other halls.";
                }
            }
        }

        $data['halls'] = $this->profile_model->get_halls();

        $data['heading'] = "Add Hall";
        $data['form_action'] = base_url("user/add-hall");
        $this->load->view("edit-hall", $data);
        $this->load->view("common/footer");
    }

    public function edit_hall($user_hall_id=NULL)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Edit hall of attachment/residence";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // DON'T join these two lines into 1,
            // $user_hall_id is usefull for re-displaying the form incase of any error.
            $user_hall_id = $this->input->post("user-hall-id");
            $data['user_hall_id'] = $user_hall_id;

            $data['hall_id'] = $this->input->post("hall-id");
            $data['resident'] = $this->input->post("resident");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->update_hall($data)) {
                    $this->utility_model->show_success("Your edits have been successfully saved.");
                    return;
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br>" .
                                                "You cannot be attached to/a resident of two halls at the same time.";
                }
            }
        }

        try {
            $user_hall = $this->profile_model->get_user_hall($user_hall_id);
        }
        catch (HallNotFoundException $e) {
            show_404();
        }

        $data['user_hall'] = $user_hall;
        if (!isset($data['error_message'])) {  // So that we may retain the dates entered in the form.
            $data['resident'] = $user_hall['resident'];

            $data['start_day'] = $user_hall['start_day'];
            $data['start_month'] = $user_hall['start_month'];
            $data['start_year'] = $user_hall['start_year'];

            $data['end_day'] = $user_hall['end_day'];
            $data['end_month'] = $user_hall['end_month'];
            $data['end_year'] = $user_hall['end_year'];

            $data['old_start_date'] = $user_hall['date_from'];
            $data['old_end_date'] = $user_hall['date_to'];
        }

        $data['heading'] = "Edit Hall";
        $data['form_action'] = base_url("user/edit-hall");
        $this->load->view("edit-hall", $data);
        $this->load->view("common/footer");
    }

    public function add_hostel()
    {
        $data = $this->user_model->initialize_user();
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
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->add_hostel($data)) {
                    $this->utility_model->show_success("Your hostel details have been successfully saved.");
                    return;
                }
                else {
                    $data['error_message'] = "The hostel you entered conflicts with one of your records.<br>" .
                                             "Either you indicated that you are a resident of a hall, Or<br>" .
                                             "The date overlaps with that of one of the hostels you have been to.";
                }
            }
        }

        $data['heading'] = "Add Hostel";
        $data['form_action'] = base_url("user/add-hostel");
        $data['hostels'] = $this->profile_model->get_hostels();
        $this->load->view("edit-hostel", $data);
        $this->load->view("common/footer");
    }

    public function edit_hostel($user_hostel_id=NULL)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Edit hostel";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // DON'T join these two lines into 1,
            // $user_hall_id is usefull for re-displaying the form incase of any error.
            $user_hostel_id = $this->input->post("user-hostel-id");
            $data['user_hostel_id'] = $user_hostel_id;

            $data['hostel_id'] = $this->input->post("hostel-id");

            $data['start_day'] = $this->input->post("start-day");
            $data['start_month'] = $this->input->post("start-month");
            $data['start_year'] = $this->input->post("start-year");

            $data['end_day'] = $this->input->post("end-day");
            $data['end_month'] = $this->input->post("end-month");
            $data['end_year'] = $this->input->post("end-year");
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = "Invalid dates entered! Please check the dates and try again.";
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->update_hostel($data)) {
                    $this->utility_model->show_success("Your edits have been successfully saved.");
                    return;
                }
                else {
                    $data['error_message'] = "The hostel you entered conflicts with one of your records.<br>" .
                                             "Either you indicated that you are a resident of a hall, Or<br>" .
                                             "The date overlaps with that of one of the hostels you have been to.";
                }
            }
        }

        try {
            $user_hostel = $this->profile_model->get_user_hostel($user_hostel_id);
        }
        catch (HostelNotFoundException $e) {
            show_404();
        }

        $data['user_hostel'] = $user_hostel;
        if (!isset($data['error_message'])) {  // So that we may retain the dates entered in the form.
            $data['start_day'] = $user_hostel['start_day'];
            $data['start_month'] = $user_hostel['start_month'];
            $data['start_year'] = $user_hostel['start_year'];

            $data['end_day'] = $user_hostel['end_day'];
            $data['end_month'] = $user_hostel['end_month'];
            $data['end_year'] = $user_hostel['end_year'];

            $data['old_start_date'] = $user_hostel['date_from'];
            $data['old_end_date'] = $user_hostel['date_to'];
        }

        $data['heading'] = "Edit Hostel";
        $data['form_action'] = base_url("user/edit-hostel");
        $this->load->view("edit-hostel", $data);
        $this->load->view("common/footer");
    }

    public function add_country()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Add your country of origin";
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
        $data = $this->user_model->initialize_user();
        $data['title'] = "Add your district";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data['district'] = $this->input->post("district");
            if (empty(trim($data['district']))) {
                $data['error_message'] = "Please enter the name of your district or state and try again.";
            }
            else {
                $data['districts'] = $this->profile_model->get_searched_district($data['district']);
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
