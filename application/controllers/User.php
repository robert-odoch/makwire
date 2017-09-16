<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();

        $this->load->model([
            'user_model', 'post_model', 'profile_model', 'photo_model',
            'link_model', 'utility_model'
        ]);
    }

    public function index($user_id = 0, $offset = 0)
    {
        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = format_name($data['primary_user']) . ' posts';

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        try {
            $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['suid'] = $user_id;
        $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
        $data['friendship_status'] = $this->user_model->get_friendship_status($_SESSION['user_id'], $user_id);
        $data['title'] = format_name($data['secondary_user']) . ' posts';

        $this->load->view('common/header', $data);

        // Check if there is an error from the previous attemt to post.
        if (isset($_SESSION['post_error']) && !empty($_SESSION['post_error'])) {
            $data['post_error'] = $_SESSION['post_error'];
            unset($_SESSION['post_error']);
        }

        $limit = 10;  // Maximum number of items to show.
        $data['has_next'] = FALSE;
        $num_timeline_items = $this->user_model->get_num_timeline_items($user_id);
        if (($num_timeline_items - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $timeline_items = $this->user_model->get_timeline_items($user_id, $_SESSION['user_id'], $offset, $limit);
        $data['items'] = $timeline_items;
        $data['page'] = 'timeline';
        $data['user_id'] = $user_id;
        $this->load->view('show/user', $data);
        $this->load->view('common/footer');
    }

    public function birthday($user_id = 0, $age = 0, $offset = 0)
    {
        $this->load->model('birthday_message_model');

        if ( ! $this->user_model->can_view_birthday($user_id, $age)) {
            show_404();
        }

        if ( ! $this->user_model->are_friends($_SESSION['user_id'], $user_id)) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions.";
            redirect(base_url('error'));
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['user'] = $this->user_model->get_profile_name($user_id);
        $data['title'] = format_name($data['user']) . ' birthday';
        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;

        $this->load->view('common/header', $data);

        $limit = 10;
        $num_birthday_messages = $this->birthday_message_model->get_num_birthday_messages($user_id, $age);
        $data['has_next'] = FALSE;
        if (($num_birthday_messages - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        if (isset($_SESSION['error_message'])) {
            $data['error_message'] = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }

        $data['birthday_messages'] = $this->birthday_message_model->get_birthday_messages($user_id, $age, $offset, $limit);
        $data['user_id'] = $user_id;
        $data['user_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
        $data['dob'] = $this->user_model->get_dob($user_id);
        $data['age'] = $age;
        $this->load->view('show/birthday', $data);
        $this->load->view('common/footer');
    }

    public function send_birthday_message($receiver_id = 0, $age = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions.";
            redirect(base_url('error'));
        }

        $message = trim(strip_tags($this->input->post('birthday-message')));
        if (strlen($message) == 0) {
            $_SESSION['error_message'] = "Please enter your message.";
        }
        else {
            $this->birthday_message_model->send_birthday_message($_SESSION['user_id'], $message, $receiver_id, $age);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function chat($offset = 0)
    {
        if (is_ajax_request()) {
            $data['chat_users'] = $this->user_model->get_chat_users($_SESSION['user_id'], TRUE);
            $this->load->view('common/active-users', $data);
            return;
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Chat With Friends';
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

        $this->load->view('chat-users', $data);
        $this->load->view('common/footer');
    }

    public function send_message($receiver_id = 0, $offset = 0, $refresh = FALSE)
    {
        $data = [];

        // Prevent a user from sending a message to himself.
        if ($receiver_id === $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You can't send a message to yourself.";
            redirect(base_url('error'));
        }

        // Users can only exchange messages if they are friends.
        if ( ! $this->user_model->are_friends($_SESSION['user_id'], $receiver_id)) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to send a message to this user.";
            redirect(base_url('error'));
        }

        $limit = 5;  // Maximum number of previous messages to show.
        $num_convo = $this->user_model->get_num_conversation($_SESSION['user_id'], $receiver_id);
        $data['has_prev'] = FALSE;
        if (($num_convo - $offset) > $limit) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = ($offset + $limit);
        }

        $data['sender']['profile_pic_path'] = $this->user_model->get_profile_pic_path($_SESSION['user_id']);

        $data['receiver']['user_id'] = $receiver_id;
        $data['receiver']['profile_name'] = $this->user_model->get_profile_name($receiver_id);
        $data['receiver']['profile_pic_path'] = $this->user_model->get_profile_pic_path($receiver_id);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $message = trim(strip_tags($this->input->post('message')));
            if (strlen($message) == 0) {
                $data['message_error'] = "Please enter your message.";
            }
            else {
                $message_id = $this->user_model->send_message($_SESSION['user_id'], $receiver_id, $message);
                if (is_ajax_request()) {
                    try {
                        $data['message'] = $this->user_model->get_message($message_id);
                    }
                    catch (NotFoundException $e) {
                        // Do nothing for now...
                    }
                    $html = $this->load->view('chat-message', $data, TRUE);
                    echo $html;
                    return;
                }
            }
        }
        elseif (is_ajax_request()) {
            if ($offset > 0) {  // User wants to view previous messages.
                $data['messages'] = $this->user_model->get_conversation($_SESSION['user_id'], $receiver_id, $offset, $limit);
                $html = $this->load->view('chat-messages', $data, TRUE);
                echo $html;
                return;
            }
            elseif ($refresh) {  // User wants to see if there are new messages.
                $html = '';
                $messages = $this->user_model->get_messages($_SESSION['user_id'], $offset, $limit);
                foreach ($messages as $m) {
                    // get_messages returns all new messages from friends. Filter it!
                    if ($m['sender_id'] == $receiver_id) {
                        $data['message'] = $m;
                        $html .= $this->load->view('chat-message', $data, TRUE);
                    }
                }

                echo $html;
                return;
            }
            else {  // User wants to chat with a friend.
                $data['messages'] = $this->user_model->get_conversation($_SESSION['user_id'], $receiver_id, $offset, $limit);
                $this->load->view('chat-user', $data);
                return;
            }
        }

        // No AJAX.
        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        try {
            $data['secondary_user'] = $this->user_model->get_profile_name($receiver_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['suid'] = $receiver_id;
        $data['title'] = "Send a message to {$data['secondary_user']}";
        $this->load->view('common/header', $data);

        if ($offset > 0) {  // Use wants to view older messages.
            $limit += $offset;
            $offset = 0;  // To select all new messages plus older messages.
        }
        $data['messages'] = $this->user_model->get_conversation($_SESSION['user_id'], $receiver_id, $offset, $limit);
        $this->load->view('message', $data);
        $this->load->view('common/footer');
    }

    public function post($post_id = 0, $offset = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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
        $data['comments'] = $this->post_model->get_comments($post, $offset, $limit, $_SESSION['user_id']);

        $this->load->view('show/post', $data);
        $this->load->view('common/footer');
    }

    public function photo($photo_id = 0, $offset = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = format_name($photo['author']) . ' photo';

        $this->load->view('common/header', $data);

        $data['photo'] = $photo;

        $limit = 10;  // Maximum number of comments to display.
        $data['has_next'] = FALSE;
        if (($data['photo']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['comments'] = $this->photo_model->get_comments($photo, $offset, $limit, $_SESSION['user_id']);

        $this->load->view('show/photo', $data);
        $this->load->view('common/footer');
    }

    public function video($video_id = 0, $offset = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = format_name($video['author']) . ' video';

        $this->load->view('common/header', $data);

        $data['video'] = $video;

        $limit = 10;  // Maximum number of comments to show.
        $data['has_next'] = FALSE;
        if (($data['video']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['comments'] = $this->video_model->get_comments($video, $offset, $limit, $_SESSION['user_id']);

        $this->load->view('show/video', $data);
        $this->load->view('common/footer');
    }

    public function link($link_id = 0, $offset = 0)
    {
        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = format_name($link['author']) . ' link';

        $this->load->view('common/header', $data);

        $data['link'] = $link;

        $limit = 10;  // Maximum number of comments to show.
        $data['has_next'] = FALSE;
        if (($data['link']['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['comments'] = $this->link_model->get_comments($link, $offset, $limit, $_SESSION['user_id']);

        $this->load->view('show/link', $data);
        $this->load->view('common/footer');
    }

    public function notifications($offset = 0)
    {
        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Notifications';
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
			$data['notifications'] = $this->user_model->get_notifications($_SESSION['user_id'], $offset, $limit, TRUE);
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
		    $data['notifications'] = $this->user_model->get_notifications($_SESSION['user_id'], $offset, $limit, FALSE);
		    if (($num_notifications - $offset) > $limit) {
		        $data['has_next'] = TRUE;
		        $data['next_offset'] = ($offset + $limit);
		    }
		}

        $this->load->view('show/notifications', $data);
        $this->load->view('common/footer');
    }

    public function find_friends($offset = 0)
    {
        $data = [];
        $limit = 10;  // Maximum number of suggested users/search results to show.
        $data['has_next'] = FALSE;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_SESSION['search_results'])) {
            if (isset($_SESSION['search_results'])) {
                $query = $_SESSION['query'];
            }
            else {
                $query = trim(strip_tags($this->input->post('query')));
            }

            if (strlen($query) == 0) {
                $data['error'] = "Please enter your query.";
            }
            else {
                if (empty($_SESSION['search_results'])) {
                    $_SESSION['query'] = $query;
                    $_SESSION['search_results'] = TRUE;
                }

                $data['search_results'] = $this->user_model->get_searched_user($query, $_SESSION['user_id'], $offset, $limit);
                $num_search_results = count($this->user_model->get_searched_user($query, $_SESSION['user_id'], $offset, $limit+1));
                if (($num_search_results - $offset) > $limit) {
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
            $data['suggested_users'] = $this->user_model->get_suggested_users($_SESSION['user_id'], $offset, $limit);
            if ((count($data['suggested_users']) - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Find Friends';
        $this->load->view('common/header', $data);

        $this->load->view('find-friends', $data);
        $this->load->view('common/footer');
    }

    public function friend_requests($offset = 0, $request_id = 0)
    {
        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Friend Requests';
        $this->load->view('common/header', $data);

        $limit = 10;
        $data['has_next'] = FALSE;
        $num_friend_requests = $this->user_model->get_num_friend_requests($_SESSION['user_id'], FALSE);
        if ($num_friend_requests == 0 && $request_id != 0) {
            try {
                $data['friend_request'] = $this->user_model->get_friend_request($_SESSION['user_id'], $request_id);
            } catch (NotFoundException $e) {
                $data['friend_requests'] = [];
            }
        }
        else {
            if (($num_friend_requests - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($limit + $offset);
            }
            $data['friend_requests'] = $this->user_model->get_friend_requests($_SESSION['user_id'], $offset, $limit);
        }

        $this->load->view('friend-requests', $data);
        $this->load->view('common/footer');
    }

    public function add_friend($target_id = 0)
    {
        try {
            $this->user_model->send_friend_request($_SESSION['user_id'], $target_id);
            redirect(base_url("user/{$target_id}"));
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('error'));
        }
    }

    public function accept_friend($user_id = 0)
    {
        try {
            $this->user_model->confirm_friend_request($_SESSION['user_id'], $user_id);
            redirect(base_url("user/{$user_id}"));
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('error'));
        }
    }

    public function delete_friend_request($user_id = 0)
    {
        try {
            $this->user_model->delete_friend_request($_SESSION['user_id'], $user_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
    }

    public function unfriend($friend_id = 0)
    {
        try {
            $this->user_model->unfriend_user($_SESSION['user_id'], $friend_id);
            redirect(base_url("user/{$friend_id}"));
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('error'));
        }
    }

    public function messages($offset = 0)
    {
        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Messages';
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
            $data['messages'] = $this->user_model->get_messages($_SESSION['user_id'], $offset, $limit);
            if (($data['num_new_messages'] - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }
            else {
                $num_messages = $this->user_model->get_num_messages($_SESSION['user_id'], FALSE);

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
            $num_messages = $this->user_model->get_num_messages($_SESSION['user_id'], FALSE);
            $data['messages'] = $this->user_model->get_messages($_SESSION['user_id'], $offset, $limit, FALSE);
            if (($num_messages - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }
        }

        $this->load->view('show/messages', $data);
        $this->load->view('common/footer');
    }

    public function friends($user_id = NULL, $offset = 0)
    {
        if ($user_id === NULL) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = format_name($data['primary_user']) . ' friends';

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        try {
            $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
        $data['friendship_status'] = $this->user_model->get_friendship_status($_SESSION['user_id'], $user_id);
        $data['suid'] = $user_id;
        $data['title'] = format_name($data['secondary_user']) . ' friends';

        $this->load->view('common/header', $data);

        $limit = 10;  // Maximum number of friends to show.
        $data['has_next'] = FALSE;
        $num_friends = $this->user_model->get_num_friends($user_id);
        if (($num_friends - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($limit + $offset);
        }

        $data['friends'] = $this->user_model->get_friends($user_id, $offset, $limit);
        $this->load->view('show/friends', $data);
        $this->load->view('common/footer');
    }

    public function profile($user_id = NULL)
    {
        if ($user_id === NULL) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = "Profile - {$data['primary_user']}";

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        try {
            $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
        $data['friendship_status'] = $this->user_model->get_friendship_status($_SESSION['user_id'], $user_id);
        $data['suid'] = $user_id;
        $data['title'] = "Profile - {$data['secondary_user']}";

        $this->load->view('common/header', $data);
        if ($user_id == $_SESSION['user_id']) {
            $data['profile_questions'] = $this->profile_model->get_profile_questions($_SESSION['user_id']);
        }
        $data['profile'] = $this->profile_model->get_profile($user_id);
        $this->load->view('profile', $data);
        $this->load->view('common/footer');
    }

    public function photos($user_id = NULL, $offset = 0)
    {
        if ($user_id === NULL) {
            $user_id = $_SESSION['user_id'];
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = format_name($data['primary_user']) . ' photos';

        $data['is_visitor'] = ($user_id == $_SESSION['user_id']) ? FALSE : TRUE;
        try {
            $data['secondary_user'] = $this->user_model->get_profile_name($user_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['su_profile_pic_path'] = $this->user_model->get_profile_pic_path($user_id);
        $data['friendship_status'] = $this->user_model->get_friendship_status($_SESSION['user_id'], $user_id);
        $data['suid'] = $user_id;
        $data['title'] = format_name($data['secondary_user']) . ' photos';

        $this->load->view('common/header', $data);

        $limit = 10;  // Maximum number of photos to show.
        $data['has_next'] = FALSE;
        $num_photos = $this->user_model->get_num_photos($user_id);
        if (($num_photos - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($limit + $offset);
        }

        $data['photos'] = $this->user_model->get_photos($user_id, $_SESSION['user_id'], $offset, $limit);
        $data['user_id'] = $user_id;  // Used in view more photos.
        $this->load->view('show/photos', $data);
        $this->load->view('common/footer');
    }
}
?>
