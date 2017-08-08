<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('exceptions/NotFoundException.php');

class Settings_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
        $this->load->model(['utility_model', 'user_model']);
    }

    public function get_emails()
    {
        $sql = sprintf("SELECT email, is_primary FROM user_emails " .
                        "WHERE (user_id = %d AND activation_code IS NULL) " .
                        "ORDER BY is_primary DESC, is_backup DESC",
                        $_SESSION['user_id']);
        $query = $this->utility_model->run_query($sql);

        return $query->result_array();
    }

    public function set_primary_email($email_address)
    {
        // Make all emails for this user non-primary.
        $sql = sprintf("UPDATE user_emails SET is_primary = FALSE, is_backup = TRUE " .
                        "WHERE (user_id = %d)", $_SESSION['user_id']);
        $this->utility_model->run_query($sql);

        // Set the primary email address.
        $sql = sprintf("UPDATE user_emails SET is_primary = TRUE, is_backup = FALSE " .
                        "WHERE (user_id = %d AND email = '%s')",
                        $_SESSION['user_id'], $email_address);
        $this->utility_model->run_query($sql);
    }

    public function set_backup_email($email_address)
    {
        // Make all emails for this user non-backup.
        // This also carters for 'allow only primary email address' i.e., 'none'.
        $sql = sprintf("UPDATE user_emails SET is_backup = FALSE " .
                        "WHERE (user_id = %d AND is_primary IS FALSE)",
                        $_SESSION['user_id']);
        $this->utility_model->run_query($sql);

        // Set backup email address(es).
        if ($email_address == 'all') {
            $sql = sprintf("UPDATE user_emails SET is_backup = TRUE " .
                            "WHERE (user_id = %d AND is_primary IS FALSE)",
                            $_SESSION['user_id']);
        }
        else {
            $sql = sprintf("UPDATE user_emails SET is_backup = TRUE " .
                            "WHERE (user_id = %d AND email = '%s')",
                            $_SESSION['user_id'], $email_address);
        }

        $this->utility_model->run_query($sql);
    }

    /**
     * Adds an email address for a user.
     *
     * @param $email the email to be added.
     */
    public function add_email($email, $activation_code)
    {
        if (isset($_SESSION['user_id'])) {
            // Existing user adding another email address.
            $sql = sprintf("INSERT INTO user_emails (user_id, email, activation_code) " .
                            "VALUES (%d, '%s', '%s')",
                            $_SESSION['user_id'], $email, $activation_code);
        }
        else {
            // New user registering.
            $sql = sprintf("INSERT INTO user_emails (email, activation_code) " .
                            "VALUES ('%s', '%s')", $email, $activation_code);
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
        $email_sql = sprintf("SELECT id FROM  user_emails " .
                                "WHERE (email = %s) LIMIT 1",
                                $this->db->escape($email));
        $email_query = $this->utility_model->run_query($email_sql);

        return ($email_query->num_rows() === 1);
    }

    public function is_activated_email($email)
    {
        $sql = sprintf("SELECT id FROM user_emails " .
                        "WHERE (email = '%s' AND activation_code IS NULL) " .
                        "LIMIT 1", $email);
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
    public function activate_email($activation_code)
    {
        // Check whether the record exists.
        $email_sql = sprintf("SELECT id FROM user_emails WHERE (activation_code = %s)",
                             $this->db->escape($activation_code));
        $email_query = $this->utility_model->run_query($email_sql);
        if ($email_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        // Set activation code to NULL.
        $update_sql = sprintf("UPDATE user_emails SET activation_code = NULL " .
                                "WHERE activation_code = %s",
                                $this->db->escape($activation_code));
        $this->utility_model->run_query($update_sql);
    }

}
?>
