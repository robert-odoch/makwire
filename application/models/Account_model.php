<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

class Account_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model']);
    }

    public function is_username_taken($username)
    {
        $sql = sprintf('SELECT user_id FROM users WHERE uname = %s',
                        $this->db->escape($username));
        $query = $this->db->query($username);
        return ($query->num_rows() == 1);
    }

    public function user_exists($user_id, $password)
    {
        $sql = sprintf("SELECT passwd FROM users WHERE user_id = %d", $user_id);
        $query = $this->utility_model->run_query($sql);
        return password_verify($password, $query->row()->passwd);
    }

    public function change_password($user_id, $new_password)
    {
        $sql = sprintf("UPDATE users SET passwd = '%s' WHERE user_id = %d",
                        password_hash($new_password, PASSWORD_BCRYPT), $user_id);
        $this->utility_model->run_query($sql);
    }

    public function get_name($user_id)
    {
        $sql = sprintf('SELECT lname, other_names FROM users WHERE user_id = %d',
                        $user_id);
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get_name_combinations($user_id)
    {
        $sql = sprintf("SELECT lname, other_names FROM users WHERE user_id = %d", $user_id);
        $query = $this->utility_model->run_query($sql);
        $result = $query->row_array();

        $lname = $result['lname'];
        $other_names = $result['other_names'];
        return [
            "{$lname} {$other_names}",
            "{$other_names} {$lname}"
        ];

        return $name_combinations;
    }

    public function set_prefered_profile_name($user_id, $name)
    {
        $sql = sprintf("UPDATE users SET profile_name = %s WHERE user_id = %d",
                        $this->db->escape($name), $user_id);
        $this->utility_model->run_query($sql);
    }

    public function change_name($user_id, $last_name, $other_names)
    {
        $sql = sprintf("UPDATE users SET lname = %s, other_names = %s, profile_name = %s
                        WHERE user_id = %d", $this->db->escape(ucwords($last_name)),
                        $this->db->escape(ucwords($other_names)),
                        $this->db->escape(ucwords($last_name)) . ' ' . $this->db->escape(ucwords($other_names)),
                        $user_id);
        $this->utility_model->run_query($sql);
    }

    public function delete_account($user_id)
    {
        $sql = sprintf("DELETE FROM users WHERE user_id = %d", $user_id);
        $this->db->query($sql);
    }

    public function get_emails($user_id)
    {
        $sql = sprintf("SELECT email, is_primary FROM user_emails
                        WHERE (user_id = %d AND activation_code IS NULL)
                        ORDER BY is_primary DESC, is_backup DESC",
                        $user_id);
        $query = $this->utility_model->run_query($sql);

        return $query->result_array();
    }

    public function set_primary_email($user_id, $email_address)
    {
        // Make all emails for this user non-primary.
        $sql = sprintf("UPDATE user_emails SET is_primary = FALSE, is_backup = TRUE
                        WHERE (user_id = %d)", $user_id);
        $this->utility_model->run_query($sql);

        // Set the primary email address.
        $sql = sprintf("UPDATE user_emails SET is_primary = TRUE, is_backup = FALSE
                        WHERE (user_id = %d AND email = '%s')",
                        $user_id, $email_address);
        $this->utility_model->run_query($sql);
    }

    public function set_backup_email($user_id, $email_address)
    {
        // Make all emails for this user non-backup.
        // This also carters for 'allow only primary email address' i.e., 'none'.
        $sql = sprintf("UPDATE user_emails SET is_backup = FALSE
                        WHERE (user_id = %d AND is_primary IS FALSE)",
                        $user_id);
        $this->utility_model->run_query($sql);

        // Set backup email address(es).
        if ($email_address == 'all') {
            $sql = sprintf("UPDATE user_emails SET is_backup = TRUE
                            WHERE (user_id = %d AND is_primary IS FALSE)",
                            $user_id);
        }
        else {
            $sql = sprintf("UPDATE user_emails SET is_backup = TRUE
                            WHERE (user_id = %d AND email = '%s')",
                            $user_id, $email_address);
        }

        $this->utility_model->run_query($sql);
    }

    /**
     * Adds an email address for a user.
     *
     * @param $email the email to be added.
     */
    public function add_email($user_id, $email, $activation_code)
    {
        if ($user_id !== NULL) {
            // Existing user adding another email address.
            $sql = sprintf("INSERT INTO user_emails (user_id, email, activation_code)
                            VALUES (%d, '%s', '%s')",
                            $user_id, $email, $activation_code);
        }
        else {
            // New user registering.
            $sql = sprintf("INSERT INTO user_emails (email, activation_code)
                            VALUES ('%s', '%s')", $email, $activation_code);
        }
        $this->utility_model->run_query($sql);
    }

    /**
     * Checks whether an email address exists in our database.
     *
     * @param $email the email to check.
     * @return TRUE if email exists.
     */
    public function is_registered_email($email)
    {
        $email_sql = sprintf("SELECT id FROM  user_emails WHERE (email = %s) LIMIT 1",
                                $this->db->escape($email));
        $email_query = $this->utility_model->run_query($email_sql);

        return ($email_query->num_rows() === 1);
    }

    public function is_activated_email($email)
    {
        $sql = sprintf("SELECT id FROM user_emails
                        WHERE (email = '%s' AND activation_code IS NULL)
                        LIMIT 1", $email);
        $query = $this->utility_model->run_query($sql);
        return ($query->num_rows() == 1);
    }

    /**
     * Activates a user's email address by setting it's activation code to NULL.
     *
     * Throws NotFoundException if no matching record is found.
     *
     * @param $activation_code activation code for this email address.
     */
    public function activate_email($email, $activation_code)
    {
        $update_sql = sprintf("UPDATE user_emails SET activation_code = NULL
                                WHERE email = %s AND activation_code = %s",
                                $this->db->escape($email),
                                $this->db->escape($activation_code));
        $this->db->query($update_sql);

        if ($this->db->affected_rows() == 0) {
            throw new NotFoundException();
        }
    }

    public function email_has_user($email)
    {
        $sql = sprintf("SELECT user_id FROM user_emails WHERE email = %s AND user_id IS NOT NULL",
                        $this->db->escape($email));
        $query = $this->db->query($sql);
        return ($query->num_rows() != 0);
    }

    public function gen_email_verification_code()
    {
        return md5(uniqid(rand(), true));
    }

    public function send_email($from, $to, $subject, $body)
    {
        $result = $this->email->from($from)->to($to)->subject($subject)->message($body)->send();
        return $result;
    }

    public function save_password_reset_token($email, $token)
    {
        $sql = sprintf("INSERT INTO password_reset_token VALUES (%s, %s)",
                        $this->db->escape($email), $this->db->escape($token));
        $this->db->query($sql);
    }

    public function get_password_reset_user_data($token)
    {
        $sql = sprintf('SELECT email FROM password_reset_token WHERE token = %s',
                        $this->db->escape($token));
        $query = $this->db->query($sql);
        if ($query->num_rows() == 0) {
            return NULL;
        }

        $email = $query->row_array()['email'];
        $sql = sprintf('SELECT user_id FROM user_emails WHERE email = %s',
                        $this->db->escape($email));
        $query = $this->db->query($sql);
        if ($query->num_rows() == 0) {
            return NULL;
        }

        $user_id = $query->row_array()['user_id'];
        $data = [
            'user_id'=>$user_id,
            'email'=>$email
        ];

        return $data;
    }

    public function get_primary_email($user_id)
    {
        $sql = sprintf('SELECT email FROM user_emails WHERE user_id = %d AND is_primary IS TRUE', $user_id);
        $query = $this->db->query($sql);
        return $query->row_array()['email'];
    }
}
?>
