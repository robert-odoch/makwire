<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /*** Utility ***/
    private function handle_error($error)
    {
        print($error);
        exit(1);
    }

    private function run_query($q)
    {
        $query = $this->db->query($q);
        if ( ! $query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function user_exists($username, $password)
    {
        $q = sprintf("SELECT user_id, passwd FROM users WHERE uname=%s",
                     $this->db->escape($username));
        $query = $this->run_query($q);

        if ($query->num_rows() === 0) {
            return FALSE;
        }

        if (password_verify($password, $query->row()->passwd)) {
            $q = sprintf("UPDATE users SET logged_in=1 WHERE uname=%s LIMIT 1",
                         $this->db->escape($username));

            $this->run_query($q);

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
