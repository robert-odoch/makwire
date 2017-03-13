<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'comment_model', 'utility_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function like($comment_id)
    {
        if (!$this->comment_model->like($comment_id)) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to like this comment.");
            return;
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function reply($comment_id)
    {
        $comment = $this->comment_model->get_comment($comment_id);
        if (!$comment ||
            !$this->user_model->are_friends($comment['commenter_id'])) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to reply to this comment.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "Reply to Comment";
        $this->load->view("common/header", $data);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $reply = trim(strip_tags($this->input->post('reply')));
            if (!$reply) {
                $data['reply_error'] = "Reply can't be empty!";
            }
            else {
                $this->comment_model->reply($comment_id, $reply);
                $this->replies($comment_id);
                return;
            }
        }

        $data['comment'] = $comment;
        $data['object'] = 'comment';
        $this->load->view("comment", $data);
        $this->load->view("common/footer");
    }

    public function likes($comment_id, $offset=0)
    {
        $comment = $this->comment_model->get_comment($comment_id);
        if (!$comment) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to view this comment.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who liked this comment";
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
        if (($comment['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['likes'] = $this->comment_model->get_likes($comment_id, $offset, $limit);

        $data['object'] = 'comment';
        $data['comment'] = $comment;
        $this->load->view("show-likes", $data);
        $this->load->view("common/footer");
    }

    public function replies($comment_id, $offset=0)
    {
        $comment = $this->comment_model->get_comment($comment_id);
        if (!$comment) {
            $this->utility_model->show_permission_denied("You don't have the proper permissions " .
                                                            "to view this comment.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who replied to this comment";
        $this->load->view("common/header", $data);

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
        if (($comment['num_replies'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['replies'] = $this->comment_model->get_replies($comment_id, $offset, $limit);
        $data['object'] = 'comment';
        $data['comment'] = $comment;
        $this->load->view("show-comment-replies", $data);
        $this->load->view("common/footer");
    }
}
?>
