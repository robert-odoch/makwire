<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url("login"));
        }

        $this->load->model('logout_model');
    }

    public function index()
    {
        $this->logout_model->logout();
        redirect(base_url('login'));
    }
}
