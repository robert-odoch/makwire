<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function account($page='index')
    {
        $data = $this->user_model->initialize_user();

        switch ($page) {
        case 'change-password':
            $data['title'] = 'Change password';
            break;
        case 'prefered-display-name':
            $data['title'] = 'Set prefered display name';
            break;
        case 'change-name':
            $data['title'] = 'Change name';
            break;
        case 'delete':
            $data['title'] = 'Delete account';
            break;
        default:
            $data['title'] = 'Account settings';
            break;
        }
        $this->load->view('common/header', $data);


        $this->load->view("settings/account/{$page}");
        $this->load->view('common/footer');
    }

    public function emails()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Email settings';
        $this->load->view('common/header', $data);

        $this->load->view('settings/email');
        $this->load->view('common/footer');

    }

    public function notifications()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Notification settings';
        $this->load->view('common/header', $data);

        $this->load->view('settings/notification');
        $this->load->view('common/footer');
    }

    public function blocked_users()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Blocked users';
        $this->load->view('common/header', $data);

        $this->load->view('settings/block-user');
        $this->load->view('common/footer');
    }

}
?>
