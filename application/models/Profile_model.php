<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model
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

    private function run_query($q)
    {
        $query = $this->db->query($q);
        if ( ! $query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function get_halls()
    {
        $q = sprintf("SELECT hall_id, hall_name FROM halls");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function get_hostels()
    {
        $q = sprintf("SELECT hostel_id, hostel_name FROM hostels");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function get_programmes()
    {
        $q = sprintf("SELECT programme_id, programme_name FROM programmes");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function get_colleges()
    {
        $q = sprintf("SELECT college_id, college_name FROM colleges");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function college_and_school_exists($college_id, $school_id)
    {
        $q = sprintf("SELECT * FROM schools WHERE (school_id=%d AND college_id=%d) LIMIT 1",
                     $school_id, $college_id);
        $query = $this->run_query($q);
        if ($query->num_rows() == 1) {
            return TRUE;
        }

        return FALSE;
    }

    public function get_schools()
    {
        $q = sprintf("SELECT school_id, college_id, school_name FROM schools");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function add_school($school_id)
    {
        $q = sprintf("UPDATE user_profile SET school_id=%d WHERE user_id=%d LIMIT 1",
                     $school_id, $_SESSION['user_id']);
        $this->run_query($q);
    }

    public function add_college($college_id)
    {
        $q = sprintf("INSERT INTO user_profile (user_id, college_id) " .
                     "VALUES (%d, %d)",
                     $_SESSION['user_id'], $college_id);
        $this->run_query($q);
    }
}
