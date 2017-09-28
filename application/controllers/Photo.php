<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Photo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();

        // Set up and load the upload library.
        $config['upload_path'] = 'uploads';
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['file_ext_tolower'] = TRUE;
        $config['max_size'] = 1024;
        $this->load->library('upload', $config);

        $this->load->library('image_lib');
        $this->load->helper(['form']);
        $this->load->model(['photo_model', 'user_model', 'utility_model']);
    }

    public function new()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Upload the file.
            if ( ! $this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
            }
            else {
                $upload_data = $this->upload->data();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = "";
                $config['maintain_ratio'] = TRUE;

                // Create a 480x300 thumbnail for photo.
                $config['new_image'] = $upload_data['file_path'];
                $config['width'] = 480;
                $config['height'] = 300;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Record it in the database.
                $this->photo_model->publish($upload_data, $_SESSION['user_id']);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        if (is_ajax_request()) {
            $html = $this->load->view('forms/new-photo', [], TRUE);
            echo $html;
            return;
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add new photo';
        $this->load->view('common/header', $data);

        $this->load->view('add/photo', $data);
        $this->load->view('common/footer');
    }

    public function edit($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($photo['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to edit this photo.";
            redirect(base_url('error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_description = $this->input->post('description');
            $this->photo_model->update_description($photo_id, $new_description);
            redirect(base_url("user/photo/{$photo_id}"));
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Edit photo - Makwire';
        $this->load->view('common/header', $data);

        $data['item'] = 'photo';
        $data['photo'] = $photo;
        $data['description'] = $photo['description'];
        $data['form_action'] = base_url("photo/edit/{$photo_id}");
        $data['cancel_url'] = base_url("user/photo/{$photo_id}");
        $this->load->view('edit/description', $data);
        $this->load->view('common/footer');
    }

    public function delete($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->photo_model->delete_photo($photo, $_SESSION['user_id']);
                $_SESSION['message'] = 'Your photo has been successfully deleted.';
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
        $data['title'] = 'Delete photo - Makwire';
        $this->load->view('common/header', $data);

        $photo_data = [
            'item' => 'photo',
            'photo' => $photo,
            'item_owner_id' => $photo['user_id'],
            'form_action' => base_url("photo/delete/{$photo['photo_id']}"),
            'cancel_url' => base_url("user/photo/{$photo['photo_id']}")
        ];

        $data = array_merge($data, $photo_data);
        $this->load->view('delete-item', $data);
        $this->load->view('common/footer');
    }

    public function add_description($photo_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $description = trim(strip_tags($this->input->post('description')));
            if (strlen($description) == 0) {
                $data['error_message'] = "Please enter a description for this photo.";
            }
            else {
                $this->photo_model->add_description($description, $photo_id);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($photo['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions.';
            redirect(base_url('error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Say something about this photo';
        $this->load->view('common/header', $data);

        $data['item'] = 'photo';
        $data['photo'] = $photo;
        $data['form_action'] = base_url("photo/add-description/{$photo_id}");
        $this->load->view('add/description', $data);
        $this->load->view('common/footer');
    }

    public function like($photo_id = 0)
    {
        if (is_ajax_request()) {
            try {
                $num_likes = $this->photo_model->like($photo_id, $_SESSION['user_id']);
                $num_likes .= ($num_likes == 1) ? ' like' : ' likes';
                echo $num_likes;
            }
            catch (NotFoundException $e) {
            }
            catch (IllegalAccessException $e) {
            }

            return;
        }

        try {
            $this->photo_model->like($photo_id, $_SESSION['user_id']);
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

    public function comment($photo_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (strlen($comment) == 0) {
                $data['comment_error'] = "Please enter your comment.";
            }
            else {
                $this->photo_model->comment($photo_id, $comment, $_SESSION['user_id']);
                redirect(base_url("photo/comments/{$photo_id}"));
            }
        }

        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ( ! $this->user_model->are_friends($_SESSION['user_id'], $photo['user_id'])) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to comment on this photo.";
            redirect(base_url('error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Comment on this photo';
        $this->load->view('common/header', $data);

        $data['photo'] = $photo;
        $data['object'] = 'photo';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($photo_id = 0)
    {
        try {
            $this->photo_model->share($photo_id, $_SESSION['user_id']);
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

    public function likes($photo_id = 0, $offset = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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

        $data['comments'] = $this->photo_model->get_comments($photo, $offset, $limit, $_SESSION['user_id']);
        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view('show/comments', $data);
        $this->load->view('common/footer');
    }

    public function shares($photo_id = 0, $offset = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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

    public function options($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'More options for this photo';
        $this->load->view('common/header', $data);

        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view('photo-options', $data);
        $this->load->view('common/footer');
    }

    public function make_profile_picture($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($photo['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied';
            $_SESSION['heading'] = 'Permission Denied!';
            $_SESSION['message'] = "You don't have the proper permissions to use
                                    this photo as your profile picture.";
            redirect(base_url('error'));
        }

        // Create a profile pic thumbnail if it doesn't already exist.
        $last_slash = strrpos($photo['full_path'], '/');
        $photo_directory = substr($photo['full_path'], 0, $last_slash);
        $photo_name = substr($photo['full_path'], $last_slash+1);
        $filename = "{$photo_directory}/small/{$photo_name}";

        if ( ! file_exists($filename)) {
            $this->load->library('image_lib');
            $config['image_library'] = 'gd2';
            $config['source_image'] = $photo['full_path'];
            $config['create_thumb'] = TRUE;
            $config['thumb_marker'] = "";
            $config['maintain_ratio'] = TRUE;
            $config['new_image'] = "{$photo_directory}/small";
            $config['width'] = 60;
            $config['height'] = 60;
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
        }

        // Record a new instance of this photo in the database.
        $photo_id = $this->photo_model->add_photo($photo, $_SESSION['user_id']);

        // Make photo profile picture.
        $this->load->model('profile_model');
        $this->profile_model->set_profile_picture($photo_id, $_SESSION['user_id']);
        $_SESSION['title'] = 'You profile picture has been changed';
        $_SESSION['message'] = 'Your profile picture has been successfully changed.';
        redirect(base_url('success'));
    }

    public function download($photo_id = 0)
    {
        try {
            $photo = $this->photo_model->get_photo($photo_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($photo['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied';
            $_SESSION['heading'] = 'Permission Denied!';
            $_SESSION['message'] = "You dont have the proper permissions to download
                                    this photo.";
            redirect(base_url('error'));
        }

        $this->load->helper('download');
        force_download($photo['full_path'], NULL);
        redirect(base_url("photo/options/{$photo_id}"));
    }
}
?>
