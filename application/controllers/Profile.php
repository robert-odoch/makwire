<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'profile_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function add_college()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add your college';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['start_day'] = $this->input->post('start-day');
            $data['start_month'] = $this->input->post('start-month');
            $data['start_year'] = $this->input->post('start-year');

            $data['end_day'] = $this->input->post('end-day');
            $data['end_month'] = $this->input->post('end-month');
            $data['end_year'] = $this->input->post('end-year');

            // Validate the dates.
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }

            if (!isset($data['error_message'])) {
                $data['college_id'] = $this->input->post('college');
                $data['school_id'] = $this->input->post('school');

                // Make sure college and school exist.
                if (!$this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                    $data['error_message'] = 'Your college and school do not match!<br>' .
                                                'Please try again.';
                }
            }

            if (!isset($data['error_message'])) {
                // Try saving the college and school.
                if ($this->profile_model->add_college($data)) {
                    $this->utility_model->show_success(
                        'Your college and school have been succesfully saved.'
                    );
                    return;
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br>" .
                                                "You can't be at two colleges or schools at the same time.";
                }
            }
        }

        // User reaches here if he has just opened this page, or
        // there is an error in submitted form data.
        $data['colleges'] = $this->profile_model->get_colleges();
        $data['schools'] = $this->profile_model->get_schools();

        $data['heading'] = 'Add College';
        $data['form_action'] = base_url('profile/add-college');
        $this->load->view('edit/college', $data);
        $this->load->view('common/footer');
    }

    public function edit_college($user_college_id=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Edit your college';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // DON'T try to remove this line,
            // $user_college_id useful when re-displaying the form.
            $user_college_id = $this->input->post('user-college-id');

            $data['start_day'] = $this->input->post('start-day');
            $data['start_month'] = $this->input->post('start-month');
            $data['start_year'] = $this->input->post('start-year');

            $data['end_day'] = $this->input->post('end-day');
            $data['end_month'] = $this->input->post('end-month');
            $data['end_year'] = $this->input->post('end-year');

            $data['old_start_date'] = $this->input->post('old-start-date');
            $data['old_end_date'] = $this->input->post('old-end-date');

            // Validate the dates.
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }

            if (!isset($data['error_message'])) {
                $data['user_college_id'] = $user_college_id;
                $data['college_id'] = $this->input->post('college-id');
                $data['school_id'] = $this->input->post('school-id');

                // Check whether college and school exist.
                if (!$this->profile_model->college_and_school_exists($data['college_id'], $data['school_id'])) {
                    $data['error_message'] = 'Your college and school do not match!<br>' .
                                                'Please try again.';
                }
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->update_college($data)) {
                    $this->utility_model->show_success(
                        'Your edits have been succesfully saved.'
                    );
                    return;
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.<br>" .
                                                "You can't be at two colleges or schools at the same time.";
                }
            }
        }

        try {
            $user_college = $this->profile_model->get_user_college($user_college_id);
        }
        catch (CollegeNotFoundException $e) {
            show_404();
        }

        $data['user_college'] = $user_college;

        if (!isset($data['error_message'])) {  // So that we can retain the dates entered in the form.
            $data['start_year'] = $user_college['start_year'];
            $data['start_month'] = $user_college['start_month'];
            $data['start_day'] = $user_college['start_day'];

            $data['end_year'] = $user_college['end_year'];
            $data['end_month'] = $user_college['end_month'];
            $data['end_day'] = $user_college['end_day'];
        }

        $data['heading'] = 'Edit College';
        $data['form_action'] = base_url('profile/edit-college');
        $this->load->view('edit/college', $data);
        $this->load->view('common/footer');
    }

    public function add_programme($user_college_id=0)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['user_college_id']= $this->input->post('user-college-id');
            $data['programme_id'] = $this->input->post('programme');
            $data['year_of_study'] = $this->input->post('year-of-study');

            $this->profile_model->add_programme($data);
            $this->utility_model->show_success(
                'Your programme details have been successfully saved.'
            );
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add your programme';
        $this->load->view('common/header', $data);

        try {
            $user_college = $this->profile_model->get_user_college($user_college_id);
        }
        catch (CollegeNotFoundException $e) {
            show_404();
        }

        $data['user_college'] = $user_college;
        $data['programmes'] = $this->profile_model->get_programmes($user_college['school']['school_id']);

        $data['heading'] = 'Add Programme';
        $data['form_action'] = base_url('profile/add-programme');
        $this->load->view('edit/programme', $data);
        $this->load->view('common/footer');
    }

    public function edit_programme($user_programme_id=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Edit your programme';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['user_programme_id'] = $this->input->post('user-programme-id');
            $data['year_of_study'] = $this->input->post('year-of-study');

            $this->profile_model->update_programme($data);
            $this->utility_model->show_success(
                'Your edits have been successfully saved.'
            );
            return;
        }

        try {
            $user_programme = $this->profile_model->get_user_programme($user_programme_id);
        }
        catch (ProgrammeNotFoundException $e) {
            show_404();
        }

        $data['user_programme'] = $user_programme;
        $data['year_of_study'] = $user_programme['year_of_study'];

        $data['heading'] = 'Edit Programme Details';
        $data['form_action'] = base_url('proifle/edit-programme');
        $this->load->view('edit/programme', $data);
        $this->load->view('common/footer');
    }

    public function add_hall()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add hall of attachment/residence';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['hall_id'] = $this->input->post('hall');
            $data['resident'] = $this->input->post('resident');

            $data['start_day'] = $this->input->post('start-day');
            $data['start_month'] = $this->input->post('start-month');
            $data['start_year'] = $this->input->post('start-year');

            $data['end_day'] = $this->input->post('end-day');
            $data['end_month'] = $this->input->post('end-month');
            $data['end_year'] = $this->input->post('end-year');
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->add_hall($data)) {
                    $this->utility_model->show_success(
                        'Your hall details have been successfully saved.'
                    );
                    return;
                }
                else {
                    $data['error_message'] = 'The years you entered either conflict with one of your records.<br>' .
                                             'Either you indicated that you were in a hostel during that period, or<br>' .
                                             'The dates overlap with one of your other halls.';
                }
            }
        }

        $data['halls'] = $this->profile_model->get_halls();

        $data['heading'] = 'Add Hall';
        $data['form_action'] = base_url('profile/add-hall');
        $this->load->view('edit/hall', $data);
        $this->load->view('common/footer');
    }

    public function edit_hall($user_hall_id=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Edit hall of attachment/residence';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // DON'T join these two lines into 1,
            // $user_hall_id is usefull for re-displaying the form incase of any error.
            $user_hall_id = $this->input->post('user-hall-id');
            $data['user_hall_id'] = $user_hall_id;

            $data['hall_id'] = $this->input->post('hall-id');
            $data['resident'] = $this->input->post('resident');

            $data['start_day'] = $this->input->post('start-day');
            $data['start_month'] = $this->input->post('start-month');
            $data['start_year'] = $this->input->post('start-year');

            $data['end_day'] = $this->input->post('end-day');
            $data['end_month'] = $this->input->post('end-month');
            $data['end_year'] = $this->input->post('end-year');
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->update_hall($data)) {
                    $this->utility_model->show_success(
                        'Your edits have been successfully saved.'
                    );
                    return;
                }
                else {
                    $data['error_message'] = 'The years you entered conflict with one of your records.<br>' .
                                                'You cannot be attached to/a resident of two halls at the same time.';
                }
            }
        }

        try {
            $user_hall = $this->profile_model->get_user_hall($user_hall_id);
        }
        catch (HallNotFoundException $e) {
            show_404();
        }

        $data['user_hall'] = $user_hall;
        if (!isset($data['error_message'])) {  // So that we may retain the dates entered in the form.
            $data['resident'] = $user_hall['resident'];

            $data['start_day'] = $user_hall['start_day'];
            $data['start_month'] = $user_hall['start_month'];
            $data['start_year'] = $user_hall['start_year'];

            $data['end_day'] = $user_hall['end_day'];
            $data['end_month'] = $user_hall['end_month'];
            $data['end_year'] = $user_hall['end_year'];

            $data['old_start_date'] = $user_hall['date_from'];
            $data['old_end_date'] = $user_hall['date_to'];
        }

        $data['heading'] = 'Edit Hall';
        $data['form_action'] = base_url('profile/edit-hall');
        $this->load->view('edit/hall', $data);
        $this->load->view('common/footer');
    }

    public function add_hostel()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add hostel';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['hostel_id'] = $this->input->post('hostel');

            $data['start_day'] = $this->input->post('start-day');
            $data['start_month'] = $this->input->post('start-month');
            $data['start_year'] = $this->input->post('start-year');

            $data['end_day'] = $this->input->post('end-day');
            $data['end_month'] = $this->input->post('end-month');
            $data['end_year'] = $this->input->post('end-year');
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->add_hostel($data)) {
                    $this->utility_model->show_success(
                        'Your hostel details have been successfully saved.'
                    );
                    return;
                }
                else {
                    $data['error_message'] = 'The hostel you entered conflicts with one of your records.<br>' .
                                             'Either you indicated that you are a resident of a hall, Or<br>' .
                                             'The date overlaps with that of one of the hostels you have been to.';
                }
            }
        }

        $data['heading'] = 'Add Hostel';
        $data['form_action'] = base_url('profile/add-hostel');
        $data['hostels'] = $this->profile_model->get_hostels();
        $this->load->view('edit/hostel', $data);
        $this->load->view('common/footer');
    }

    public function edit_hostel($user_hostel_id=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Edit hostel';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // DON'T join these two lines into 1,
            // $user_hall_id is usefull for re-displaying the form incase of any error.
            $user_hostel_id = $this->input->post('user-hostel-id');
            $data['user_hostel_id'] = $user_hostel_id;

            $data['hostel_id'] = $this->input->post('hostel-id');

            $data['start_day'] = $this->input->post('start-day');
            $data['start_month'] = $this->input->post('start-month');
            $data['start_year'] = $this->input->post('start-year');

            $data['end_day'] = $this->input->post('end-day');
            $data['end_month'] = $this->input->post('end-month');
            $data['end_year'] = $this->input->post('end-year');
            if ($data['end_year'] < $data['start_year']) {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }
            elseif (checkdate($data['start_month'], $data['start_day'], $data['start_year']) &&
                    checkdate($data['end_month'], $data['end_day'], $data['end_year'])) {
                $data['start_date'] = "{$data['start_year']}-{$data['start_month']}-{$data['start_day']}";
                $data['end_date'] = "{$data['end_year']}-{$data['end_month']}-{$data['end_day']}";
            }
            else {
                $data['error_message'] = 'Invalid dates entered! Please check the dates and try again.';
            }

            if (!isset($data['error_message'])) {
                if ($this->profile_model->update_hostel($data)) {
                    $this->utility_model->show_success(
                        'Your edits have been successfully saved.'
                    );
                    return;
                }
                else {
                    $data['error_message'] = 'The hostel you entered conflicts with one of your records.<br>' .
                                             'Either you indicated that you are a resident of a hall, Or<br>' .
                                             'The date overlaps with that of one of the hostels you have been to.';
                }
            }
        }

        try {
            $user_hostel = $this->profile_model->get_user_hostel($user_hostel_id);
        }
        catch (HostelNotFoundException $e) {
            show_404();
        }

        $data['user_hostel'] = $user_hostel;
        if (!isset($data['error_message'])) {  // So that we may retain the dates entered in the form.
            $data['start_day'] = $user_hostel['start_day'];
            $data['start_month'] = $user_hostel['start_month'];
            $data['start_year'] = $user_hostel['start_year'];

            $data['end_day'] = $user_hostel['end_day'];
            $data['end_month'] = $user_hostel['end_month'];
            $data['end_year'] = $user_hostel['end_year'];

            $data['old_start_date'] = $user_hostel['date_from'];
            $data['old_end_date'] = $user_hostel['date_to'];
        }

        $data['heading'] = 'Edit Hostel';
        $data['form_action'] = base_url('profile/edit-hostel');
        $this->load->view('edit/hostel', $data);
        $this->load->view('common/footer');
    }

    public function add_country()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add your country of origin';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $country_id = trim(strip_tags($this->input->post('country')));
            if ($country_id == 'none') {
                // Display a form allowing the user to enter his/her country
                // and notifiy the admin.
                redirect(base_url('request-admin/add-country'));
            }
            else {
                $this->profile_model->add_country($country_id);
                $data['success_message'] = 'Your country details have been successfully saved.';
            }
        }
        else {
            $data['countries'] = $this->profile_model->get_countries();
        }
        $this->load->view('edit/country', $data);
        $this->load->view('common/footer');
    }

    public function add_district($district_id=0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Add your district';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['district'] = trim(strip_tags($this->input->post('district')));
            if (empty($data['district'])) {
                $data['error_message'] = 'Please enter the name of your district or state and try again.';
            }
            else {
                $data['districts'] = $this->profile_model->get_searched_district($data['district']);
            }
        }
        elseif ($district_id) {
            if ($this->profile_model->add_district($district_id)) {
                $data['success_message'] = 'Your district details have been successfully updated.';
            }
            else {
                $data['error_message'] = 'Sorry, but an error occured.';
            }
        }

        $data['heading'] = 'Add District';
        $this->load->view('edit/district', $data);
        $this->load->view('common/footer');
    }
}
?>
