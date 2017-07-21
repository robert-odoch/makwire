<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Photo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        // Set up and load the upload library.
        $config['upload_path'] = 'uploads';
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['file_ext_tolower'] = TRUE;
        $config['max_size'] = 1024;
        $this->load->library('upload', $config);

        $this->load->library('image_lib');
        $this->load->helper(['form']);
        $this->load->model(['photo_model', 'user_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function new()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add new photo';
        $this->load->view('common/header', $data);

        $data['heading'] = 'Add new photo';
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            // Upload the file.
            if (!$this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
            }
            else {
                $upload_data = $this->upload->data();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = "";
                $config['maintain_ratio'] = TRUE;

                // Create a 150x100 thumbnail for photo.
                $config['new_image'] = "{$upload_data['file_path']}medium";
                $config['width'] = 150;
                $config['height'] = 100;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Create a 480x300 thumbnail for photo.
                $config['new_image'] = $upload_data['file_path'];
                $config['width'] = 480;
                $config['height'] = 300;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Record it in the database.
                $this->photo_model->publish($upload_data);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        $data['form_action'] = base_url('photo/new');
        $this->load->view('upload-image', $data);
        $this->load->view('common/footer');
    }

    public function add_description($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($photo['user_id'] != $_SESSION['user_id']) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions."
            );
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Say something about this photo';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $description = trim(strip_tags($this->input->post('description')));
            if (strlen($description) == 0) {
                $data['error_message'] = "Please enter a description for this photo.";
            }
            else {
                $this->photo_model->add_description($description, $photo_id);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        $data['photo'] = $photo;
        $data['item'] = 'photo';
        $data['form_action'] = base_url("photo/add-description/{$photo_id}");
        $this->load->view('add-description', $data);
        $this->load->view('common/footer');
    }

    public function like($photo_id = 0)
    {
        try {
            $this->photo_model->like($photo_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to like this photo."
            );
        }
    }

    public function comment($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if (!$this->user_model->are_friends($photo['user_id'])) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to comment on this photo."
            );
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comment on this photo';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (!$comment) {
                $data['comment_error'] = "Comment can't be empty";
            }
            else {
                $this->photo_model->comment($photo_id, $comment);
                $this->comments($photo_id);
                return;
            }
        }

        $data['photo'] = $photo;
        $data['object'] = 'photo';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($photo_id = 0)
    {
        try {
            $this->photo_model->share($photo_id);
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to share this photo."
            );
        }
    }

    public function likes($photo_id = 0, $offset = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who liked this photo';
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
        if (($photo['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->photo_model->get_likes($photo, $offset, $limit);

        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }

    public function comments($photo_id = 0, $offset = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comments on this photo';
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
        if (($photo['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['comments'] = $this->photo_model->get_comments($photo, $offset, $limit);
        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view('show/comments', $data);
        $this->load->view('common/footer');
    }

    public function shares($photo_id = 0, $offset = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who shared this photo';
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
        if (($photo['num_shares'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['shares'] = $this->photo_model->get_shares($photo, $offset, $limit);

        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view('show/shares', $data);
        $this->load->view('common/footer');
    }
}
?>
