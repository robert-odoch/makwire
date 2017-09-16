<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();
        $this->load->model(['post_model', 'user_model', 'utility_model']);
    }

    public function new()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = trim(strip_tags($this->input->post('post')));
            if (strlen($post) == 0) {
                $_SESSION['post_error'] = "Post can't be empty!";  // Needed by user/index method.
                redirect(base_url('post/new'));
            }

            $this->post_model->publish($post, $_SESSION['user_id']);
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }

        if (is_ajax_request()) {
            $html = $this->load->view('forms/new-post', [], TRUE);
            echo $html;
            return;
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Write a status post';
        $this->load->view('common/header', $data);

        if (isset($_SESSION['post_error']) && ! empty($_SESSION['post_error'])) {
            $data['post_error'] = $_SESSION['post_error'];
            unset($_SESSION['post_error']);
        }

        $this->load->view('add/post');
        $this->load->view('common/footer');
    }

    public function edit($post_id = 0)
    {
        $data = [];

        try {
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($post['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to edit this post.";
            redirect(base_url('error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_post = $this->input->post('post');
            if (strlen($new_post) == 0) {
                $data['error_message'] = "Post can't be empty.";
            }
            else {
                $this->post_model->update_post($post_id, $new_post);
                redirect(base_url("user/post/{$post_id}"));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit post - Makwire';
        $this->load->view('common/header', $data);

        $data['post'] = $post;
        $this->load->view('edit/post', $data);
        $this->load->view('common/footer');
    }

    public function delete($post_id = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->post_model->delete_post($post, $_SESSION['user_id']);
                $_SESSION['message'] = 'Your post has been successfully deleted.';
                redirect(base_url('success'));
            }
            catch (IllegalAccessException $e) {
                $_SESSION['title'] = 'Permission Denied!';
                $_SESSION['heading'] = 'Permission Denied';
                $_SESSION['message'] = $e->getMessage();
                redirect(base_url('error'));
            }
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Delete post - Makwire';
        $this->load->view('common/header', $data);

        $post_data = [
            'item' => 'post',
            'post' => $post,
            'item_owner_id' => $post['user_id'],
            'form_action' => base_url("post/delete/{$post['post_id']}"),
            'cancel_url' => base_url("user/post/{$post['post_id']}")
        ];

        $data = array_merge($data, $post_data);
        $this->load->view('delete-item', $data);
        $this->load->view('common/footer');
    }

    public function like($post_id = 0)
    {
        try {
            $this->post_model->like($post_id, $_SESSION['user_id']);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('error'));
        }
    }

    public function comment($post_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (!$comment) {
                $data['comment_error'] = "Comment can't be empty";
            }
            else {
                $this->post_model->comment($post_id, $comment, $_SESSION['user_id']);
                redirect(base_url("post/comments/{$post_id}"));
            }
        }

        try {
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ( ! $this->user_model->are_friends($_SESSION['user_id'], $post['user_id'])) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to comment on this post.";
            redirect(base_url('error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Comment on this post';
        $this->load->view('common/header', $data);

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
            $this->post_model->share($post_id, $_SESSION['user_id']);
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('error'));
        }
    }

    public function likes($post_id = 0, $offset = 0)
    {
        try {
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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

        $data['comments'] = $this->post_model->get_comments($post, $offset, $limit, $_SESSION['user_id']);
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
            $post = $this->post_model->get_post($post_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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
