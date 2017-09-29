<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();

        $this->load->model(['user_model', 'comment_model', 'utility_model']);
    }

    public function like($comment_id = 0)
    {
        if (is_ajax_request()) {
            try {
                $num_likes = $this->comment_model->like($comment_id, $_SESSION['user_id']);
                $num_likes .= ($num_likes == 1) ? ' like' : ' likes';
                echo $num_likes;
            }
            catch (NotFoundException $e) {
            }
            catch (IllegalAccessException $e) {
            }

            return;
        }

        try {
            $this->comment_model->like($comment_id, $_SESSION['user_id']);
            redirect($_SERVER['HTTP_REFERER']);
        }
        catch (NotFoundException $e) {
            show_404();
        }
        catch (IllegalAccessException $e) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = $e->getMessage();
            redirect(base_url('error'));
        }
    }

    public function reply($comment_id = 0)
    {
        try {
            $comment = $this->comment_model->get_comment($comment_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if (!$this->user_model->are_friends($_SESSION['user_id'], $comment['commenter_id'])) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to reply to this comment.";
            redirect(base_url('error'));
        }

        // Only allow a user to reply to his comment if there is atleast one reply.
        if ($comment['commenter_id'] == $_SESSION['user_id'] &&
            $comment['num_replies'] == 0) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "For you to reply to your own comment, atleast one of your friends must have replied.";
            redirect(base_url('error'));
        }

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reply = trim(strip_tags($this->input->post('reply')));
            if (!$reply) {
                $data['reply_error'] = "Reply can't be empty!";
            }
            else {
                $this->comment_model->reply($comment_id, $reply, $_SESSION['user_id']);
                redirect(base_url("comment/replies/{$comment_id}"));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] ='Reply to Comment';
        $this->load->view('common/header', $data);

        $data['comment'] = $comment;
        $data['object'] = 'comment';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function likes($comment_id = 0, $offset = 0)
    {
        try {
            $comment = $this->comment_model->get_comment($comment_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'People who liked this comment';
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
        if (($comment['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['likes'] = $this->comment_model->get_likes($comment, $offset, $limit);

        $data['object'] = 'comment';
        $data['comment'] = $comment;
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }

    public function replies($comment_id = 0, $offset = 0)
    {
        try {
            $comment = $this->comment_model->get_comment($comment_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'People who replied to this comment';
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
        if (($comment['num_replies'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['replies'] = $this->comment_model->get_replies($comment, $offset, $limit, $_SESSION['user_id']);
        $data['object'] = 'comment';
        $data['comment'] = $comment;
        $this->load->view('show/replies', $data);
        $this->load->view('common/footer');
    }

    public function options($comment_id = 0)
    {
        try {
            $comment = $this->comment_model->get_comment($comment_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Edit or delete this comment';
        $this->load->view('common/header', $data);

        $data['object'] = 'comment';
        $data['comment'] = $comment;
        $this->load->view('comment-reply-options', $data);
        $this->load->view('common/footer');
    }

    public function edit($comment_id = 0)
    {
        $data = [];

        try {
            $comment = $this->comment_model->get_comment($comment_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($comment['commenter_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to edit this comment.";
            redirect(base_url('error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_comment = $this->input->post('comment');
            if (strlen($new_comment) == 0) {
                $data['error'] = "Comment can't be empty.";
            }
            else {
                $this->comment_model->update_comment($comment_id, $new_comment);
                redirect(base_url("comment/replies/{$comment_id}"));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit comment - Makwire';
        $this->load->view('common/header', $data);

        $data['object'] = 'comment';
        $data['comment'] = $comment;
        $this->load->view('edit/comment-or-reply', $data);
        $this->load->view('common/footer');
    }

    public function delete($comment_id = 0)
    {
        try {
            $comment = $this->comment_model->get_comment($comment_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->comment_model->delete_comment($comment_id, $_SESSION['user_id']);
                $_SESSION['message'] = 'Your comment has been successfully deleted.';
                redirect(base_url('success'));
            }
            catch (IllegalAccessException $e) {
                $_SESSION['title'] = 'Permission Denied!';
                $_SESSION['heading'] = 'Permission Denied';
                $_SESSION['message'] = $e->getMessage();
                redirect(base_url('error'));
            }
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Delete comment - Makwire';
        $this->load->view('common/header', $data);

        $comment_data = [
            'item' => 'comment',
            'object' => 'comment',
            'comment' => $comment,
            'item_owner_id' => $comment['commenter_id'],
            'form_action' => base_url("comment/delete/{$comment['comment_id']}"),
            'cancel_url' => base_url("comment/replies/{$comment['comment_id']}")
        ];

        $data = array_merge($data, $comment_data);
        $this->load->view('delete-item', $data);
        $this->load->view('common/footer');
    }
}
?>
