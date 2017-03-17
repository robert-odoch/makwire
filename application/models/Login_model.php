<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contains function for loggin in a user.
 */
class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model');
    }

    /**
     * Checks whether the given username and password matches a record.
     *
     * @param $username the username supplied by the user.
     * @param $password the password supplied by the user.
     * @return true if the given username and password exists on record.
     */
    public function is_valid_login($username, $password)
    {
        $user_sql = sprintf("SELECT user_id, passwd FROM users WHERE uname = %s",
                                $this->db->escape($username));
        $user_query = $this->utility_model->run_query($user_sql);

        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if (password_verify($password, $user_query->row()->passwd)) {
            $login_sql = sprintf("UPDATE users SET logged_in=1 WHERE uname = %s LIMIT 1",
                                    $this->db->escape($username));

            $this->utility_model->run_query($login_sql);

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
