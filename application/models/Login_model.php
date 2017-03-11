<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /*** Utility ***/
    private function handle_error($error)
    {
        print($error);
        exit(1);
    }

    private function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function user_exists($username, $password)
    {
        $user_sql = sprintf("SELECT user_id, passwd FROM users WHERE uname = %s",
                                $this->db->escape($username));
        $user_query = $this->run_query($user_sql);

        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if (password_verify($password, $user_query->row()->passwd)) {
            $login_sql = sprintf("UPDATE users SET logged_in=1 WHERE uname = %s LIMIT 1",
                                    $this->db->escape($username));

            $this->run_query($login_sql);

            session_start();
            session_regenerate_id(TRUE);
            $_SESSION['user_id'] = $user_query->row()->user_id;

            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}
