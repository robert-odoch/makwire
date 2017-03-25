<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }
    }

    public function forgot_password()
    {

    }

    public function change_passord()
    {

    }

    public function deactivate()
    {

    }

    public function delete()
    {

    }

}
?>
