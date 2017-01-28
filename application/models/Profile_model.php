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

    private function are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)
    {
        if (($data_date_from < $rdate_from) && ($data_date_to > $rdate_from) ||
            ($data_date_from < $rdate_to) && ($data_date_to > $rdate_to) ||
            ($data_date_from >= $rdate_from) && ($data_date_to <= $rdate_to)) {
            return TRUE;
        }

        return FALSE;
    }
    /*** End Utility ***/

    public function get_profile($user_id)
    {
        // Get the year of study, country and district.
        $q = sprintf("SELECT country_id, district_id FROM user_profile WHERE (user_id=%d)",
                     $user_id);
        $query = $this->run_query($q);
        if ($query->num_rows() == 1) {
            $data = $query->row_array();

            // Get the name of the country.
            if ($data['country_id']) {
                $q = sprintf("SELECT country_name FROM countries WHERE (country_id=%d)",
                             $data['country_id']);
                $query = $this->run_query($q);
                $data['country'] = $query->row()->country_name;
            }
            else {
                $data['country'] = NULL;
            }

            // Get the name of the district.
            if ($data['district_id']) {
                $q = sprintf("SELECT district_name FROM districts WHERE (district_id=%d)",
                             $data['district_id']);
                $query = $this->run_query($q);
                $data['district'] = $query->row()->district_name;
            }
            else {
                $data['district'] = NULL;
            }
        }
        else {
            $data = array(
                'level'=>NULL,
                'year_of_study'=>NULL,
                'country'=>NULL,
                'district'=>NULL
            );
        }

        // Get the colleges.
        $q = sprintf("SELECT id, college_id, date_from, date_to, level, YEAR(date_from) AS start_year, YEAR(date_to) AS end_year " .
                     "FROM user_colleges WHERE (user_id=%d) ORDER BY date_to DESC",
                     $user_id);
        $query = $this->run_query($q);

        $data['colleges'] = array();
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $r) {
                // Get the college name.
                $q = sprintf("SELECT college_name FROM colleges WHERE (college_id=%d)",
                             $r['college_id']);
                $query = $this->run_query($q);
                $r['college_name'] = $query->row()->college_name;

                array_push($data['colleges'], $r);
            }
        }

        // Get the schools.
        $q = sprintf("SELECT school_id, date_from, date_to FROM user_schools " .
                     "WHERE (user_id=%d) ORDER BY date_to DESC",
                     $user_id);
        $query = $this->run_query($q);

        $data['schools'] = array();
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $r) {
                // Get the school name.
                $q = sprintf("SELECT school_name FROM schools WHERE (school_id=%d)",
                             $r['school_id']);
                $query = $this->run_query($q);
                $r['school_name'] = $query->row()->school_name;

                array_push($data['schools'], $r);
            }
        }

        // Get the programmes.
        $q = sprintf("SELECT id, programme_id, date_from, date_to FROM user_programmes " .
                     "WHERE (user_id=%d) ORDER BY date_to DESC", $user_id);
        $query = $this->run_query($q);

        $data['programmes'] = array();
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $r) {
                // Get the programme name.
                $q = sprintf("SELECT programme_name FROM programmes WHERE (programme_id=%d)",
                             $r['programme_id']);
                $query = $this->run_query($q);
                $r['programme_name'] = $query->row()->programme_name;

                array_push($data['programmes'], $r);
            }
        }

        // Get the halls.
        $q = sprintf("SELECT hall_id, date_from, date_to, YEAR(date_from) AS start_year, YEAR(date_to) AS end_year, resident " .
                     "FROM user_halls WHERE (user_id=%d) ORDER BY date_to DESC",
                     $user_id);
        $query = $this->run_query($q);

        $data['halls'] = array();
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $r) {
                // Get the hall name.
                $q = sprintf("SELECT hall_name FROM halls WHERE (hall_id=%d)",
                             $r['hall_id']);
                $query = $this->run_query($q);
                $r['hall_name'] = $query->row()->hall_name;

                array_push($data['halls'], $r);
            }
        }

        // Get the hostel.
        $q = sprintf("SELECT hostel_id, date_from, date_to, YEAR(date_from) AS start_year, YEAR(date_to) AS end_year " .
                     "FROM user_hostels WHERE (user_id=%d) ORDER BY date_to DESC",
                     $user_id);
        $query = $this->run_query($q);

        $data['hostels'] = array();
        if ($query->num_rows() > 0) {
            print_r($query);
            $results = $query->result_array();
            foreach ($results as $r) {
                // Get the hostel name.
                $q = sprintf("SELECT hostel_name FROM hostels WHERE (hostel_id=%d)",
                             $r['hostel_id']);
                $query = $this->run_query($q);
                $r['hostel_name'] = $query->row()->hall_name;

                array_push($data['hostels'], $r);
            }
        }

        return $data;
    }

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

    public function get_programmes($user_college_id)
    {
        $college_info = $this->get_user_college($user_college_id);
        if ($college_info) {
            // Get all programmes under that school.
            $q = sprintf("SELECT programme_id, programme_name FROM programmes " .
                         "WHERE (school_id=%d)",
                         $college_info['schools'][0]['school_id']);
            $query = $this->run_query($q);

            return $query->result_array();
        }

        return NULL;
    }

    public function get_user_college($user_college_id)
    {
        $q = sprintf("SELECT college_id, date_from, date_to, " .
                     "DAY(date_from) AS start_day, MONTH(date_from) AS start_month, YEAR(date_from) AS start_year, " .
                     "DAY(date_to) AS end_day, MONTH(date_to) AS end_month, YEAR(date_to) AS end_year " .
                     "FROM user_colleges WHERE (id=%d AND user_id=%d)",
                     $user_college_id, $_SESSION['user_id']);
        $query = $this->run_query($q);

        if ($query->num_rows() === 1) {
            // Get the name of the college.
            $data['colleges'][] = $query->row_array();
            $q = sprintf("SELECT college_name FROM colleges WHERE (college_id=%d)",
                         $data['colleges'][0]['college_id']);
            $query = $this->run_query($q);
            $data['colleges'][0]['college_name'] = $query->row()->college_name;

            // Get the school.
            $q = sprintf("SELECT school_id FROM user_schools " .
                         "WHERE (user_id=%d AND date_from='%s' AND date_to='%s')",
                         $_SESSION['user_id'], $data['colleges'][0]['date_from'], $data['colleges'][0]['date_to']);
            $query = $this->run_query($q);
            $data['schools'][0]['school_id'] = $query->row_array()['school_id'];

            // Get the name of the school.
            $q = sprintf("SELECT school_name FROM schools WHERE (school_id=%d)",
                         $data['schools'][0]['school_id']);
            $query = $this->run_query($q);
            $data['schools'][0]['school_name'] = $query->row_array()['school_name'];

            return $data;
        }

        return NULL;
    }

    public function get_user_programme($user_programme_id) {
        $q = sprintf("SELECT programme_id, date_from, date_to, year_of_study " .
                     "FROM user_programmes WHERE (id=%d AND user_id=%d)",
                     $user_programme_id, $_SESSION['user_id']);
        $query = $this->run_query($q);

        if ($query->num_rows() === 1) {
            // Get the name of the programme.
            $data['programmes'][] = $query->row_array();
            $q = sprintf("SELECT programme_name FROM programmes WHERE (programme_id=%d)",
                         $data['programmes'][0]['programme_id']);
            $query = $this->run_query($q);
            $data['programmes'][0]['programme_name'] = $query->row()->programme_name;
            return $data;
        }

        return NULL;
    }

    public function get_colleges()
    {
        $q = sprintf("SELECT college_id, college_name FROM colleges");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function college_and_school_exists($college_id, $school_id)
    {
        $q = sprintf("SELECT school_id FROM schools " .
                     "WHERE (school_id=%d AND college_id=%d) LIMIT 1",
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

    public function add_college($data)
    {
        // First check whether a college already exists in the range of years provided.
        $q = sprintf("SELECT date_from, date_to FROM user_colleges " .
                     "WHERE (user_id=%d)",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                $rdate_from = date_create($r['date_from']);
                $rdate_to = date_create($r['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, then the years are OK.
        $q = sprintf("INSERT INTO user_colleges (user_id, college_id, date_from, date_to) " .
                     "VALUES (%d, %d, %s, %s)",
                     $_SESSION['user_id'], $data['college_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']));
        $this->run_query($q);

        // Also add the school.
        $q = sprintf("INSERT INTO user_schools (user_id, school_id, date_from, date_to ) " .
                     "VALUES (%d, %d, %s, %s)",
                     $_SESSION['user_id'], $data['school_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']));
        $this->run_query($q);

        return TRUE;
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
                     "WHERE user_id=%d",
                     $country_id, $_SESSION['user_id']);
        $this->run_query($q);
    }

    /**
     * Get the districts matching a given district name.
     * TODO: A lot to be done.
     */
    public function get_districts($district)
    {
        $q = sprintf("SELECT district_id, district_name FROM districts " .
                     "WHERE district_name LIKE '*%s*'",
                     $district);
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function add_district($district_id)
    {
        $q = sprintf("UPDATE user_profile SET district_id=%d " .
                     "WHERE user_id=%d",
                     $district_id, $_SESSION['user_id']);
        $this->run_query($q);
    }

    public function add_programme($data)
    {
        // First check whether a programme already exists in the range of years provided.
        $q = sprintf("SELECT date_from, date_to FROM user_programmes " .
                     "WHERE (user_id=%d)",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                $rdate_from = date_create($r['date_from']);
                $rdate_to = date_create($r['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, then the years are OK.
        $q = sprintf("INSERT INTO user_programmes (user_id, programme_id, date_from, date_to, year_of_study) " .
                     "VALUES(%d, %d, %s, %s, '%d')",
                     $_SESSION['user_id'], $data['programme_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']), $data['year_of_study']);
        $this->run_query($q);

        return TRUE;
    }

    public function add_hall($data)
    {
        // First check whether a hall already exists in the range of years provided.
        $q = sprintf("SELECT date_from, date_to FROM user_halls " .
                     "WHERE (user_id=%d)",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                $rdate_from = date_create($r['date_from']);
                $rdate_to = date_create($r['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, then things are OK.
        $q = sprintf("INSERT INTO user_halls (user_id, hall_id, date_from, date_to, resident) " .
                     "VALUES (%d, %d, %s, %s, %d)",
                     $_SESSION['user_id'], $data['hall_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']),
                     $data['resident']);
        $this->run_query($q);

        return TRUE;
    }

    public function add_hostel($data)
    {
        // First check whether a hall already exists in the range of years provided,
        // and the user is resident.
        $q = sprintf("SELECT date_from, date_to, resident FROM user_halls " .
                     "WHERE (user_id=%d)",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                // Constraint only applies if the user is resident.
                if ($r['resident']) {
                    $rdate_from = date_create($r['date_from']);
                    $rdate_to = date_create($r['date_to']);
                    if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                        return FALSE;
                    }
                }
            }
        }

        // Next check whether a hostel already exists in the range of years provided.
        $q = sprintf("SELECT date_from, date_to FROM user_hostels " .
                     "WHERE (user_id=%d)",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                $rdate_from = date_create($r['date_from']);
                $rdate_to = date_create($r['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, the things are OK.
        $q = sprintf("INSERT INTO user_hostels (user_id, hostel_id, date_from, date_to) " .
                     "VALUES (%d, %d, %s, %s)",
                     $_SESSION['user_id'], $data['hostel_id'],
                     $this->db->escape($data['start_date']), $this->db->escape($data['end_date']));
        $this->run_query($q);

        return TRUE;
    }

    public function update_college($data)
    {
        // First make sure that what we are trying to update exists before attempting the edit.
        $q = sprintf("SELECT college_id FROM user_colleges " .
                     "WHERE (user_id=%d AND college_id=%d AND date_from='%s' AND date_to='%s') " .
                     "LIMIT 1",
                     $_SESSION['user_id'], $data['college_id'], $data['old_start_date'], $data['old_end_date']);
        $query = $this->run_query($q);
        if ($query->num_rows() != 1) {
            return FALSE;
        }

        // If we have reached this point, then the college exists.
        // Next check whether a college already exists in the range of years provided.
        $q = sprintf("SELECT date_from, date_to FROM user_colleges " .
                     "WHERE (user_id=%d AND college_id !=%d)",
                     $_SESSION['user_id'], $data['college_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                $rdate_from = date_create($r['date_from']);
                $rdate_to = date_create($r['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                    return FALSE;
                }
            }
        }

        // Get the id in the user_schools table.
        $q = sprintf("SELECT id FROM user_schools " .
                     "WHERE (user_id=%d AND school_id=%d AND date_from='%s' AND date_to='%s') " .
                     "LIMIT 1",
                     $_SESSION['user_id'], $data['school_id'], $data['old_start_date'], $data['old_end_date']);
        $user_school_id = $this->run_query($q)->row_array()['id'];

        // If we have reached this point, then attempt the update.
        $q = sprintf("UPDATE user_colleges SET date_from='%s', date_to='%s' WHERE (id=%d)",
                     $data['start_date'], $data['end_date'], $data['user_college_id']);
        $this->run_query($q);

        // Also update the user_schools table.
        $q = sprintf("UPDATE user_schools SET date_from='%s', date_to='%s' WHERE (id=%d)",
                     $data['start_date'], $data['end_date'], $user_school_id);
        $this->run_query($q);

        return TRUE;
    }

    public function update_programme($data)
    {
        // First make sure that what we are trying to update exists before attempting the edit.
        $q = sprintf("SELECT programme_id FROM user_programmes " .
                     "WHERE (user_id=%d AND programme_id=%d AND date_from='%s' AND date_to='%s') " .
                     "LIMIT 1",
                     $_SESSION['user_id'], $data['programme_id'], $data['start_date'], $data['end_date']);
        $query = $this->run_query($q);
        if ($query->num_rows() != 1) {
            return FALSE;
        }

        // If we have reached this point, then the college exists.
        // Next check whether a programme already exists in the range of years provided.
        $q = sprintf("SELECT date_from, date_to FROM user_programmes " .
                     "WHERE (user_id=%d AND programme_id !=%d)",
                     $_SESSION['user_id'], $data['programme_id']);
        $query = $this->run_query($q);
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            $data_date_from = date_create($data['start_date']);
            $data_date_to = date_create($data['end_date']);
            foreach ($results as $r) {
                $rdate_from = date_create($r['date_from']);
                $rdate_to = date_create($r['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, then attempt the update.
        $q = sprintf("UPDATE user_programmes SET year_of_study='%d' WHERE (id=%d)",
                     $data['year_of_study'], $data['user_programme_id']);
        $this->run_query($q);

        return TRUE;
    }
}
