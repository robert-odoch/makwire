<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reply extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model("user_model");
        $this->load->model("reply_model");

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
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

    public function like($reply_id, $comment_id)
    {
        $this->reply_model->like($reply_id);
        redirect(base_url("comment/replies/{$comment_id}"));
    }

    public function likes($reply_id, $offset=0)
    {
        $data = $this->initialize_user();
        $data['title'] = "People who liked this reply";
        $this->load->view("common/header", $data);

        $data['reply'] = $this->reply_model->get_reply($reply_id);

        $limit = 10;
        $data['has_next'] = FALSE;
        if (($data['reply']['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['likes'] = $this->reply_model->get_likes($reply_id, $offset, $limit);
        $this->load->view("show-reply-likes", $data);
        $this->load->view("common/footer");
    }
}
?>
