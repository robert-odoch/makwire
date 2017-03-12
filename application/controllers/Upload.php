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
        $this->load->model(['user_model', 'upload_model']);

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
        $data['title'] = "Upload Profile Picture";
        $this->load->view("common/header", $data);

        $data['heading'] = "Upload Profile Picture";
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (!$this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
                $this->load->view('upload-image', $data);
            }
            else {

                // Upload the file.
                $upload_data = $this->upload->data();

                // Create a thumbnail.
                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['new_image'] = "{$upload_data['file_path']}thumbnails";
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = "";
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 60;
                $config['height'] = 60;

                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    print $this->image_lib->display_errors();
                }
                else {
                    print "thumbnail created.";
                }

                // Record it in the database.
                $this->upload_model->set_profile_picture($upload_data);
                redirect(base_url("/user/{$_SESSION['user_id']}"));
            }
        }
        else {
            $this->load->view("upload-image", $data);
        }

        $this->load->view("common/footer");
    }
}
?>
