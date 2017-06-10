<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model');
    }

    /**
     * Checks whether an email address has already been used by another user
     * for registration.
     *
     * @param $email the email to check.
     * @return TRUE if email is already being used; FALSE otherwise.
     */
    public function is_registered_email($email)
    {
        $email_sql = sprintf("SELECT id FROM  user_emails " .
                                "WHERE (email = %s) LIMIT 1",
                                $this->db->escape($email));
        $email_query = $this->utility_model->run_query($email_sql);

        return ($email_query->num_rows() === 1);
    }

    /**
     * Checks whether a registered email address is associated with a user id.
     *
     * When a user first requests for registration by submitting an email address,
     * the user_id field is left blank as the user account wouldn't have been
     * created yet.
     * If an email is registered but no user_id exists, it implies that the user
     * hasn't completed the registration process.
     *
     * @param $email the email whose associated user_id is required.
     * @return TRUE if the user completed registration.
     */
    public function email_user_id_exists($email)
    {
        $sql = sprintf("SELECT user_id FROM user_emails " .
                        "WHERE email = %s AND user_id IS NOT NULL",
                        $this->db->escape($email));
        $query = $this->utility_model->run_query($sql);

        return ($query->num_rows() == 1);
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
        $profile_name = ucfirst($data['lname']) . ' ' . ucwords($data['other_names']);
        $reg_sql = sprintf("INSERT INTO users " .
                            "(dob, lname, other_names, gender, uname, passwd, profile_name) " .
                            "VALUES (%s, %s, %s, '%s', %s, %s, %s)",
                            $data['dob'], $this->db->escape($data['lname']),
                            $this->db->escape($data['other_names']), $data['gender'],
                            $this->db->escape($data['uname']),
                            $this->db->escape(password_hash($data['passwd'], PASSWORD_BCRYPT)),
                            $this->db->escape($profile_name));
        $this->utility_model->run_query($reg_sql);

        $user_id = $this->db->insert_id();

        // Update the user_emails table.
        $update_sql = sprintf("UPDATE user_emails SET user_id = %d, is_primary = TRUE, is_backup = FALSE " .
                                "WHERE (id = %d)",
                                $user_id, $data['user_email_id']);
        $this->utility_model->run_query($update_sql);

        return $user_id;
    }

}
?>
