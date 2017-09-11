<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();
        $this->load->model('user_model');
    }

    public function add_district()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $district = trim(strip_tags($this->input->post("district")));
            if (empty($district)) {
                $data['error_message'] = 'Please enter the name of your district ' .
                                            'or state and try again.';
            }
            else {
                // Submit the request to the administrator.
                // TODO: code to submit the request.
                $_SESSION['message'] = "Your request to add the district <em><b>{$district}</b></em> has been submitted.<br>" .
                                        "The administrator will reply as soon as he sees it.";
                redirect(base_url('success'));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Request admin to add your district or state';
        $this->load->view('common/header', $data);

        $this->load->view('request-admin/add-district', $data);
        $this->load->view('common/footer');
    }

    public function add_country()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $country = trim(strip_tags($this->input->post("country")));
            if (empty($country)) {
                $data['error_message'] = 'Please enter the name of your country and try again.';
            }
            else {
                // Submit the request to the administrator.
                // TODO: code to submit the request.
                $_SESSION['message'] = "Your request to add the country <em><b>{$country}</b></em> has been submitted.<br>" .
                                        "The administrator will reply as soon as he sees it.";
                redirect(base_url('success'));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Request admin to add your country';
        $this->load->view('common/header', $data);

        $this->load->view('request-admin/add-country', $data);
        $this->load->view('common/footer');
    }
}

?>
