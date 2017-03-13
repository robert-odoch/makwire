<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model(['post_model', 'user_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function like($post_id)
    {
        if (!$this->post_model->like($post_id)) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to like this post.");
            return;
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function comment($post_id)
    {
        $post = $this->post_model->get_post($post_id);
        if (!$post ||
            !$this->user_model->are_friends($post['user_id'])) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to comment on this post.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comment on this post';
        $this->load->view("common/header", $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (!$comment) {
                $data['comment_error'] = "Comment can't be empty";
            }
            else {
                $this->post_model->comment($post_id, $comment);
                $this->comments($post_id);
                return;
            }
        }

        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter($post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($post_id)
    {
        $share = $this->post_model->share($post_id);
        if (!$share) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to share this post.");
            return;
        }

        redirect(base_url("user/{$_SESSION['user_id']}"));
    }

    public function likes($post_id, $offset=0)
    {
        $post = $this->post_model->get_post($post_id);
        if (!$post) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to view this post.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who liked this post";
        $this->load->view("common/header", $data);

        // Maximum number of likes to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        if (($post['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->post_model->get_likes($post_id, $offset, $limit);

        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter($post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view("show-likes", $data);
        $this->load->view("common/footer");
    }

    public function comments($post_id, $offset=0)
    {
        $post = $this->post_model->get_post($post_id);
        if (!$post) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to view this post.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comments on this post';
        $this->load->view("common/header", $data);

        // Maximum number of comments to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        $num_comments = $this->post_model->get_num_comments($post_id);
        if (($num_comments - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['comments'] = $this->post_model->get_comments($post_id, $offset, $limit);
        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter($post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");;
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view("show-comments", $data);
        $this->load->view("common/footer");
    }

    public function shares($post_id, $offset=0)
    {
        $post = $this->post_model->get_post($post_id);
        if (!$post) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to view this post.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who shared this post";
        $this->load->view("common/header", $data);

        // Maximum number of shares to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        $num_shares = $this->post_model->get_num_shares($post_id);
        if (($num_shares - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['shares'] = $this->post_model->get_shares($post_id, $offset, $limit);

        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter($post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view("show-shares", $data);
        $this->load->view("common/footer");
    }
}
?>
