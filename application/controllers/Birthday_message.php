<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Birthday_message extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model([
            'user_model', 'birthday_message_model', 'utility_model'
        ]);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function like($message_id = 0)
    {
        try {
            $this->birthday_message_model->like($message_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('user/error'));
        }
    }

    public function likes($message_id = 0, $offset = 0)
    {
        try {
            $message = $this->birthday_message_model->get_message($message_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who liked this message';
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
        if (($message['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->birthday_message_model->get_likes($message, $offset, $limit);

        $data['object'] = 'message';
        $data['message'] = $message;
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }

    public function reply($message_id)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reply = trim(strip_tags($this->input->post('reply')));
            if (strlen($reply) == 0) {
                $data['reply_error'] = "Reply can't be empty!";
            }
            else {
                $this->birthday_message_model->reply($message_id, $reply);
                redirect(base_url("birthday-message/replies/{$message_id}"));
            }
        }

        try {
            $message = $this->birthday_message_model->get_message($message_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        // Only allow sender to reply to his message if there is atleast one reply.
        if ($message['sender_id'] == $_SESSION['user_id'] &&
            $message['num_replies'] == 0) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'For you to reply to your own message, atleast one of your friends must have replied.';
            redirect(base_url('user/error'));
        }

        if (!$message['user_can_reply']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to reply to this message.";
            redirect(base_url('user/error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user());
        $data['title'] = 'Reply to birthday message';
        $this->load->view('common/header', $data);

        $data['message'] = $message;
        $this->load->view('reply-birthday-message', $data);
        $this->load->view('common/footer');
    }

    public function replies($message_id = 0, $offset = 0)
    {
        try {
            $message = $this->birthday_message_model->get_message($message_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'People who replied to this message';
        $this->load->view('common/header', $data);

        // Maximum number of replies to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        if (($message['num_replies'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['replies'] = $this->birthday_message_model->get_replies($message, $offset, $limit);
        $data['object'] = 'birthday-message';
        $data['message'] = $message;
        $this->load->view('show/replies', $data);
        $this->load->view('common/footer');
    }
}
?>
