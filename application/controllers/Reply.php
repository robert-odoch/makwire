<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reply extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        session_start();
        $this->load->model("user_model");
        $this->load->model("reply_model");
    }
    
    public function like($reply_id)
    {
        $this->reply_model->like($reply_id);
    }
    
    public function likes($reply_id, $offset=0)
    {
        $user_id = $_SESSION['user_id'];
        $data['user_id'] = $user_id;
        $data['user'] = $this->user_model->get_full_name($data['user_id']);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['visitor'] = ($_SESSION['user_id'] === $user_id) ? FALSE : TRUE;
        if ($data['visitor']) {
            $data['can_add_friend'] = $this->user_model->can_add_friend($user_id);
        }
        $data['title'] = "People who liked this reply";
        $this->load->view("common/header", $data);
        
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        
        $data['reply'] = $this->reply_model->get_reply($reply_id);
        
        $limit = 10;
        $data['likes'] = $this->reply_model->get_likes($reply_id, $offset, $limit);
        $data['has_next'] = FALSE;
        if (($data['reply']['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view("show-reply-likes", $data);
        $this->load->view("common/footer");
    }
}
?>
