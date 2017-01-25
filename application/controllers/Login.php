<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        $this->load->model('login_model');
    }

    public function index()
    {
        if (isset($_SESSION['user_id'])) {  // Already logged in user.
            redirect(base_url("user/index/{$_SESSION['user_id']}"));
        }

        $data['title'] = 'Log in to your account';
        $this->load->view("common/header", $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login_errors = array();
            if (empty($this->input->post('username'))) {
                $data['login_errors']['username'] = 'Please enter a username!';
            }
            else {
                $username = $this->input->post('username');
            }

            if (empty($this->input->post('password'))) {
                $data['login_errors']['password'] = 'Please enter a password!';
            }
            else {
                $password = $this->input->post('password');
            }

            if ( ! $login_errors) {
                if ($this->login_model->user_exists($username, $password)) {
                    redirect(base_url("user/index/{$_SESSION['user_id']}"));
                }
                else {
                    $data['login_errors']['login'] = 'Invalid username/password combination';
                }
            }
        }

        if (isset($_SESSION['message']) && ! empty($_SESSION['message'])) {
            $data['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $this->load->view('show-login');
        $this->load->view('common/footer');
    }
}
?>
