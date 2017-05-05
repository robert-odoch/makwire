<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contians utility functions shared among models.
 */
class Utility_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    /**
     * Handle database errors.
     *
     * @param $error the resulting error from running a query.
     */
    public function handle_error($error)
    {
        print($error);
        exit(1);
    }

    /**
     * Runs a query againsts the database.
     *
     * @param $sql the SQL query to be run.
     * @return query object.
     */
    public function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }

    /**
     * Shows a success message.
     *
     * @param $message the message to display.
     */
    public function show_success($message)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Success!";
        $this->load->view("common/header", $data);

        $data['messagesss'] = $message;
        $this->load->view("show-success", $data);
        $this->load->view("common/footer");
    }

    /**
     * Shows message if a user attempts to perfrom an illegal activity.
     *
     * @param $message the message to display.
     */
    public function show_error($title, $message)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = $title;
        $this->load->view("common/header", $data);

        $data['message'] = $message;
        $this->load->view("show/error", $data);
        $this->load->view("common/footer");
    }
}
