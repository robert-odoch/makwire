<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();

        // Set up and load the upload library.
        $config['upload_path'] = 'uploads';
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['file_ext_tolower'] = TRUE;
        $config['max_size'] = 1024;
        $this->load->library('upload', $config);

        $this->load->library('image_lib');
        $this->load->helper(['form']);

        $this->load->model(['user_model', 'profile_model']);
    }

    public function change_profile_picture()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ( ! $this->upload->do_upload('userfile')) {
                $data['error'] = $this->upload->display_errors();
            }
            else {

                // Upload the file.
                $upload_data = $this->upload->data();

                $config['image_library'] = 'gd2';
                $config['source_image'] = $upload_data['full_path'];
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = "";
                $config['maintain_ratio'] = TRUE;

                // Create a 60x60 thumbnail for profile picture.
                $config['new_image'] = "{$upload_data['file_path']}small";
                $config['width'] = 60;
                $config['height'] = 60;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Create a 480x300 thumbnail for photo.
                $config['new_image'] = $upload_data['file_path'];
                $config['width'] = 480;
                $config['height'] = 300;
                $this->image_lib->initialize($config);
                $this->image_lib->resize();

                // Record photo data in the photos table.
                $photo_id = $this->photo_model->add_photo($upload_data, $_SESSION['user_id']);

                // Set profile picture.
                $this->profile_model->set_profile_picture($photo_id, $_SESSION['user_id']);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Change profile picture';
        $this->load->view('common/header', $data);

        $this->load->view('add/profile-picture', $data);
        $this->load->view('common/footer');
    }

    public function add_school()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['school_id'] = $this->input->post('school');
            $data['level'] = $this->input->post('level');

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

            if (empty($data['error_message'])) {
                // Try saving the college and school.
                if ($this->profile_model->add_school($_SESSION['user_id'], $data)) {
                    $_SESSION['message'] = 'Your school has been succesfully saved.';
                    redirect(base_url('success'));
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.
                                                Remember, You can't be at two schools at the same time.";
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add your school';
        $this->load->view('common/header', $data);

        // User reaches here if he has just opened this page, or
        // there is an error in submitted form data.
        $data['schools'] = $this->profile_model->get_schools($_SESSION['user_id']);

        $data['heading'] = 'Add School';
        $data['form_action'] = base_url('profile/add-school');
        $this->load->view('add/school', $data);
        $this->load->view('common/footer');
    }

    public function edit_school($user_school_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_school_id = $this->input->post('user-school-id');
            $data['user_school_id'] = $user_school_id;
            $data['school_id'] = $this->input->post('school-id');
            $data['level'] = $this->input->post('level');

            // Get the dates.
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

            if (empty($data['error_message'])) {
                if ($this->profile_model->update_school($_SESSION['user_id'], $data)) {
                    $_SESSION['message'] = 'Your edits have been succesfully saved.';
                    redirect(base_url('success'));
                }
                else {
                    $data['error_message'] = "The years you entered conflict with one of your records.
                                                Remember, you can't be at two schools at the same time.";
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit your school';
        $this->load->view('common/header', $data);

        try {
            $user_school = $this->profile_model->get_user_school($user_school_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['user_school'] = $user_school;

        if (empty($data['error_message'])) {
            // Use values from the database if visitor is viewing this page
            // for the first time.
            $data['level'] = $user_school['level'];

            $data['start_year'] = $user_school['start_year'];
            $data['start_month'] = $user_school['start_month'];
            $data['start_day'] = $user_school['start_day'];

            $data['end_year'] = $user_school['end_year'];
            $data['end_month'] = $user_school['end_month'];
            $data['end_day'] = $user_school['end_day'];
        }

        $data['heading'] = 'Edit School';
        $data['form_action'] = base_url('profile/edit-school');
        $this->load->view('edit/school', $data);
        $this->load->view('common/footer');
    }

    public function add_programme($user_school_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['user_school_id'] = $this->input->post('user-school-id');
            $data['programme_id'] = $this->input->post('programme');
            $data['year_of_study'] = $this->input->post('year-of-study');
            $data['graduated'] = ($data['year_of_study'] == 0) ? 1 : 0;

            $this->profile_model->add_programme($_SESSION['user_id'], $data);
            $_SESSION['message'] = 'Your programme details have been successfully saved.';
            redirect(base_url('success'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add your programme';
        $this->load->view('common/header', $data);

        try {
            $user_school = $this->profile_model->get_user_school($user_school_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['user_school'] = $user_school;
        $data['programmes'] = $this->profile_model->get_programmes($user_school['college_id'], $user_school['level']);

        $data['heading'] = 'Add Programme';
        $data['form_action'] = base_url('profile/add-programme');
        $this->load->view('edit/programme', $data);
        $this->load->view('common/footer');
    }

    public function edit_programme($user_programme_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['user_programme_id'] = $this->input->post('user-programme-id');
            $data['year_of_study'] = $this->input->post('year-of-study');
            $data['graduated'] = ($data['year_of_study'] == 0) ? 1 : 0;

            $this->profile_model->update_programme($data);
            $_SESSION['message'] = 'Your edits have been successfully saved.';
            redirect(base_url('success'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit your programme';
        $this->load->view('common/header', $data);

        try {
            $user_programme = $this->profile_model->get_user_programme($user_programme_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['user_programme'] = $user_programme;
        $data['year_of_study'] = $user_programme['year_of_study'];

        $data['heading'] = 'Edit Programme Details';
        $data['form_action'] = base_url('profile/edit-programme');
        $this->load->view('edit/programme', $data);
        $this->load->view('common/footer');
    }

    public function add_hall()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

            if (empty($data['error_message'])) {
                if ($this->profile_model->add_hall($_SESSION['user_id'], $data)) {
                    $_SESSION['message'] = 'Your hall details have been successfully saved.';
                    redirect(base_url('success'));
                }
                else {
                    $data['error_message'] = 'The years you entered either conflict with one of your records.<br>' .
                                             'Either you indicated that you were in a hostel during that period, or<br>' .
                                             'The dates overlap with one of your other halls.';
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add hall of attachment/residence';
        $this->load->view('common/header', $data);

        $data['halls'] = $this->profile_model->get_halls($_SESSION['user_id']);

        $data['heading'] = 'Add Hall';
        $data['form_action'] = base_url('profile/add-hall');
        $this->load->view('edit/hall', $data);
        $this->load->view('common/footer');
    }

    public function edit_hall($user_hall_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

            if (empty($data['error_message'])) {
                if ($this->profile_model->update_hall($_SESSION['user_id'], $data)) {
                    $_SESSION['message'] = 'Your edits have been successfully saved.';
                    redirect(base_url('success'));
                }
                else {
                    $data['error_message'] = 'The years you entered conflict with one of your records.<br>' .
                                                'You cannot be attached to/a resident of two halls at the same time.';
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit hall of attachment/residence';
        $this->load->view('common/header', $data);

        try {
            $user_hall = $this->profile_model->get_user_hall($user_hall_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['user_hall'] = $user_hall;
        if (empty($data['error_message'])) {
            // Use values from the database if the visitor is viewing
            // this page for the first time.
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
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

            if (empty($data['error_message'])) {
                if ($this->profile_model->add_hostel($_SESSION['user_id'], $data)) {
                    $_SESSION['message'] = 'Your hostel details have been successfully saved.';
                    redirect(base_url('success'));
                }
                else {
                    $data['error_message'] = 'The hostel you entered conflicts with one of your records.<br>' .
                                             'Either you indicated that you are a resident of a hall, Or<br>' .
                                             'The date overlaps with that of one of the hostels you have been to.';
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add hostel';
        $this->load->view('common/header', $data);

        $data['heading'] = 'Add Hostel';
        $data['form_action'] = base_url('profile/add-hostel');
        $data['hostels'] = $this->profile_model->get_hostels();
        $this->load->view('edit/hostel', $data);
        $this->load->view('common/footer');
    }

    public function edit_hostel($user_hostel_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

            if (empty($data['error_message'])) {
                if ($this->profile_model->update_hostel($_SESSION['user_id'], $data)) {
                    $_SESSION['message'] = 'Your edits have been successfully saved.';
                    redirect(base_url('success'));
                }
                else {
                    $data['error_message'] = 'The hostel you entered conflicts with one of your records.<br>' .
                                             'Either you indicated that you are a resident of a hall, Or<br>' .
                                             'The date overlaps with that of one of the hostels you have been to.';
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit hostel';
        $this->load->view('common/header', $data);

        try {
            $user_hostel = $this->profile_model->get_user_hostel($user_hostel_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data['user_hostel'] = $user_hostel;
        if (empty($data['error_message'])) {  // So that we may retain the dates entered in the form.
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

    public function add_district($district_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['district'] = trim(strip_tags($this->input->post('district')));
            if (empty($data['district'])) {
                $data['error_message'] = 'Please enter the name of your district or state and try again.';
            }
            else {
                $data['districts'] = $this->profile_model->get_searched_district($data['district']);
            }
        }
        elseif ($district_id) {
            if ($this->profile_model->add_district($_SESSION['user_id'], $district_id)) {
                $_SESSION['message'] = 'Your district details have been successfully updated.';
                redirect(base_url('success'));
            }
            else {
                redirect(base_url('error'));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add your district';
        $this->load->view('common/header', $data);

        $data['heading'] = 'Add District';
        $this->load->view('edit/district', $data);
        $this->load->view('common/footer');
    }
}
?>
