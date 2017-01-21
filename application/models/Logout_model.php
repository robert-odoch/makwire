<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function logout()
    {
        $q = sprintf("UPDATE users SET logged_in=%d WHERE user_id=%d",
                     0, $_SESSION['user_id']);
        
        if ( ! $this->db->query($q)) {
            $error = $this->db->error();
            echo $error;
            exit(1);
        }
        
        $_SESSION = array();
        session_destroy();
        setcookie(session_name(), '', time()-300);
        
        redirect(base_url('login/'));
        exit(0);
    }
}
?>