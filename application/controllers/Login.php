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
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

            // Already logged in user.
            redirect(base_url("user/news-feed"));
        }

        $data['title'] = 'Log in to your account';
        $this->load->view("common/header", $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($this->input->post('username')))) {
                $data['login_errors']['username'] = 'Please enter a username!';
            }
            else {
                $username = $this->input->post('username');
                $data['username'] = $username;
            }

            if (empty(trim($this->input->post('password')))) {
                $data['login_errors']['password'] = 'Please enter a password!';
            }
            else {
                $password = $this->input->post('password');
            }

            if (!isset($data['login_errors'])) {
                if ($this->login_model->is_valid_login($username, $password)) {
                    if (isset($_SESSION['return_uri'])) {
                        $return_url = base_url(str_replace("/makwire/", "", $_SESSION['return_uri']));
                        unset($_SESSION['return_uri']);
                        redirect($return_url);
                    }
                    else {
                        redirect(base_url("user/news-feed"));
                    }
                }
                else {
                    $data['login_errors']['login'] = 'Invalid username/password combination';
                }
            }
        }

        if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
            $data['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $this->load->view('show/login', $data);
        $this->load->view('common/external-page-footer');
    }
}
?>
