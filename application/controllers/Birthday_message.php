<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Birthday_message extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model([
            'user_model', 'birthday_message_model', 'utility_model'
        ]);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function like($birthday_message_id=0)
    {
        try {
            $this->birthday_message_model->like($birthday_message_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (MessageNotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to like this message."
            );
        }
    }
}
?>
