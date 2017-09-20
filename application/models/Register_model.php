<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

class Register_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Adds a new user record to the user's table and also updates user_emails
     * table by adding user_id to the email used for registration.
     *
     * @param Array $data details about the user.
     * @return ID of newly created user.
     */
    public function register_user($data)
    {
        // Create the user's account.
        $profile_name = ucwords("{$data['lname']} {$data['other_names']}");
        $reg_sql = sprintf("INSERT INTO users
                            (dob, lname, other_names, gender, uname, passwd, profile_name)
                            VALUES ('%s', %s, %s, '%s', %s, %s, %s)",
                            $data['dob'], $this->db->escape(ucwords($data['lname'])),
                            $this->db->escape(ucwords($data['other_names'])), $data['gender'],
                            $this->db->escape($data['uname']),
                            $this->db->escape(password_hash($data['passwd'], PASSWORD_BCRYPT)),
                            $this->db->escape($profile_name));
        $this->db->query($reg_sql);

        $user_id = $this->db->insert_id();

        // Update the user_emails table.
        $update_sql = sprintf("UPDATE user_emails SET user_id = %d, is_primary = 1, is_backup = 0
                                WHERE (id = %d)",
                                $user_id, $data['user_email_id']);
        $this->db->query($update_sql);

        return $user_id;
    }

}
?>
