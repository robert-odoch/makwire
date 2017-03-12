<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Birthday_message extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'birthday_message_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    private function show_permission_denied($message)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Permission Denied!";
        $this->load->view("common/header", $data);

        $data['message'] = $message;
        $this->load->view("show-permission-denied", $data);
        $this->load->view("common/footer");
    }

    public function like($birthday_message_id)
    {
        if (!$this->birthday_message_model->like($birthday_message_id)) {
            $this->show_permission_denied("You don't have the proper permissions " .
                                            "to like this message.");
            return;
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
}
?>
