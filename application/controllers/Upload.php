<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->helper(['form', 'url']);
        $this->load->library('image_lib');
        $this->load->model(['user_model', 'upload_model', 'photo_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();

        // Set up and load the upload library.
        $config['upload_path'] = 'uploads';
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['file_ext_tolower'] = TRUE;
        $config['max_size'] = 1024;
        $this->load->library('upload', $config);
    }

    public function profile_picture()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Change profile picture";
        $this->load->view("common/header", $data);

        $data['heading'] = "Change profile picture";
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (!$this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
                $this->load->view('upload-image', $data);
            }
            else {

                // Upload the file.
                $upload_data = $this->upload->data();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = "";
                $config['maintain_ratio'] = TRUE;

                // Create a 60x60 thumbnail for profile picture.
                $config['new_image'] = "{$upload_data['file_path']}thumbnails";
                $config['width'] = 60;
                $config['height'] = 60;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Create a 480x480 thumbnail for photo.
                $config['new_image'] = $upload_data['file_path'];
                $config['width'] = 480;
                $config['height'] = 300;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Record it in the database.
                $this->user_model->set_profile_picture($upload_data);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        $data['form_action'] = base_url('upload/profile-picture');
        $this->load->view("upload-image", $data);
        $this->load->view("common/footer");
    }

    public function photo()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Add new photo";
        $this->load->view("common/header", $data);

        $data['heading'] = "Add new photo";
        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            // Upload the file.
            if (!$this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
                $this->load->view('upload-image', $data);
            }
            else {
                $upload_data = $this->upload->data();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = "";
                $config['maintain_ratio'] = TRUE;

                // Create a 480x480 thumbnail for photo.
                $config['new_image'] = $upload_data['file_path'];
                $config['width'] = 480;
                $config['height'] = 300;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Record it in the database.
                $photo_id = $this->photo_model->publish($upload_data);
                redirect(base_url("photo/add-description/{$photo_id}"));
            }
        }

        $data['form_action'] = base_url('upload/photo');
        $this->load->view("upload-image", $data);
        $this->load->view("common/footer");
    }
}
?>
