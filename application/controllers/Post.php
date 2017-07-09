<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['post_model', 'user_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function new()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions."
            );
            return;
        }

        $post = trim(strip_tags($this->input->post('post')));
        if (!$post) {
            $_SESSION['post_error'] = "Post can't be empty!";  // Used and unset by index() method.
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }

        $this->post_model->post($post, 'timeline', $_SESSION['user_id']);
        redirect(base_url("user/{$_SESSION['user_id']}"));
    }

    public function like($post_id = 0)
    {
        try {
            $this->post_model->like($post_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to like this post."
            );
        }
    }

    public function comment($post_id = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if (!$this->user_model->are_friends($post['user_id'])) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to comment on this post."
            );
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comment on this post';
        $this->load->view('common/header', $data);

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
        $post['post'] = character_limiter(
            $post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>"
        );
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($post_id = 0)
    {
        try {
            $this->post_model->share($post_id);
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to share this post."
            );
        }
    }

    public function likes($post_id = 0, $offset = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who liked this post';
        $this->load->view('common/header', $data);

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
        $data['likes'] = $this->post_model->get_likes($post, $offset, $limit);

        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter(
            $post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>"
        );
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }

    public function comments($post_id = 0, $offset = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comments on this post';
        $this->load->view('common/header', $data);

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
        if (($post['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['comments'] = $this->post_model->get_comments($post, $offset, $limit);
        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter(
            $post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>"
        );
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view('show/comments', $data);
        $this->load->view('common/footer');
    }

    public function shares($post_id = 0, $offset = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who shared this post';
        $this->load->view('common/header', $data);

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
        if (($post['num_shares'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['shares'] = $this->post_model->get_shares($post, $offset, $limit);

        $post_url = base_url("user/post/{$post_id}");
        $post['post'] = character_limiter(
            $post['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>"
        );
        $data['post'] = $post;

        $data['object'] = 'post';
        $this->load->view('show/shares', $data);
        $this->load->view('common/footer');
    }
}
?>
