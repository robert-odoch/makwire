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

        $this->load->helper(array('form', 'url'));

        $this->load->model('user_model');
        $this->load->model('upload_model');

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();

        // Set up and load the upload library.
        $config['upload_path'] = '../uploads';
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['encrypt_name'] = TRUE;
        $config['file_ext_tolower'] = TRUE;
        $config['max_size'] = 2048;
        $config['max_width'] = 1366;
        $config['max_height'] = 768;

        $this->load->library('upload', $config);
    }

    public function profile_picture()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Upload Profile Picture";
        $this->load->view("common/header", $data);

        $data['heading'] = "Upload Profile Picture";
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (!$this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
                $this->load->view('upload-image', $data);
            }
            else {
                // Record it in the database.
                $upload_data = $this->upload->data();
                $this->upload_model->set_profile_picture($upload_data);
                redirect(base_url("user/index/{$_SESSION['user_id']}"));
            }
        }
        else {
            $this->load->view("upload-image", $data);
        }

        $this->load->view("common/footer");
    }
}
?>
