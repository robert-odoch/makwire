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
            redirect(base_url('user/index/' . $_SESSION['user_id']));
            exit(0);
        }
        
        $login_errors = array();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {            
            if (empty($this->input->post('username'))) {
                $login_errors['username'] = 'Please enter a username!';
            }
            else {
                $username = $this->input->post('username');   
            }
            
            if (empty($this->input->post('password'))) {
                $login_errors['password'] = 'Please enter a password!';
            }
            else {
                $password = $this->input->post('password');
            }
            
            if ( ! $login_errors && $this->login_model->is_valid($username, $password)) {
                    redirect(base_url('user/index/' . $_SESSION['user_id']));
            }
            else if ( ! $login_errors) {
                    $login_errors['login'] = 'Invalid username/password combination';
            }
        }
        
        $this->show_login_form($login_errors);
    }
    public function show_login_form($login_errors)
    {
        $data['title'] = 'Log in to your account';
        $data['login_errors'] = $login_errors;
        if (isset($_SESSION['message']) && ! empty($_SESSION['message'])) {
            $data['message'] = $_SESSION['message'];
            unset($_SESSION['message']);
        }
        $this->load->view('common/header', $data);
        $this->load->view('show-login');
        $this->load->view('common/footer');
    }
}
?>
