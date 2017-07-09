<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model('user_model');

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function add_district()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Request admin to add your district or state';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $district = trim(strip_tags($this->input->post("district")));
            if (empty($district)) {
                $data['error_message'] = 'Please enter the name of your district ' .
                                            'or state and try again.';
            }
            else {
                // Submit the request to the administrator.
                // TODO: code to submit the request.
                $data['success_message'] = "Your request to add the district <em><b>{$district}</b></em> has been submitted.<br>" .
                                            "The administrator will reply as soon as he sees it.";
            }
        }

        $this->load->view('request-admin/add-district', $data);
        $this->load->view('common/footer');
    }

    public function add_country()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Request admin to add your country';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $country = trim(strip_tags($this->input->post("country")));
            if (empty($country)) {
                $data['error_message'] = 'Please enter the name of your country and try again.';
            }
            else {
                // Submit the request to the administrator.
                // TODO: code to submit the request.
                $data['success_message'] = "Your request to add the country <em><b>{$country}</b></em> has been submitted.<br>" .
                                            "The administrator will reply as soon as he sees it.";
            }
        }

        $this->load->view('request-admin/add-country', $data);
        $this->load->view('common/footer');
    }
}

?>
