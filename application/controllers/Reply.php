<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reply extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'reply_model']);

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

    public function like($reply_id, $comment_id, $offset)
    {
        if (!$this->reply_model->like($reply_id)) {
            $this->show_permission_denied("You don't have the proper permissions " .
                                            "to like this reply.");
            return;
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function likes($reply_id, $offset=0)
    {
        $reply = $this->reply_model->get_reply($reply_id);
        if (!$reply) {
            $this->show_permission_denied("You don't have the proper permissions " .
                                            "to view this reply.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who liked this reply";
        $this->load->view("common/header", $data);

        // Maximum number of likes to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        if (($reply['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->reply_model->get_likes($reply_id, $offset, $limit);

        $data['object'] = 'reply';
        $data['reply'] = $reply;
        $this->load->view("show-likes", $data);
        $this->load->view("common/footer");
    }
}
?>
