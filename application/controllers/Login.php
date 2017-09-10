<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! empty($_SESSION['user_id'])) {
            redirect(base_url('news-feed'));  // Already logged in user.
        }

        $this->load->model('login_model');
    }

    public function index()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $identifier = trim($this->input->post('identifier'));
            if (strlen($identifier) == 0) {
                $data['login_errors']['identifier'] = 'Please enter a username or email address!';
            }

            // Check if user is trying to login using an email address.
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $this->load->model('account_model');
                if (!$this->account_model->is_activated_email($identifier)) {
                    $data['login_errors']['identifier'] = "Sorry, makwire doesn't recognise that email address.";
                }
            }

            $password = trim($this->input->post('password'));
            if (strlen($password) == 0) {
                $data['login_errors']['password'] = 'Please enter a password!';
            }

            if (empty($data['login_errors'])) {
                if ($user_id = $this->login_model->is_valid_login($identifier, $password)) {
                    session_start();
                    session_regenerate_id(TRUE);
                    $_SESSION['user_id'] = $user_id;

                    if (isset($_SESSION['return_uri'])) {
                        $return_url = base_url(
                            str_replace('/makwire/', '', $_SESSION['return_uri'])
                        );
                        unset($_SESSION['return_uri']);
                        redirect($return_url);
                    }
                    else {
                        redirect(base_url('news-feed'));
                    }
                }
                else {
                    $data['login_errors']['login'] = 'Invalid login, please try again.';
                }
            }
            else {
                $data['identifier'] = $identifier;
            }
        }

        $data['title'] = 'Log in to your account';
        $this->load->view('common/header', $data);

        if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
            $data['info_message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $this->load->view('show/login', $data);
        $this->load->view('common/footer');
    }
}
?>
