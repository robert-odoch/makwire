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
    }

    public function like($reply_id = 0)
    {
        try {
            $this->reply_model->like($reply_id, $_SESSION['user_id']);
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

    public function likes($reply_id = 0, $offset = 0)
    {
        try {
            $reply = $this->reply_model->get_reply($_SESSION['user_id'], $reply_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
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

    public function options($reply_id = 0)
    {
        try {
            $reply = $this->reply_model->get_reply($_SESSION['user_id'], $reply_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Edit or delete this reply';
        $this->load->view('common/header', $data);

        $data['object'] = 'reply';
        $data['reply'] = $reply;
        $this->load->view('comment-reply-options', $data);
        $this->load->view('common/footer');
    }

    public function edit($reply_id = 0)
    {
        $data = [];

        try {
            $reply = $this->reply_model->get_reply($_SESSION['user_id'], $reply_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($reply['commenter_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to edit this reply.";
            redirect(base_url('user/error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_reply = $this->input->post('reply');
            if (strlen($new_reply) == 0) {
                $data['error_message'] = "Reply can't be empty.";
            }
            else {
                $this->reply_model->update_reply($reply_id, $new_reply);
                redirect(base_url("reply/likes/{$reply_id}"));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Edit reply - Makwire';
        $this->load->view('common/header', $data);

        $data['object'] = 'reply';
        $data['reply'] = $reply;
        $this->load->view('edit/comment-or-reply', $data);
        $this->load->view('common/footer');
    }

    public function delete($reply_id = 0)
    {
        try {
            $reply = $this->reply_model->get_reply($_SESSION['user_id'], $reply_id);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($reply['commenter_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to delete this reply.";
            redirect(base_url('user/error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->reply_model->delete_reply($reply_id);
            $_SESSION['message'] = 'Your reply has been successfully deleted.';
            redirect(base_url('user/success'));
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Delete reply - Makwire';
        $this->load->view('common/header', $data);

        $reply_data = [
            'item' => 'reply',
            'object' => 'reply',
            'reply' => $reply,
            'item_owner_id' => $reply['commenter_id'],
            'form_action' => base_url("reply/delete/{$reply['comment_id']}"),
            'cancel_url' => base_url("reply/likes/{$reply['comment_id']}")
        ];

        $data = array_merge($data, $reply_data);
        $this->load->view('delete-item', $data);
        $this->load->view('common/footer');
    }
}
?>
