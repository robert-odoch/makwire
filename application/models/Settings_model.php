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
     * @return ID of the newly added email.
     */
    public function add_email($email)
    {
        if (isset($_SESSION['user_id'])) {
            // Existing user adding another email address.
            $sql = sprintf("INSERT INTO user_emails (user_id, email, activation_code) " .
                            "VALUES (%d, '%s', SHA1('%s'))",
                            $_SESSION['user_id'], $email, $email);
        }
        else {
            // New user registering.
            $sql = sprintf("INSERT INTO user_emails (email, activation_code) " .
                            "VALUES ('%s', SHA1('%s'))", $email, $email);
        }
        $this->utility_model->run_query($sql);

        return $this->db->insert_id();
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
     * @param $user_email_id ID in the user_emails table.
     * @param $activation_code activation code for this email address.
     */
    public function activate_email($user_email_id, $activation_code)
    {
        // Check whether the record exists.
        $email_sql = sprintf("SELECT id FROM user_emails " .
                            "WHERE (id = %d AND activation_code = %s)",
                            $user_email_id, $this->db->escape($activation_code));
        $email_query = $this->utility_model->run_query($email_sql);
        if ($email_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        // Set activation code to NULL.
        $update_sql = sprintf("UPDATE user_emails SET activation_code = NULL " .
                                "WHERE id = %d",
                                $user_email_id);
        $this->utility_model->run_query($update_sql);
    }

    public function block_user($user_id)
    {
        // Block a user only once.
        if (in_array($user_id, $this->get_blocked_users_ids())) {
            return;
        }

        $sql = sprintf("INSERT INTO blocked_friends (user_id, friend_id) " .
                        "VALUES (%d, %d)", $_SESSION['user_id'], $user_id);
        $this->utility_model->run_query($sql);
    }

    public function unblock_user($user_id)
    {
        $sql = sprintf("DELETE FROM blocked_friends WHERE (user_id = %d AND friend_id = %d)",
                        $_SESSION['user_id'], $user_id);
        $this->utility_model->run_query($sql);
    }

    public function get_blocked_users()
    {
        $sql = sprintf("SELECT DISTINCT(bf.friend_id) AS user_id, u.profile_name  FROM blocked_friends bf " .
                        "LEFT JOIN users u ON (bf.friend_id = u.user_id) " .
                        "WHERE (bf.user_id = %d)", $_SESSION['user_id']);
        $query = $this->utility_model->run_query($sql);
        $blocked_users = $query->result_array();
        foreach ($blocked_users as &$bu) {
            $bu['profile_pic_path'] = $this->user_model->get_profile_pic_path($bu['user_id']);
        }
        unset($bu);

        return $blocked_users;
    }

    public function get_searched_user($query)
    {
        $friends_ids = $this->user_model->get_friends_ids($_SESSION['user_id']);
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        $blocked_users_ids = $this->get_blocked_users_ids();
        $blocked_users_ids[] = 0;
        $blocked_users_ids = implode(',', $blocked_users_ids);

        if (filter_var($query, FILTER_VALIDATE_EMAIL)) {
            $sql = sprintf("SELECT ue.user_id, u.profile_name FROM user_emails ue " .
                            "LEFT JOIN users u ON(ue.user_id = u.user_id) " .
                            "WHERE (ue.email = '%s' AND ue.user_id IN(%s) AND ue.user_id NOT IN(%s))",
                            $query, $friends_ids, $blocked_users_ids);
        }
        else {
            $keywords = preg_split("/[\s,]+/", $query);
            foreach ($keywords as &$keyword) {
                $keyword = strtolower("+{$keyword}");

                // The @ sign breaks this query if it is used as part of an invalid email address.
                $keyword = str_replace('@', '', $keyword);
            }
            unset($keyword);

            $key = implode(' ', $keywords);
            $sql = sprintf("SELECT user_id, profile_name FROM users " .
                            "WHERE MATCH(profile_name) AGAINST (%s IN BOOLEAN MODE) AND " .
                            "user_id IN(%s) AND user_id NOT IN(%s)",
                            $this->db->escape($key), $friends_ids, $blocked_users_ids);
        }

        $results = $this->utility_model->run_query($sql)->result_array();
        foreach ($results as &$r) {
            $r['profile_pic_path'] = $this->user_model->get_profile_pic_path($r['user_id']);
        }
        unset($r);

        return $results;
    }

    private function get_blocked_users_ids() {
        $sql = sprintf("SELECT DISTINCT(friend_id) FROM blocked_friends " .
                        "WHERE (user_id = %d)", $_SESSION['user_id']);
        $query = $this->utility_model->run_query($sql);
        $results = $query->result_array();

        $blocked_users_ids = [];
        foreach ($results as $r) {
            $blocked_users_ids[] = $r['friend_id'];
        }

        return $blocked_users_ids;
    }

}
?>
