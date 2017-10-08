<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Makwire extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! empty($_SESSION['user_id'])) {
            $this->load->model('user_model');
        }
    }

    public function menu()
    {
        if (empty($_SESSION['user_id'])) {
            $data = [];
        }
        else {
            $data = $this->user_model->initialize_user($_SESSION['user_id']);
        }

        $data['title'] = 'Makwire menu';
        $data['page'] = 'menu';
        $this->load->view('common/header', $data);
        $this->load->view('show/menu');
        $this->load->view('common/footer');
    }

    public function error()
    {
        if (empty($_SESSION['user_id'])) {
            $data = [];
        }
        else {
            $data = $this->user_model->initialize_user($_SESSION['user_id']);
        }

        // Defaults.
        $title = 'Error! - Makwire';
        $heading = 'Something isn\'t right';
        $message = 'Oh dear, I don\'t know how you ended up here.';

        // Allow calls to override.
        if ( ! empty($_SESSION['title'])) {
            $title = $_SESSION['title'];
            unset($_SESSION['title']);
        }

        if ( ! empty($_SESSION['heading'])) {
            $heading = $_SESSION['heading'];
            unset($_SESSION['heading']);
        }

        if ( ! empty($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $data['title'] = $title;
        $data['heading'] = $heading;
        $data['message'] = $message;

        $this->load->view('common/header', $data);
        $this->load->view('show/error', $data);
        $this->load->view('common/footer');
    }

    public function success()
    {
        if (empty($_SESSION['user_id'])) {
            $data = [];
        }
        else {
            $data = $this->user_model->initialize_user($_SESSION['user_id']);
        }

        // Defaults.
        $title = 'Success! - Makwire';
        $heading = 'Success';
        $message = 'Thanks for passing by...';

        // Allow calls to override.
        if ( ! empty($_SESSION['title'])) {
            $title = $_SESSION['title'];
            unset($_SESSION['title']);
        }

        if ( ! empty($_SESSION['heading'])) {
            $heading = $_SESSION['heading'];
            unset($_SESSION['heading']);
        }

        if ( ! empty($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $data['title'] = $title;
        $data['heading'] = $heading;
        $data['message'] = $message;

        $this->load->view('common/header', $data);
        $this->load->view('show/success', $data);
        $this->load->view('common/footer');
    }

    public function mobile_nav()
    {
        if ( ! is_ajax_request()) {
            show_404();
        }

        if (empty($_SESSION['user_id'])) {
            $data = [];
        }
        else {
            $data = $this->user_model->initialize_user($_SESSION['user_id']);
        }

        echo $this->load->view('common/mobile-nav', $data, TRUE);
    }

    public function desktop_nav()
    {
        if ( ! is_ajax_request()) {
            show_404();
        }

        if (empty($_SESSION['user_id'])) {
            $data = [];
        }
        else {
            $data = $this->user_model->initialize_user($_SESSION['user_id']);
        }

        echo $this->load->view('common/desktop-nav', $data, TRUE);
    }

    public function suggestions()
    {
        if ( ! is_ajax_request()) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        echo $this->load->view('common/suggestions', $data, TRUE);
    }
}
