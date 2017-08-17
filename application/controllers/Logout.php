<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model('logout_model');
    }

    public function index()
    {
        $this->logout_model->logout($_SESSION['user_id']);
        $_SESSION = array();
        session_destroy();
        setcookie(session_name(), '', time()-300);

        redirect(base_url('login'));
    }
}
