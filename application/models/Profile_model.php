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

    private function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
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
        // Get the country and district.
        $origin_sql = sprintf("SELECT country_id, district_id " .
                                "FROM user_profile " .
                                "WHERE (user_id = %d)",
                                $user_id);
        $origin_query = $this->run_query($origin_sql);
        if ($origin_query->num_rows() === 0) {
            $data = array(
                'country'=>NULL,
                'district'=>NULL
            );
        }
        else {
            $data = $origin_query->row_array();

            // Get the name of the country.
            if ($data['country_id']) {
                $country_sql = sprintf("SELECT country_name " .
                                        "FROM countries " .
                                        "WHERE (country_id = %d)",
                                        $data['country_id']);
                $country_query = $this->run_query($country_sql);
                $data['country'] = $country_query->row_array()['country_name'];
            }
            else {
                $data['country'] = NULL;
            }

            // Get the name of the district.
            if ($data['district_id']) {
                $district_sql = sprintf("SELECT district_name " .
                                        "FROM districts " .
                                        "WHERE (district_id = %d)",
                                        $data['district_id']);
                $district_query = $this->run_query($district_sql);
                $data['district'] = $district_query->row_array()['district_name'];
            }
            else {
                $data['district'] = NULL;
            }
        }

        // Get the colleges.
        $colleges_sql = sprintf("SELECT id, college_id, date_from, date_to, level, " .
                                "YEAR(date_from) AS start_year, YEAR(date_to) AS end_year " .
                                "FROM user_colleges " .
                                "WHERE (user_id = %d) " .
                                "ORDER BY date_to DESC",
                                $user_id);
        $colleges_query = $this->run_query($colleges_sql);

        $data['colleges'] = array();
        if ($colleges_query->num_rows() > 0) {
            $colleges_results = $colleges_query->result_array();
            foreach ($colleges_results as $r) {
                // Get the college name.
                $college_sql = sprintf("SELECT college_name " .
                                        "FROM colleges " .
                                        "WHERE (college_id = %d)",
                                        $r['college_id']);
                $college_query = $this->run_query($college_sql);
                $r['college_name'] = $college_query->row_array()['college_name'];

                array_push($data['colleges'], $r);
            }
        }

        // Get the schools.
        $schools_sql = sprintf("SELECT school_id, date_from, date_to " .
                                "FROM user_schools " .
                                "WHERE (user_id = %d) " .
                                "ORDER BY date_to DESC",
                                $user_id);
        $schools_query = $this->run_query($schools_sql);

        $data['schools'] = array();
        if ($schools_query->num_rows() > 0) {
            $schools_results = $schools_query->result_array();
            foreach ($schools_results as $r) {
                // Get the school name.
                $school_sql = sprintf("SELECT school_name " .
                                        "FROM schools " .
                                        "WHERE (school_id = %d)",
                                        $r['school_id']);
                $school_query = $this->run_query($school_sql);
                $r['school_name'] = $school_query->row_array()['school_name'];

                array_push($data['schools'], $r);
            }
        }

        // Get the programmes.
        $programmes_sql = sprintf("SELECT id, programme_id, date_from, date_to " .
                                    "FROM user_programmes " .
                                    "WHERE (user_id = %d) " .
                                    "ORDER BY date_to DESC",
                                    $user_id);
        $programmes_query = $this->run_query($programmes_sql);

        $data['programmes'] = array();
        if ($programmes_query->num_rows() > 0) {
            $programmes_results = $programmes_query->result_array();
            foreach ($programmes_results as $r) {
                // Get the programme name.
                $programme_sql = sprintf("SELECT programme_name " .
                                            "FROM programmes " .
                                            "WHERE (programme_id = %d)",
                                            $r['programme_id']);
                $programme_query = $this->run_query($programme_sql);
                $r['programme_name'] = $programme_query->row_array()['programme_name'];

                array_push($data['programmes'], $r);
            }
        }

        // Get the halls.
        $halls_sql = sprintf("SELECT id, hall_id, date_from, date_to, " .
                                "YEAR(date_from) AS start_year, YEAR(date_to) AS end_year, resident " .
                                "FROM user_halls " .
                                "WHERE (user_id = %d) " .
                                "ORDER BY date_to DESC",
                                $user_id);
        $halls_query = $this->run_query($halls_sql);

        $data['halls'] = array();
        if ($halls_query->num_rows() > 0) {
            $halls_results = $halls_query->result_array();
            foreach ($halls_results as $r) {
                // Get the hall name.
                $hall_sql = sprintf("SELECT hall_name " .
                                    "FROM halls " .
                                    "WHERE (hall_id = %d)",
                                    $r['hall_id']);
                $hall_query = $this->run_query($hall_sql);
                $r['hall_name'] = $hall_query->row_array()['hall_name'];

                array_push($data['halls'], $r);
            }
        }

        // Get the hostels.
        $hostels_sql = sprintf("SELECT id, hostel_id, date_from, date_to, " .
                                "YEAR(date_from) AS start_year, YEAR(date_to) AS end_year " .
                                "FROM user_hostels " .
                                "WHERE (user_id = %d) " .
                                "ORDER BY date_to DESC",
                                $user_id);
        $hostels_query = $this->run_query($hostels_sql);

        $data['hostels'] = array();
        if ($hostels_query->num_rows() > 0) {
            $hostels_results = $hostels_query->result_array();
            foreach ($hostels_results as $r) {
                // Get the hostel name.
                $hostel_sql = sprintf("SELECT hostel_name " .
                                        "FROM hostels " .
                                        "WHERE (hostel_id = %d)",
                                        $r['hostel_id']);
                $hostel_query = $this->run_query($hostel_sql);
                $r['hostel_name'] = $hostel_query->row_array()['hostel_name'];

                array_push($data['hostels'], $r);
            }
        }

        return $data;
    }

    public function get_halls()
    {
        $halls_sql = sprintf("SELECT hall_id, hall_name FROM halls");
        $halls_query = $this->run_query($halls_sql);

        return $halls_query->result_array();
    }

    public function get_hostels()
    {
        $hostels_sql = sprintf("SELECT hostel_id, hostel_name FROM hostels");
        $hostels_query = $this->run_query($hostels_sql);

        return $hostels_query->result_array();
    }

    /**
     * Gets the programmes under a school so that a user can add it as his programme.
     * @return programmes offered under a particular school.
     */
    public function get_programmes($school_id)
    {
        // Get all programmes under that school.
        $programmes_sql = sprintf("SELECT programme_id, programme_name " .
                                    "FROM programmes " .
                                    "WHERE (school_id = %d)",
                                    $school_id);
        $programmes_query = $this->run_query($programmes_sql);

        return $programmes_query->result_array();
    }

    /**
     * @param $user_college_id: The ID in the user_colleges table.
     */
    public function get_user_college($user_college_id)
    {
        $user_college_sql = sprintf("SELECT id, college_id, date_from, date_to, " .
                                    "DAY(date_from) AS start_day, MONTH(date_from) AS start_month, " .
                                    "YEAR(date_from) AS start_year, DAY(date_to) AS end_day, " .
                                    "MONTH(date_to) AS end_month, YEAR(date_to) AS end_year " .
                                    "FROM user_colleges " .
                                    "WHERE (id = %d AND user_id = %d)",
                                    $user_college_id, $_SESSION['user_id']);
        $user_college_query = $this->run_query($user_college_sql);

        if ($user_college_query->num_rows() == 0) {
            return FALSE;
        }

        // Get the name of the college.
        $user_college = $user_college_query->row_array();
        $college_name_sql = sprintf("SELECT college_name " .
                                        "FROM colleges " .
                                        "WHERE (college_id = %d)",
                                        $user_college['college_id']);
        $college_name_query = $this->run_query($college_name_sql);
        $user_college['college_name'] = $college_name_query->row_array()['college_name'];

        // Get the school id.
        $school_id_sql = sprintf("SELECT school_id " .
                                    "FROM user_schools " .
                                    "WHERE (user_id = %d AND date_from = '%s' AND date_to = '%s')",
                                    $_SESSION['user_id'], $user_college['date_from'],
                                    $user_college['date_to']);
        $school_id_query = $this->run_query($school_id_sql);
        $user_college['school']['school_id'] = $school_id_query->row_array()['school_id'];

        // Get the name of the school.
        $school_name_sql = sprintf("SELECT school_name " .
                                    "FROM schools " .
                                    "WHERE (school_id = %d)",
                                    $user_college['school']['school_id']);
        $school_name_query = $this->run_query($school_name_sql);
        $user_college['school']['school_name'] = $school_name_query->row_array()['school_name'];

        return $user_college;
    }

    /**
     * @param $user_programme_id: The ID of this entry in the user_programmes table.
     */
    public function get_user_programme($user_programme_id)
    {
        $user_programme_sql = sprintf("SELECT id, programme_id, date_from, date_to, year_of_study " .
                                        "FROM user_programmes " .
                                        "WHERE (id = %d AND user_id = %d)",
                                        $user_programme_id, $_SESSION['user_id']);
        $user_programme_query = $this->run_query($user_programme_sql);

        if ($user_programme_query->num_rows() == 0) {
            return FALSE;
        }

        $user_programme = $user_programme_query->row_array();

        // Get the name of the programme.
        $programme_sql = sprintf("SELECT programme_name " .
                                    "FROM programmes " .
                                    "WHERE (programme_id = %d)",
                                    $user_programme['programme_id']);
        $programme_query = $this->run_query($programme_sql);
        $user_programme['programme_name'] = $programme_query->row_array()['programme_name'];

        return $user_programme;
    }

    public function get_user_hall($user_hall_id)
    {
        $user_hall_sql = sprintf("SELECT id, hall_id, date_from, date_to, resident, " .
                                    "DAY(date_from) AS start_day, MONTH(date_from) AS start_month, " .
                                    "YEAR(date_from) AS start_year, DAY(date_to) AS end_day, " .
                                    "MONTH(date_to) AS end_month, YEAR(date_to) AS end_year " .
                                    "FROM user_halls " .
                                    "WHERE (id = %d AND user_id = %d)",
                                    $user_hall_id, $_SESSION['user_id']);
        $user_hall_query = $this->run_query($user_hall_sql);

        if ($user_hall_query->num_rows() == 0) {
            return FALSE;
        }

        $user_hall = $user_hall_query->row_array();
        $hall_name_sql = sprintf("SELECT hall_name " .
                                    "FROM halls " .
                                    "WHERE (hall_id = %d)",
                                    $user_hall['hall_id']);
        $hall_name_query = $this->run_query($hall_name_sql);
        $user_hall['hall_name'] = $hall_name_query->row_array()['hall_name'];

        return $user_hall;
    }

    public function get_user_hostel($user_hostel_id)
    {
        $user_hostel_sql = sprintf("SELECT id, hostel_id, date_from, date_to, " .
                                    "DAY(date_from) AS start_day, MONTH(date_from) AS start_month, " .
                                    "YEAR(date_from) AS start_year, DAY(date_to) AS end_day, " .
                                    "MONTH(date_to) AS end_month, YEAR(date_to) AS end_year " .
                                    "FROM user_hostels " .
                                    "WHERE (id = %d AND user_id = %d)",
                                    $user_hostel_id, $_SESSION['user_id']);
        $user_hostel_query = $this->run_query($user_hostel_sql);

        if ($user_hostel_query->num_rows() == 0) {
            return FALSE;
        }

        $user_hostel = $user_hostel_query->row_array();

        $hostel_name_sql = sprintf("SELECT hostel_name " .
                                    "FROM hostels " .
                                    "WHERE (hostel_id = %d)",
                                    $user_hostel['hostel_id']);
        $hostel_name_query = $this->run_query($hostel_name_sql);
        $user_hostel['hostel_name'] = $hostel_name_query->row_array()['hostel_name'];

        return $user_hostel;
    }

    public function get_colleges()
    {
        $colleges_sql = sprintf("SELECT college_id, college_name FROM colleges");
        $colleges_query = $this->run_query($colleges_sql);

        return $colleges_query->result_array();
    }

    public function college_and_school_exists($college_id, $school_id)
    {
        $school_sql = sprintf("SELECT school_id " .
                                "FROM schools " .
                                "WHERE (school_id = %d AND college_id = %d) " .
                                "LIMIT 1",
                                $school_id, $college_id);
        $school_query = $this->run_query($school_sql);
        return ($school_query->num_rows() == 1);
    }

    public function get_schools()
    {
        $schools_sql = sprintf("SELECT school_id, college_id, school_name FROM schools");
        $schools_query = $this->run_query($schools_sql);

        return $schools_query->result_array();
    }

    public function add_college($data)
    {
        // First check whether a college already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $dates_sql = sprintf("SELECT date_from, date_to " .
                                "FROM user_colleges " .
                                "WHERE (user_id = %d)",
                                $_SESSION['user_id']);
        $dates_query = $this->run_query($dates_sql);
        $dates_results = $dates_query->result_array();
        foreach ($dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then the years are OK.
        $add_college_sql = sprintf("INSERT INTO user_colleges " .
                                    "(user_id, college_id, date_from, date_to) " .
                                    "VALUES (%d, %d, %s, %s)",
                                    $_SESSION['user_id'], $data['college_id'],
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']));
        $this->run_query($add_college_sql);

        // Also add the school.
        $add_school_sql = sprintf("INSERT INTO user_schools " .
                                    "(user_id, school_id, date_from, date_to ) " .
                                    "VALUES (%d, %d, %s, %s)",
                                    $_SESSION['user_id'], $data['school_id'],
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']));
        $this->run_query($add_school_sql);

        return TRUE;
    }

    public function get_countries()
    {
        $countries_sql = sprintf("SELECT country_id, country_name FROM countries");
        $countries_query = $this->run_query($countries_sql);

        return $countries_query->result_array();
    }

    public function add_country($country_id)
    {
        $profile_sql = sprintf("UPDATE user_profile " .
                                "SET country_id = %d " .
                                "WHERE user_id = %d",
                                $country_id, $_SESSION['user_id']);
        $this->run_query($profile_sql);
    }

    public function get_districts($district)
    {
        $keywords = preg_split("/[\s,]+/", $district);
        for ($i=0; $i!=count($keywords); ++$i) {
            $keywords[$i] = "+{$keywords[$i]}";
        }

        $key = '';
        foreach ($keywords as $keyword) {
            $key .= "{$keyword} ";
        }

        $districts_sql = sprintf("SELECT district_id, district_name " .
                                    "FROM districts " .
                                    "WHERE MATCH(district_name) AGAINST (%s IN BOOLEAN MODE)",
                                    $this->db->escape($key));
        $districts_query = $this->run_query($districts_sql);

        return $districts_query->result_array();
    }

    public function add_district($district_id)
    {
        // First check if a district with that id exists.
        $district_sql = sprintf("SELECT district_name " .
                                "FROM districts " .
                                "WHERE (district_id = %d)",
                                $district_id);
        $district_query = $this->run_query($district_sql);
        if ($district_query->num_rows() == 0) {
            return FALSE;
        }

        // Next check if this user hasn't added district already.
        $profile_sql = sprintf("SELECT district_id " .
                                "FROM user_profile " .
                                "WHERE (user_id = %d AND district_id IS NOT NULL)",
                                $_SESSION['user_id']);
        $profile_query = $this->run_query($profile_sql);
        if ($profile_query->num_rows() != 0) {
            return FALSE;
        }

        $profile_sql = sprintf("UPDATE user_profile " .
                                "SET district_id = %d " .
                                "WHERE user_id = %d",
                                $district_id, $_SESSION['user_id']);
        $this->run_query($profile_sql);

        return TRUE;
    }

    public function add_programme($data)
    {
        $user_college = $this->get_user_college($data['user_college_id']);

        // Save the programme.
        $programme_sql = sprintf("INSERT INTO user_programmes " .
                                "(user_id, programme_id, date_from, date_to, year_of_study) " .
                                "VALUES(%d, %d, %s, %s, '%d')",
                                $_SESSION['user_id'], $data['programme_id'],
                                $this->db->escape($user_college['date_from']),
                                $this->db->escape($user_college['date_to']),
                                $data['year_of_study']);
        $this->run_query($programme_sql);
    }

    public function add_hall($data)
    {
        // First check whether a hall already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $hall_dates_sql = sprintf("SELECT date_from, date_to " .
                                    "FROM user_halls " .
                                    "WHERE (user_id = %d)",
                                    $_SESSION['user_id']);

        $hall_dates_results = $this->run_query($dates_sql)->result_array();
        foreach ($hall_dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // Next, check whether a hostel already exists in the range of years
        // provided but the user is claiming that he's resident.
        if ($data['resident'] == 1) {
            $hostel_dates_sql = sprintf("SELECT date_from, date_to " .
                                        "FROM user_hostels " .
                                        "WHERE (user_id = %d)",
                                        $_SESSION['user_id']);
            $hostel_dates_results = $this->run_query($hostel_dates_sql)->result_array();
            foreach ($hostel_dates_results as $d) {
                $date_from = date_create($d['date_from']);
                $date_to = date_create($d['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, then things are OK.
        $add_hall_sql = sprintf("INSERT INTO user_halls " .
                                "(user_id, hall_id, date_from, date_to, resident) " .
                                "VALUES (%d, %d, %s, %s, %d)",
                                $_SESSION['user_id'], $data['hall_id'],
                                $this->db->escape($data['start_date']),
                                $this->db->escape($data['end_date']),
                                $data['resident']);
        $this->run_query($add_hall_sql);

        return TRUE;
    }

    public function update_hall($data)
    {
        // Check whether a hall already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $dates_sql = sprintf("SELECT date_from, date_to " .
                                "FROM user_halls " .
                                "WHERE (user_id = %d AND id != %d)",
                                $_SESSION['user_id'], $data['user_hall_id']);
        $dates_query = $this->run_query($dates_sql);

        $dates_results = $dates_query->result_array();
        foreach ($dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then attempt the update.
        $update_hall_sql = sprintf("UPDATE user_halls " .
                                    "SET date_from = %s, date_to = %s, resident = %d " .
                                    "WHERE (id = %d)",
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']),
                                    $data['resident'], $data['user_hall_id']);
        $this->run_query($update_hall_sql);

        return TRUE;
    }

    public function add_hostel($data)
    {
        // First check whether a hall already exists in the range of years provided,
        // and the user is resident.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $hall_dates_sql = sprintf("SELECT date_from, date_to, resident " .
                                    "FROM user_halls " .
                                    "WHERE (user_id = %d)",
                                    $_SESSION['user_id']);
        $hall_dates_query = $this->run_query($hall_dates_sql);

        $hall_dates_results = $hall_dates_query->result_array();
        foreach ($hall_dates_results as $d) {
            // Constraint only applies if the user is resident.
            if ($d['resident']) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                    return FALSE;
                }
            }
        }

        // Next check whether a hostel already exists in the range of years provided.
        $hostel_dates_sql = sprintf("SELECT date_from, date_to " .
                                    "FROM user_hostels " .
                                    "WHERE (user_id = %d)",
                                    $_SESSION['user_id']);
        $hostel_dates_query = $this->run_query($hostel_dates_sql);

        $hostel_dates_results = $hostel_dates_query->result_array();
        foreach ($hostel_dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, the things are OK.
        $add_hostel_sql = sprintf("INSERT INTO user_hostels " .
                                    "(user_id, hostel_id, date_from, date_to) " .
                                    "VALUES (%d, %d, %s, %s)",
                                    $_SESSION['user_id'], $data['hostel_id'],
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']));
        $this->run_query($add_hostel_sql);

        return TRUE;
    }

    public function update_hostel($data)
    {
        // Check whether a hall already exists in the range of years provided,
        // and the user is resident.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $hall_dates_sql = sprintf("SELECT date_from, date_to, resident " .
                                    "FROM user_halls " .
                                    "WHERE (user_id = %d)",
                                    $_SESSION['user_id']);
        $hall_dates_query = $this->run_query($hall_dates_sql);

        $hall_dates_results = $hall_dates_query->result_array();
        foreach ($hall_dates_results as $d) {
            // Constraint only applies if the user is resident.
            if ($d['resident']) {
                $date_from = date_create($d['date_from']);
                $date_to = date_create($d['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                    return FALSE;
                }
            }
        }

        // Next, check whether a hostel already exists in the range of years provided.
        $hostel_dates_sql = sprintf("SELECT date_from, date_to " .
                                    "FROM user_hostels " .
                                    "WHERE (user_id = %d AND id != %d)",
                                    $_SESSION['user_id'], $data['user_hostel_id']);
        $hostel_dates_query = $this->run_query($hostel_dates_sql);

        $hostel_dates_results = $hostel_dates_query->result_array();
        foreach ($hostel_dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then attempt the update.
        $update_hostel_sql = sprintf("UPDATE user_hostels " .
                                        "SET date_from = %s, date_to = %s " .
                                        "WHERE (id = %d)",
                                        $this->db->escape($data['start_date']),
                                        $this->db->escape($data['end_date']),
                                        $data['user_hostel_id']);
        $this->run_query($update_hostel_sql);

        return TRUE;
    }

    public function update_college($data)
    {
        // Check whether a college already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $dates_sql = sprintf("SELECT date_from, date_to " .
                                "FROM user_colleges " .
                                "WHERE (user_id = %d AND id != %d)",
                                $_SESSION['user_id'], $data['user_college_id']);
        $dates_query = $this->run_query($dates_sql);
        $dates_results = $dates_query->result_array();
        foreach ($dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then attempt the update.
        $update_college_sql = sprintf("UPDATE user_colleges " .
                                        "SET date_from = %s, date_to = %s " .
                                        "WHERE (id = %d)",
                                        $this->db->escape($data['start_date']),
                                        $this->db->escape($data['end_date']),
                                        $data['user_college_id']);
        $this->run_query($update_college_sql);

        // Also update the user_schools table.
        // Get the id in the user_schools table.
        $school_id_sql = sprintf("SELECT id FROM user_schools " .
                                "WHERE (user_id = %d AND school_id = %d AND " .
                                "date_from = %s AND date_to = %s)",
                                $_SESSION['user_id'], $data['school_id'],
                                $this->db->escape($data['old_start_date']),
                                $this->db->escape($data['old_end_date']));
        $school_id = $this->run_query($school_id_sql)->row_array()['id'];

        $update_school_sql = sprintf("UPDATE user_schools " .
                                        "SET date_from = %s, date_to = %s " .
                                        "WHERE (id = %d)",
                                        $this->db->escape($data['start_date']),
                                        $this->db->escape($data['end_date']),
                                        $school_id);
        $this->run_query($update_school_sql);

        return TRUE;
    }

    public function update_programme($data)
    {
        $update_sql = sprintf("UPDATE user_programmes " .
                                "SET year_of_study='%d' " .
                                "WHERE (id = %d)",
                                $data['year_of_study'], $data['user_programme_id']);
        $this->run_query($update_sql);
    }
}
