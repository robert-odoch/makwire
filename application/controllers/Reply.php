<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reply extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'reply_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function like($reply_id = 0)
    {
        try {
            $this->reply_model->like($reply_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $this->utility_model->show_error(
                "Permission Denied!",
                "You don't have the proper permissions to like this reply."
            );
        }
    }

    public function likes($reply_id = 0, $offset = 0)
    {
        try {
            $reply = $this->reply_model->get_reply($reply_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who liked this reply';
        $this->load->view('common/header', $data);

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
        $data['likes'] = $this->reply_model->get_likes($reply, $offset, $limit);

        $data['object'] = 'reply';
        $data['reply'] = $reply;
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }
}
?>
