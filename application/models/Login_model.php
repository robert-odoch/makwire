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
    }

    /**
     * Checks whether the given username and password matches a record.
     *
     * @param $identifier Username or email address for user.
     * @param $password Password.
     * @return true if the given credentials combination exists on record.
     */
    public function is_valid_login($identifier, $password)
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user_sql = sprintf("SELECT u.user_id, u.passwd FROM users u
                                 LEFT JOIN user_emails ue ON (u.user_id = ue.user_id)
                                 WHERE (ue.email = %s)", $this->db->escape($identifier));
        }
        else {
            $user_sql = sprintf("SELECT user_id, passwd FROM users WHERE uname = %s",
                                    $this->db->escape($identifier));
        }
        $user_query = $this->db->query($user_sql);

        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if (password_verify($password, $user_query->row()->passwd)) {
            $user_id = $user_query->row()->user_id;
            return $user_id;
        }
        else {
            return FALSE;
        }
    }
}
