<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model']);
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

    public function get_name_combinations($user_id)
    {
        $sql = sprintf("SELECT lname, other_names FROM users WHERE user_id = %d",
                        $_user_id);
        $query = $this->utility_model->run_query($sql);
        $result = $query->row_array();

        $lname = $result['lname'];
        $other_names = $result['other_names'];
        return [
            ucfirst($lname) . ' ' . ucfirst($other_names),
            ucfirst($other_names) . ' ' . ucfirst($lname)
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
        $sql = sprintf("UPDATE users SET lname = %s, other_names = %s, profile_name = %s " .
                        "WHERE user_id = %d", $this->db->escape($last_name),
                        $this->db->escape($other_names),
                        $this->db->escape($last_name) . ' ' . $this->db->escape($other_names),
                        $user_id);
        $this->utility_model->run_query($sql);
    }

    public function delete_account($user_id)
    {
        $sql = sprintf("DELETE FROM users WHERE user_id = %d", $user_id);
        $this->db->query($sql);
    }
}
?>
