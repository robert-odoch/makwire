<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        session_start();
        $this->load->model("user_model");
        $this->load->model("comment_model");
    }
    
    public function like($comment_id)
    {
        $this->comment_model->like($comment_id);
    }
    
    public function reply($comment_id)
    {
        $reply_errors = array();
        
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (empty($this->input->post("reply"))) {
                $reply_errors['reply'] = "Reply can't be empty!";
            }
            else {
                $reply = $this->input->post("reply");
                $this->comment_model->reply($comment_id, $reply);
            }
        }
        
        $this->show_reply_form($comment_id, $reply_errors);
    }
    
    private function show_reply_form($comment_id, $reply_errors)
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
        $data['title'] = "Reply to Comment";
        $this->load->view("common/header", $data);
        
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $data['comment'] = $this->comment_model->get_comment($comment_id);
        
        $offset = 0;
        $limit = 10;
        $data['replies'] = $this->comment_model->get_replies($comment_id, $offset, $limit);
        $data['has_next'] = FALSE;
        if (($data['comment']['num_replies'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $data['reply_errors'] = $reply_errors;
        $this->load->view("reply-comment", $data);
        $this->load->view("common/footer");
    }
    
    public function likes($comment_id, $offset=0)
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
        $data['title'] = "People who liked this comment";
        $this->load->view("common/header", $data);
        
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        
        $data['comment'] = $this->comment_model->get_comment($comment_id);
        
        $limit = 10;
        $data['likes'] = $this->comment_model->get_likes($comment_id, $offset, $limit);
        $data['has_next'] = FALSE;
        if (($data['comment']['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view("show-comment-likes", $data);
        $this->load->view("common/footer");
    }
    
    public function replies($comment_id, $offset=0, $limit=null)
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
        $data['title'] = "People who replied to this comment";
        $this->load->view("common/header", $data);
        
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        
        $data['comment'] = $this->comment_model->get_comment($comment_id);
        
        if (! $limit) {
            $limit = 10;
        }
        
        $data['replies'] = $this->comment_model->get_replies($comment_id, $offset, $limit);
        $data['has_next'] = FALSE;
        if (($data['comment']['num_replies'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view("show-comment-replies", $data);
        $this->load->view("common/footer");
    }
}
?>
