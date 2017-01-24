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
        $q = sprintf("SELECT school_id FROM schools WHERE (school_id=%d AND college_id=%d) LIMIT 1",
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

    public function add_school($data)
    {
        $q = sprintf("INSERT INTO user_schools (user_id, school_id, date_from, date_to ) " .
                     "VALUES (%d, %d, %s, %s)", $_SESSION['user_id'], $data['college_id'],
                     $this->db->escape($data['date_from']), $this->db->escape($data['date_to']));
        $this->run_query($q);
    }

    public function add_college($data)
    {
        $q = sprintf("INSERT INTO user_colleges (user_id, college_id, date_from, date_to) " .
                     "VALUES (%d, %d, %s, %s)", $_SESSION['user_id'], $data['college_id'],
                     $this->db->escape($data['date_from']), $this->db->escape($data['date_to']));
        $this->run_query($q);
    }

    public function get_countries()
    {
        $q = sprintf("SELECT country_id, country_name FROM countries");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function add_country($country_id)
    {
        $q = sprintf("UPDATE user_profile SET country_id=%d " .
                     "WHERE user_id=%d", $country_id, $_SESSION['user_id']);
        $this->run_query($q);
    }

    /**
     * Get the districts matching a given district name.
     */
    public function get_districts($district)
    {
        $q = sprintf("SELECT district_id, district_name FROM districts " .
                     "WHERE district_name LIKE %%s%", $this->db->escape($district));
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function add_district($district_id)
    {
        $q = sprintf("UPDATE user_profile SET district_id=%d " .
                     "WHERE user_id=%d", $district_id, $_SESSION['user_id']);
        $this->run_query($q);
    }

    public function add_programme($data)
    {
        $q = sprintf("INSERT INTO user_programmes (user_id, programme_id, date_from, date_to) " .
                     "VALUES(%d, %d, %s, %s)",
                    $_SESSION['user_id'], $data['programme_id'],
                    $this->db->escape($data['start_date']), $this->db->escape($data['end_date']));
        $this->run_query($q);

        $q = sprintf("UPDATE user_profile SET year_of_study=%d WHERE (user_id=%d)",
                     $data['year_of_study'], $_SESSION['user_id']);
        $this->run_query($q);
    }

    public function add_hall($data)
    {
        $q = sprintf("INSERT INTO user_halls (user_id, hall_id, date_from, date_to, resident) " .
                     "VALUES (%d, %d, %s, %s)", $_SESSION['user_id'], $data['hall_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']),
                     $data['resident']);
        $this->run_query($q);
    }

    public function add_hostel($data)
    {
        $q = sprintf("INSERT INTO user_hostels (user_id, hostel_id, date_from, date_to) " .
                     "VALUES (%d, %d, %s, %s)", $_SESSION['user_id'], $data['hostel_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']));
        $this->run_query($q);
    }
}
