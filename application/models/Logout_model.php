<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model');
    }

    public function logout()
    {
        $login_sql = sprintf("UPDATE users SET logged_in = 0 WHERE user_id = %d",
                                $_SESSION['user_id']);

        $this->utility_model->run_query($login_sql);

        $_SESSION = array();
        session_destroy();
        setcookie(session_name(), '', time()-300);
    }
}
?>
