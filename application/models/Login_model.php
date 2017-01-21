<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function is_valid($username, $password)
    {
        $q = sprintf("SELECT user_id, passwd FROM users WHERE uname=%s", $this->db->escape($username));
        $query = $this->db->query($q);
        if ( ! $query) {
            $error = $this->db->error();
            echo $error;
            exit(1);
        }
        
        if ($query->num_rows() === 0) {
            return FALSE;
        }
        
        if (password_verify($password, $query->row()->passwd)) {
            $q = sprintf("UPDATE users SET logged_in=%d WHERE uname=%s LIMIT 1",
                         1, $this->db->escape($username));
            
            if ( ! $this->db->query($q)) {
                $error = $this->db->error();
                echo $error();
                exit(1);
            }
            
            session_start();
            session_regenerate_id(TRUE);
            $_SESSION['user_id'] = $query->row()->user_id;
            
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}
