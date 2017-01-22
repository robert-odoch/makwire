<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->load->model('post_model');
        $this->load->model('user_model');
    }

    public function like($post_id)
    {
        $this->post_model->like($post_id);
        redirect(base_url("user/post/{$post_id}"));
        exit();
    }

    public function comment($post_id)
    {
        $comment_errors = array();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($this->input->post('comment'))) {
                $comment_errors['comment'] = "Comment can't be empty";
            }
            else {
                $comment = $this->input->post('comment');
                $this->post_model->comment($post_id, $comment);
            }
        }

        $this->show_comment_form($post_id, $comment_errors);
    }
    public function show_comment_form($post_id, $comment_errors)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['title'] = 'Comment on this post';
        $this->load->view('common/header', $data);

        $data['comment_errors'] = $comment_errors;
        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $post = $this->post_model->get_post($post_id);
        $short_post = $this->post_model->get_short_post($post['post'], 540);
        $post['post'] = $short_post['body'];
        $post['has_more'] = $short_post['has_more'];
        $data['post'] = $post;
        $offset = 0;
        $limit = 10;
        $data['comments'] = $this->post_model->get_comments($post_id, $offset, $limit);
        $data['has_next'] = FALSE;
        $num_comments = $this->post_model->get_num_comments($post_id);
        if ($num_comments > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($post_id, $audience="timeline", $audience_id=null)
    {
        if ($audience_id === null) {
            $audience_id = $_SESSION['user_id'];
        }
        $this->post_model->share($post_id, $audience, $audience_id);
        redirect(base_url("user/index/{$_SESSION['user_id']}"));
    }

    public function likes($post_id, $offset=0)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['title'] = "People who liked this post";
        $this->load->view("common/header", $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);

        $post = $this->post_model->get_post($post_id);
        $short_post = $this->post_model->get_short_post($post['post'], 540);
        $post['post'] = $short_post['body'];
        $post['has_more'] = $short_post['has_more'];
        $data['post'] = $post;

        $limit = 10;
        $data['likes'] = $this->post_model->get_likes($post_id, $offset, $limit);
        $data['has_next'] = FALSE;
        $num_likes = $this->post_model->get_num_likes($post_id);
        if (($num_likes - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view("show-likes", $data);
        $this->load->view("common/footer");
    }
    public function comments($post_id, $offset=0)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['title'] = 'Comments on this post';
        $this->load->view("common/header", $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $post = $this->post_model->get_post($post_id);
        $short_post = $this->post_model->get_short_post($post['post'], 540);
        $post['post'] = $short_post['body'];
        $post['has_more'] = $short_post['has_more'];
        $data['post'] = $post;
        $limit = 10;
        $data['comments'] = $this->post_model->get_comments($post_id, $offset, $limit);
        $data['has_next'] = FALSE;
        $num_comments = $this->post_model->get_num_comments($post_id);
        if (($num_comments - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view("show-comments", $data);
        $this->load->view("common/footer");
    }
    public function shares($post_id, $offset=0)
    {
        $data['primary_user'] = $this->user_model->get_full_name($_SESSION['user_id']);
        $data['num_new_messages'] = $this->user_model->get_num_messages(TRUE);
        $data['num_active_friends'] = $this->user_model->get_num_chat_users(TRUE);
        $data['num_new_notifs'] = $this->user_model->get_num_notifs(TRUE);
        $data['num_friend_requests'] = $this->user_model->get_num_friend_requests();
        $data['title'] = "People who shared this post";
        $this->load->view("common/header", $data);

        $data['suggested_users'] = $this->user_model->get_suggested_users(0, 4, TRUE);
        $data['chat_users'] = $this->user_model->get_chat_users(TRUE);
        $post = $this->post_model->get_post($post_id);
        $short_post = $this->post_model->get_short_post($post['post'], 540);
        $post['post'] = $short_post['body'];
        $post['has_more'] = $short_post['has_more'];
        $data['post'] = $post;

        $limit = 10;
        $data['shares'] = $this->post_model->get_shares($post_id, $offset, $limit);
        $data['has_next'] = FALSE;
        $num_shares = $this->post_model->get_num_shares($post_id);
        if (($num_shares - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }
        $this->load->view("show-shares", $data);
        $this->load->view("common/footer");
    }
}
?>
