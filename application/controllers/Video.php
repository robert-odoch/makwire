<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'video_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function new()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add YouTube video';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $youtube_video_url = $this->input->post('video_url');
            if (strlen($youtube_video_url) == 0) {
                $error_message = 'Please enter a URL.';
            } elseif (!preg_match('/^(http:\/\/|https:\/\/)?(www.)?((youtube.com|youtu.be)\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/', $youtube_video_url)) {
                $error_message = 'Please enter a valid YouTube video URL.';
            }

            if (empty($error_message)) {
                // Do something with valid url supplied.
                $pattern = '(embed\/|watch\?v=|\&v=|\?v=)';
                $replacement = 'embed/';
                $youtube_video_url = preg_replace($pattern, $replacement, $youtube_video_url);

                $this->video_model->publish($youtube_video_url);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            } else {
                $data['error_message'] = $error_message;
                $data['video_url'] = $youtube_video_url;
            }
        }

        $this->load->view('add-video', $data);
        $this->load->view('common/footer');
    }

    public function add_description($video_id = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($video['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions.';
            redirect(base_url('user/error'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Say something about this video';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = trim(strip_tags($this->input->post('description')));
            if (strlen($description) == 0) {
                $data['error_message'] = 'Please enter a description for this video.';
            }
            else {
                $this->video_model->add_description($description, $video_id);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        $data['video'] = $video;
        $data['item'] = 'video';
        $data['form_action'] = base_url("video/add-description/{$video_id}");
        $this->load->view('add-description', $data);
        $this->load->view('common/footer');
    }

    public function like($video_id = 0)
    {
        try {
            $this->video_model->like($video_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions to like this video.';
            redirect(base_url('user/error'));
        }
    }

    public function comment($video_id = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if (!$this->user_model->are_friends($video['user_id'])) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions to comment on this video.';
            redirect(base_url('user/error'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comment on this video';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (!$comment) {
                $data['comment_error'] = 'Please enter a comment.';
            }
            else {
                $this->video_model->comment($video_id, $comment);
                $this->comments($video_id);
                return;
            }
        }

        $data['video'] = $video;
        $data['object'] = 'video';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($video_id = 0)
    {
        try {
            $this->video_model->share($video_id);
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions to share this video.';
            redirect(base_url('user/error'));
        }
    }

    public function likes($video_id = 0, $offset = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who liked this video';
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
        if (($video['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->video_model->get_likes($video, $offset, $limit);

        $data['object'] = 'video';
        $data['video'] = $video;
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }

    public function comments($video_id = 0, $offset = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comments on this video';
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
        if (($video['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['comments'] = $this->video_model->get_comments($video, $offset, $limit);
        $data['object'] = 'video';
        $data['video'] = $video;
        $this->load->view('show/comments', $data);
        $this->load->view('common/footer');
    }

    public function shares($video_id = 0, $offset = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who shared this video';
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
        if (($video['num_shares'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['shares'] = $this->video_model->get_shares($video, $offset, $limit);

        $data['object'] = 'video';
        $data['video'] = $video;
        $this->load->view('show/shares', $data);
        $this->load->view('common/footer');
    }
}
?>
