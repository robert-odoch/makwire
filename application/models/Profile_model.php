<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

/**
 * Contains functions related to a user's profile.
 */
class Profile_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model', 'photo_model');
    }

    /**
     * Sets a new profile picture for a user.
     */
    public function set_profile_picture($photo_id, $user_id)
    {
        // Get photo path.
        $sql = sprintf('SELECT full_path FROM photos WHERE photo_id = %d', $photo_id);
        $query = $this->db->query($sql);
        $photo_path = $query->row_array()['full_path'];

        $last_slash = strrpos($photo_path, '/');
        $photo_directory = substr($photo_path, 0, $last_slash);
        $photo_name = substr($photo_path, $last_slash+1);

        // Update profile_pic_path in the users table.
        $profile_pic_path = "{$photo_directory}/small/{$photo_name}";
        $update_sql = sprintf("UPDATE users SET profile_pic_path = %s WHERE (user_id = %d) LIMIT 1",
                                $this->db->escape($profile_pic_path),
                                $user_id);
        $this->utility_model->run_query($update_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities
                                (actor_id, subject_id, source_id, source_type, activity)
                                VALUES (%d, %d, %d, 'photo', 'profile_pic_change')",
                                $user_id, $user_id, $photo_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Gets full profile for a user.
     *
     * @param $user_id the ID of the user in the users table.
     * @return user profile.
     */
    public function get_profile($user_id)
    {
        // Get the country and district.
        $origin_sql = sprintf("SELECT country_name, district_name FROM users u
                                LEFT JOIN districts d ON (u.district_id = d.district_id)
                                LEFT JOIN countries c ON (d.country_id = c.country_id)
                                WHERE (user_id = %d)",
                                $user_id);
        $origin_query = $this->utility_model->run_query($origin_sql);
        $data['origin'] = $origin_query->row_array();

        // Get the schools.
        $schools_sql = sprintf("SELECT us.id, us.school_id, us.date_from, us.date_to, us.level, s.school_name,
                                YEAR(date_from) AS start_year, YEAR(date_to) AS end_year
                                FROM user_schools us LEFT JOIN schools s ON (us.school_id = s.school_id)
                                WHERE (us.user_id = %d) ORDER BY date_to DESC",
                                $user_id);
        $schools_query = $this->utility_model->run_query($schools_sql);

        $data['schools'] = $schools_query->result_array();
        foreach ($data['schools'] as &$s) {
            // Get the college name.
            $college_sql = sprintf("SELECT college_name FROM colleges c
                                    LEFT JOIN schools s ON (c.college_id = s.college_id)
                                    WHERE (s.school_id = %d)",
                                    $s['school_id']);
            $college_query = $this->utility_model->run_query($college_sql);
            if ($college_query->num_rows() == 0) {
                $s['college_name'] = NULL;
            }
            else {
                $s['college_name'] = $college_query->row_array()['college_name'];
            }

            // Get the programme.
            $programme_sql = sprintf("SELECT up.id, p.programme_name FROM user_programmes up
                                        LEFT JOIN programmes p ON (up.programme_id = p.programme_id)
                                        WHERE (up.user_school_id = %d)",
                                        $s['id']);
            $programme_query = $this->utility_model->run_query($programme_sql);
            $s['has_programme'] = FALSE;
            if ($programme_query->num_rows() > 0) {
                $s['has_programme'] = TRUE;
                $s['programme'] = $programme_query->row_array();
            }
        }
        unset($s);

        // Get the halls.
        $halls_sql = sprintf("SELECT id, date_from, date_to, YEAR(date_from) AS start_year,
                                YEAR(date_to) AS end_year, resident, h.hall_name
                                FROM user_halls uh
                                LEFT JOIN halls h ON(uh.hall_id = h.hall_id)
                                WHERE (uh.user_id = %d)
                                ORDER BY date_to DESC",
                                $user_id);
        $halls_query = $this->utility_model->run_query($halls_sql);
        $data['halls'] = $halls_query->result_array();

        // Get the hostels.
        $hostels_sql = sprintf("SELECT id, date_from, date_to, YEAR(date_from) AS start_year,
                                YEAR(date_to) AS end_year, h.hostel_name
                                FROM user_hostels uh
                                LEFT JOIN hostels h ON(uh.hostel_id = h.hostel_id)
                                WHERE (user_id = %d)
                                ORDER BY date_to DESC",
                                $user_id);
        $hostels_query = $this->utility_model->run_query($hostels_sql);
        $data['hostels'] = $hostels_query->result_array();

        return $data;
    }

    /**
     * Gets all schools from the schools table.
     *
     * @return all schools in the schools table.
     */
    public function get_schools()
    {
        $schools_sql = sprintf("SELECT school_id, college_id, school_name FROM schools");
        $schools_query = $this->utility_model->run_query($schools_sql);

        return $schools_query->result_array();
    }

    /**
     * Gets the programmes under a school so that a user can add it as his programme.
     *
     * @param $school_id the ID of shool in the schools table.
     * @return programmes offered under a particular school.
     */
    public function get_programmes($school_id)
    {
        // Get all programmes under that school.
        $programmes_sql = sprintf("SELECT programme_id, programme_name FROM programmes WHERE (school_id = %d)",
                                    $school_id);
        $programmes_query = $this->utility_model->run_query($programmes_sql);

        return $programmes_query->result_array();
    }

    /**
     * Gets all halls from the halls table.
     *
     * @return all halls in the halls table.
     */
    public function get_halls()
    {
        $halls_sql = sprintf("SELECT hall_id, hall_name FROM halls");
        $halls_query = $this->utility_model->run_query($halls_sql);

        return $halls_query->result_array();
    }

    /**
     * Gets all hostels from the hostels table.
     *
     * @return all hostels in the hostels table.
     */
    public function get_hostels()
    {
        $hostels_sql = sprintf("SELECT hostel_id, hostel_name FROM hostels");
        $hostels_query = $this->utility_model->run_query($hostels_sql);

        return $hostels_query->result_array();
    }

    /**
     * Gets all districts from the districts table matching the name of a district
     * entered by a user.
     *
     * @param $district the name of a district entered by a user.
     * @return all districts from the districts table matching $district.
     */
    public function get_searched_district($district)
    {
        $keywords = preg_split("/[\s,]+/", $district);
        foreach ($keywords as &$keyword) {
            $keyword = strtolower("+{$keyword}");
        }
        unset($keyword);

        $key = implode(' ', $keywords);
        $districts_sql = sprintf("SELECT district_id, district_name FROM districts
                                    WHERE MATCH(district_name) AGAINST (%s IN BOOLEAN MODE)",
                                    $this->db->escape($key));
        $districts_query = $this->utility_model->run_query($districts_sql);

        return $districts_query->result_array();
    }

    /**
     * Gets details for a college attended by a user.
     *
     * Throws NotFoundException if no matching record is found.
     *
     * @param $user_college_id the ID of the record in the user_colleges table.
     * @return details for a college attended by a user.
     */
    public function get_user_school($user_school_id, $user_id)
    {
        $user_school_sql = sprintf("SELECT s.school_name, us.id, us.school_id,
                                    DAY(us.date_from) AS start_day, MONTH(us.date_from) AS start_month,
                                    YEAR(us.date_from) AS start_year, DAY(us.date_to) AS end_day,
                                    MONTH(us.date_to) AS end_month, YEAR(us.date_to) AS end_year
                                    FROM user_schools us
                                    LEFT JOIN schools s ON (us.school_id = s.school_id)
                                    WHERE (us.id = %d AND us.user_id = %d)",
                                    $user_school_id, $user_id);
        $user_school_query = $this->utility_model->run_query($user_school_sql);

        if ($user_school_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $user_school = $user_school_query->row_array();
        return $user_school;
    }

    /**
     * Gets details for a programme studied by a user.
     *
     * Throws NotFoundException if no matching record is found.
     *
     * @param $user_programme_id the ID of the record in the user_programmes table.
     * @return details for a programme studied by a user.
     */
    public function get_user_programme($user_programme_id, $user_id)
    {
        $user_programme_sql = sprintf("SELECT up.id, up.programme_id, up.year_of_study, p.programme_name
                                        FROM user_programmes up
                                        LEFT JOIN programmes p ON(up.programme_id = p.programme_id)
                                        WHERE (up.id = %d AND up.user_id = %d)",
                                        $user_programme_id, $user_id);
        $user_programme_query = $this->utility_model->run_query($user_programme_sql);

        if ($user_programme_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        return $user_programme_query->row_array();
    }

    /**
     * Gets details for a hall where a user was attached to/resident of.
     *
     * Throws NotFoundException if no matching record is found.
     *
     * @param $user_hall_id the ID Of the record in the user_halls table.
     * @return the details for a hall where a user was attached to/resident of.
     */
    public function get_user_hall($user_hall_id, $user_id)
    {
        $user_hall_sql = sprintf("SELECT id, uh.hall_id,  date_from, date_to, resident,
                                    DAY(date_from) AS start_day, MONTH(date_from) AS start_month,
                                    YEAR(date_from) AS start_year, DAY(date_to) AS end_day,
                                    MONTH(date_to) AS end_month, YEAR(date_to) AS end_year, h.hall_name
                                    FROM user_halls uh
                                    LEFT JOIN halls h ON(uh.hall_id = h.hall_id)
                                    WHERE (id = %d AND user_id = %d)",
                                    $user_hall_id, $user_id);
        $user_hall_query = $this->utility_model->run_query($user_hall_sql);

        if ($user_hall_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $user_hall = $user_hall_query->row_array();
        return $user_hall;
    }

    /**
     * Gets details for a hostel where a user stayed at.
     *
     * Throws NotFoundException if no matching record is found.
     *
     * @param $user_hostel_id the ID of the record in the user_hostels table.
     * @return details for a hostel where a user stayed at.
     */
    public function get_user_hostel($user_hostel_id, $user_id)
    {
        $user_hostel_sql = sprintf("SELECT id, uh.hostel_id, date_from, date_to,
                                    DAY(date_from) AS start_day, MONTH(date_from) AS start_month,
                                    YEAR(date_from) AS start_year, DAY(date_to) AS end_day,
                                    MONTH(date_to) AS end_month, YEAR(date_to) AS end_year, hostel_name
                                    FROM user_hostels uh
                                    LEFT JOIN hostels h ON(uh.hostel_id = h.hostel_id)
                                    WHERE (id = %d AND user_id = %d)",
                                    $user_hostel_id, $user_id);
        $user_hostel_query = $this->utility_model->run_query($user_hostel_sql);

        if ($user_hostel_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $user_hostel = $user_hostel_query->row_array();
        return $user_hostel;
    }

    /**
     * Adds a college which a user attended to a his proifle.
     *
     * @param $data an array of details about the college.
     */
    public function add_school($user_id, $data)
    {
        // First check whether a school already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $dates_sql = sprintf("SELECT date_from, date_to FROM user_schools WHERE (user_id = %d)",
                                $user_id);
        $dates_query = $this->utility_model->run_query($dates_sql);
        $dates_results = $dates_query->result_array();
        foreach ($dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then the years are OK.
        $add_college_sql = sprintf("INSERT INTO user_schools (user_id, school_id, date_from, date_to)
                                    VALUES (%d, %d, %s, %s)",
                                    $user_id, $data['school_id'],
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']));
        $this->utility_model->run_query($add_college_sql);

        return TRUE;
    }

    /**
     * Adds a user's programme of study to his proifle.
     *
     * @param $data an array of details for a programme.
     */
    public function add_programme($user_id, $data)
    {
        // Save the programme.
        $programme_sql = sprintf("INSERT INTO user_programmes
                                    (user_id, programme_id, user_school_id, year_of_study, graduated)
                                    VALUES(%d, %d, %d, '%d', %d)",
                                    $user_id, $data['programme_id'], $data['user_school_id'],
                                    $data['year_of_study'], $data['graduated']);
        $this->utility_model->run_query($programme_sql);
    }

    /**
     * Adds a user's hall of residence/attachment to a his profile.
     *
     * @param $data an array of details for a hall.
     */
    public function add_hall($user_id, $data)
    {
        // First check whether a hall already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $hall_dates_sql = sprintf("SELECT date_from, date_to FROM user_halls WHERE (user_id = %d)",
                                    $user_id);

        $hall_dates_results = $this->utility_model->run_query($hall_dates_sql)->result_array();
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
            $hostel_dates_sql = sprintf("SELECT date_from, date_to FROM user_hostels WHERE (user_id = %d)",
                                        $user_id);
            $hostel_dates_results = $this->utility_model->run_query($hostel_dates_sql)->result_array();
            foreach ($hostel_dates_results as $d) {
                $date_from = date_create($d['date_from']);
                $date_to = date_create($d['date_to']);
                if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                    return FALSE;
                }
            }
        }

        // If we have reached this point, then things are OK.
        $add_hall_sql = sprintf("INSERT INTO user_halls (user_id, hall_id, date_from, date_to, resident)
                                VALUES (%d, %d, %s, %s, %d)",
                                $user_id, $data['hall_id'],
                                $this->db->escape($data['start_date']),
                                $this->db->escape($data['end_date']),
                                $data['resident']);
        $this->utility_model->run_query($add_hall_sql);

        return TRUE;
    }

    /**
     * Adds a hostel where a user stayed at to his profile.
     *
     * @param $data an array of details about the hostel.
     */
    public function add_hostel($user_id, $data)
    {
        // First check whether a hall already exists in the range of years provided,
        // and the user is resident.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $hall_dates_sql = sprintf("SELECT date_from, date_to, resident FROM user_halls WHERE (user_id = %d)",
                                    $user_id);
        $hall_dates_query = $this->utility_model->run_query($hall_dates_sql);

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
        $hostel_dates_sql = sprintf("SELECT date_from, date_to FROM user_hostels WHERE (user_id = %d)",
                                    $user_id);
        $hostel_dates_query = $this->utility_model->run_query($hostel_dates_sql);

        $hostel_dates_results = $hostel_dates_query->result_array();
        foreach ($hostel_dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, the things are OK.
        $add_hostel_sql = sprintf("INSERT INTO user_hostels (user_id, hostel_id, date_from, date_to)
                                    VALUES (%d, %d, %s, %s)",
                                    $user_id, $data['hostel_id'],
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']));
        $this->utility_model->run_query($add_hostel_sql);

        return TRUE;
    }

    /**
     * Adds a district where a user if from to his profile.
     *
     * @param $district_id the ID of the district in the districts table.
     */
    public function add_district($user_id, $district_id)
    {
        // Get the country ID.
        $country_sql = sprintf('SELECT country_id FROM districts WHERE district_id = %d',
                                $district_id);
        $country_query = $this->db->query($country_sql);
        if ($country_query->num_rows() == 0) {
            return FALSE;
        }

        $country_id = $country_query->row()->country_id;

        // Next check if this user hasn't added district already.
        $profile_sql = sprintf("SELECT district_id FROM users
                                WHERE (user_id = %d AND district_id IS NOT NULL)",
                                $user_id);
        $profile_query = $this->utility_model->run_query($profile_sql);
        if ($profile_query->num_rows() != 0) {
            return FALSE;
        }

        $profile_sql = sprintf("UPDATE users SET district_id = %d WHERE user_id = %d",
                                $district_id, $user_id);
        $this->utility_model->run_query($profile_sql);

        return TRUE;
    }

    /**
     * Updates the details about a college which a user attended.
     *
     * @param $data an array of details to be updated.
     */
    public function update_school($user_id, $data)
    {
        // Check whether a school already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $dates_sql = sprintf("SELECT date_from, date_to FROM user_schools
                                WHERE (user_id = %d AND id != %d)",
                                $user_id, $data['user_school_id']);
        $dates_query = $this->utility_model->run_query($dates_sql);
        $dates_results = $dates_query->result_array();
        foreach ($dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then attempt the update.
        $update_school_sql = sprintf("UPDATE user_schools SET date_from = %s, date_to = %s WHERE (id = %d)",
                                        $this->db->escape($data['start_date']),
                                        $this->db->escape($data['end_date']),
                                        $data['user_school_id']);
        $this->utility_model->run_query($update_school_sql);

        return TRUE;
    }

    /**
     * Updates the datails about a programme a user studied.
     *
     * @param $data an array of details to be updated.
     */
    public function update_programme($data)
    {
        $update_sql = sprintf("UPDATE user_programmes
                                SET year_of_study = '%d', graduated = %d
                                WHERE (id = %d)", $data['year_of_study'],
                                $data['graduated'], $data['user_programme_id']);
        $this->utility_model->run_query($update_sql);
    }

    /**
     * Updates the detials about a user's hall.
     *
     * Things updated include start date and end date.
     *
     * @param $data an array of details to be updated.
     */
    public function update_hall($user_id, $data)
    {
        // Check whether a hall already exists in the range of years provided.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $dates_sql = sprintf("SELECT date_from, date_to FROM user_halls.
                                WHERE (user_id = %d AND id != %d)",
                                $user_id, $data['user_hall_id']);
        $dates_query = $this->utility_model->run_query($dates_sql);

        $dates_results = $dates_query->result_array();
        foreach ($dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then attempt the update.
        $update_hall_sql = sprintf("UPDATE user_halls
                                    SET date_from = %s, date_to = %s, resident = %d
                                    WHERE (id = %d)",
                                    $this->db->escape($data['start_date']),
                                    $this->db->escape($data['end_date']),
                                    $data['resident'], $data['user_hall_id']);
        $this->utility_model->run_query($update_hall_sql);

        return TRUE;
    }

    /**
     * Updates the datails about a user's hostel.
     *
     * @param $data an array of update details.
     */
    public function update_hostel($user_id, $data)
    {
        // Check whether a hall already exists in the range of years provided,
        // and the user is resident.
        $data_date_from = date_create($data['start_date']);
        $data_date_to = date_create($data['end_date']);

        $hall_dates_sql = sprintf("SELECT date_from, date_to, resident FROM user_halls WHERE (user_id = %d)",
                                    $user_id);
        $hall_dates_query = $this->utility_model->run_query($hall_dates_sql);

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
        $hostel_dates_sql = sprintf("SELECT date_from, date_to FROM user_hostels WHERE (user_id = %d AND id != %d)",
                                    $user_id, $data['user_hostel_id']);
        $hostel_dates_query = $this->utility_model->run_query($hostel_dates_sql);

        $hostel_dates_results = $hostel_dates_query->result_array();
        foreach ($hostel_dates_results as $d) {
            $date_from = date_create($d['date_from']);
            $date_to = date_create($d['date_to']);
            if ($this->are_conflicting_dates($data_date_from, $data_date_to, $date_from, $date_to)) {
                return FALSE;
            }
        }

        // If we have reached this point, then attempt the update.
        $update_hostel_sql = sprintf("UPDATE user_hostels SET date_from = %s, date_to = %s WHERE (id = %d)",
                                        $this->db->escape($data['start_date']),
                                        $this->db->escape($data['end_date']),
                                        $data['user_hostel_id']);
        $this->utility_model->run_query($update_hostel_sql);

        return TRUE;
    }

    public function get_profile_questions($user_id)
    {
        $profile_questions = [];

        // Check whether the user has'nt graduated from a programme,
        // and year of study was last updated one year ago.
        $programmes_sql = sprintf("SELECT id, last_updated FROM user_programmes
                                    WHERE (user_id = %d AND graduated IS FALSE)",
                                    $user_id);
        $programmes_query = $this->utility_model->run_query($programmes_sql);
        $programmes_result = $programmes_query->result_array();
        foreach ($programmes_result as $pr) {
            $last_updated = new DateTime();
            $last_updated->setTimestamp(mysql_to_unix($pr['last_updated']));
            $now = new DateTime();
            $now->setTimestamp(now());
            if ($now->diff($last_updated)->y >= 1) {
                $qtn = '<a href="' . base_url("profile/edit-programme/{$pr['id']}") .
                        '">Which year of study are you in?</a>';
                array_push($profile_questions, $qtn);
            }
        }

        return $profile_questions;
    }

    /**
     * Checks whether two date ranges overlap with each other.
     *
     * @param $data_date_from user submitted start date.
     * @param $data_date_to user submitted end date
     * @param $rdate_from start date from the database.
     * @param $rdate_to end date from the database.
     * @return TRUE if the dates overlap.
     */
    private function are_conflicting_dates($data_date_from, $data_date_to, $rdate_from, $rdate_to)
    {
        if (($data_date_from < $rdate_from) && ($data_date_to > $rdate_from) ||
            ($data_date_from < $rdate_to) && ($data_date_to > $rdate_to) ||
            ($data_date_from >= $rdate_from) && ($data_date_to <= $rdate_to)) {
            return TRUE;
        }

        return FALSE;
    }
}
