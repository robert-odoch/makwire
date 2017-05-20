<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();

        $this->load->model(['user_model']);
    }

    public function forgot_password()
    {

    }

    public function change_password()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Change you password';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/change-password', $data);
        $this->load->view('common/footer');
    }

    public function set_prefered_name()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Set prefered profile name';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/prefered-profile-name', $data);
        $this->load->view('common/footer');
    }

    public function change_name()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Change your name';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/change-name', $data);
        $this->load->view('common/footer');
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Delete your account';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/delete', $data);
        $this->load->view('common/footer');
    }

}
?>
