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

        $this->load->model(['user_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function new()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add YouTube video';
        $this->load->view('common/header', $data);

        $this->load->view('add-video', $data);
        $this->load->view('common/footer');
    }
}
?>
