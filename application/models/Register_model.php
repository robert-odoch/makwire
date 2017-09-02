<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

class Register_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model');
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
                            "VALUES ('%s', %s, %s, '%s', %s, %s, %s)",
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

    public function get_formatted_email($activation_code)
    {
        $email_data['email_heading'] = 'makwire account creation';
        $email_data['message'] = "<p>
                                    Hi there, thanks for your interest in joining
                                    <a href='http://www.makwire.com'>makwire</a>.
                                </p>
                                <p>
                                  Please use the link below to verify your email
                                  address and continue with the registration process.
                                </p>
                                <a href='" .
                                    base_url("account/activate-email/{$activation_code}") . "'
                                    style='color: #fff; margin: 5px 0; padding: 10px; display: block; text-align: center; border-radius: 2px;
                                        border-color: #46b8da; text-decoration: none; box-sizing: border-box; font-variant: small-caps;
                                        background-color: #5bc0de;'>Verify your email address</a>

                                <hr>
                                <p>
                                    You’re receiving this email because you recently tried to create
                                    a new <b>makwire</b> account. If this wasn’t you, please ignore this email.
                                </p>";

        $email_body = $this->load->view('email', $email_data, TRUE);

        return $email_body;
    }

}
?>
