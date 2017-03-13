<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utility_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function handle_error($error)
    {
        print($error);
        exit(1);
    }

    public function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }

    public function show_success($success_message)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Success!";
        $this->load->view("common/header", $data);

        $data['success'] = $success_message;
        $this->load->view("show-success", $data);
        $this->load->view("common/footer");
    }

    public function show_permission_denied($message)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Permission Denied!";
        $this->load->view("common/header", $data);

        $data['message'] = $message;
        $this->load->view("show-permission-denied", $data);
        $this->load->view("common/footer");
    }
}
