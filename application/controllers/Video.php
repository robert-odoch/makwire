<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();

        $this->load->model(['user_model', 'video_model', 'utility_model']);
    }

    public function new()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $youtube_video_url = $this->input->post('video_url');
            if (strlen($youtube_video_url) == 0) {
                $error_message = 'Please enter a URL.';
            } elseif ( ! preg_match('/^(http:\/\/|https:\/\/)?(www.)?((youtube.com|youtu.be)\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/', $youtube_video_url)) {
                $error_message = 'Please enter a valid YouTube video URL.';
            }

            if (empty($error_message)) {
                // Do something with valid url supplied.
                $pattern = '(embed\/|watch\?v=|\&v=|\?v=)';
                $replacement = 'embed/';
                $youtube_video_url = preg_replace($pattern, $replacement, $youtube_video_url);

                $video_id = $this->video_model->publish($youtube_video_url, $_SESSION['user_id']);
                if (is_ajax_request()) {
                    $data['video'] = $this->video_model->get_video($video_id, $_SESSION['user_id']);
                    $result['item'] = $this->load->view('common/video', $data, TRUE);
                    echo json_encode($result);

                    return;
                }

                redirect(base_url("user/{$_SESSION['user_id']}"));
            } else {
                $data['error_message'] = $error_message;
                $data['video_url'] = $youtube_video_url;
            }
        }

        if (is_ajax_request()) {
            $html = $this->load->view('forms/new-video', [], TRUE);
            echo $html;
            return;
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add YouTube video';
        $this->load->view('common/header', $data);

        $this->load->view('add/video', $data);
        $this->load->view('common/footer');
    }

    public function edit($video_id = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($video['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to edit this video.";
            redirect(base_url('error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_description = $this->input->post('description');
            $this->video_model->update_description($video_id, $new_description);
            redirect(base_url("user/video/{$video_id}"));
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Edit video - Makwire';
        $this->load->view('common/header', $data);

        $data['item'] = 'video';
        $data['video'] = $video;
        $data['description'] = $video['description'];
        $data['form_action'] = base_url("video/edit/{$video_id}");
        $data['cancel_url'] = base_url("user/video/{$video_id}");
        $this->load->view('edit/description', $data);
        $this->load->view('common/footer');
    }

    public function delete($video_id = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->video_model->delete_video($video, $_SESSION['user_id']);
                $_SESSION['message'] = 'Your video has been successfully deleted.';
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
        $data['title'] = 'Delete video - Makwire';
        $this->load->view('common/header', $data);

        $video_data = [
            'item' => 'video',
            'video' => $video,
            'item_owner_id' => $video['user_id'],
            'form_action' => base_url("video/delete/{$video['video_id']}"),
            'cancel_url' => base_url("user/video/{$video['video_id']}")
        ];

        $data = array_merge($data, $video_data);
        $this->load->view('delete-item', $data);
        $this->load->view('common/footer');
    }

    public function add_description($video_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $description = trim(strip_tags($this->input->post('description')));
            if (strlen($description) == 0) {
                $data['error_message'] = 'Please enter a description for this video.';
            }
            else {
                $this->video_model->add_description($description, $video_id);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($video['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions.';
            redirect(base_url('error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Say something about this video';
        $this->load->view('common/header', $data);

        $data['video'] = $video;
        $data['item'] = 'video';
        $data['form_action'] = base_url("video/add-description/{$video_id}");
        $this->load->view('add/description', $data);
        $this->load->view('common/footer');
    }

    public function like($video_id = 0)
    {
        if (is_ajax_request()) {
            try {
                $like_id = $this->video_model->like($video_id, $_SESSION['user_id']);
                $num_likes = $this->video_model->get_num_likes($video_id);
                $num_likes .= ($num_likes == 1) ? ' like' : ' likes';
                $result['numLikes'] = $num_likes;

                if ( ! empty($like_id)) {
                    // Check if request has been sent from the likes page.
                    $referer = $_SERVER['HTTP_REFERER'];
                    $base_url = str_replace('/', '\/', base_url());
                    $pattern = "/^{$base_url}[a-z-]+\/likes\/[0-9]+(\/[0-9]+)?/";
                    if (preg_match($pattern, $referer)) {
                        $data['like'] = $this->activity_model->getLike($like_id);
                        $result['like'] = $this->load->view('common/like', $data, TRUE);
                    }
                }

                echo json_encode($result);
            }
            catch (NotFoundException $e) {
            }
            catch (IllegalAccessException $e) {
            }

            return;
        }

        try {
            $this->video_model->like($video_id, $_SESSION['user_id']);
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

    public function comment($video_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (strlen($comment) == 0) {
                $data['comment_error'] = 'Please enter your comment.';
            }
            else {
                $this->video_model->comment($video_id, $comment, $_SESSION['user_id']);
                redirect(base_url("video/comments/{$video_id}"));
            }
        }

        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            if (is_ajax_request()) {
                $result['error'] = "Sorry, we couldn't find the video.";
                echo json_encode($result);
                return;
            }

            // Normal request.
            show_404();
        }

        if ( ! $this->user_model->are_friends($_SESSION['user_id'], $video['user_id'])) {
            $message = "You don't have the proper permissions to comment on this video.";

            if (is_ajax_request()) {
                $result['error'] = $message;
                echo json_encode($result);
                return;
            }

            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $message;
            redirect(base_url('error'));
        }

        // Loading comment from using AJAX.
        if (is_ajax_request()) {
            $data['video'] = $video;
            $data['object'] = 'video';
            $result['form'] = $this->load->view('forms/comment', $data, TRUE);

            echo json_encode($result);
            return;
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Comment on this video';
        $this->load->view('common/header', $data);

        $data['page'] = 'comment';
        $data['video'] = $video;
        $data['object'] = 'video';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($video_id = 0)
    {
        try {
            $this->video_model->share($video_id, $_SESSION['user_id']);
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

    public function likes($video_id = 0, $offset = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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

        $data['comments'] = $this->video_model->get_comments($video, $offset, $limit, $_SESSION['user_id']);
        $data['object'] = 'video';
        $data['video'] = $video;
        $this->load->view('show/comments', $data);
        $this->load->view('common/footer');
    }

    public function shares($video_id = 0, $offset = 0)
    {
        try {
            $video = $this->video_model->get_video($video_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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
