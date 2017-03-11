<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Photo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }

        $this->load->model('photo_model');
        $this->load->model('user_model');

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    private function permission_denied($error_message)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = "Permission Denied!";
        $this->load->view("common/header", $data);

        $data['error'] = $error_message;
        $this->load->view("show-permission-denied", $data);
        $this->load->view("common/footer");
    }

    public function like($photo_id)
    {
        $like = $this->photo_model->like($photo_id);
        if (!$like) {
            $this->permission_denied("You don't have the proper permissions to like this photo.");
            return;
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function comment($photo_id)
    {
        $photo = $this->photo_model->get_photo($photo_id);
        if (!$photo || !$this->user_model->are_friends($photo['user_id'])) {
            $this->permission_denied("You don't have the proper permissions to comment on this photo.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comment on this photo';
        $this->load->view("common/header", $data);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty(trim($this->input->post('comment')))) {
                $data['comment_error'] = "Comment can't be empty";
            }
            else {
                $comment = $this->input->post('comment');
                $this->photo_model->comment($photo_id, $comment);
                $this->comments($photo_id);
                return;
            }
        }

        $data['photo'] = $photo;
        $data['object'] = 'photo';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($photo_id)
    {
        $share = $this->photo_model->share($photo_id);
        if (!$share) {
            $this->permission_denied("You don't have the proper permissions to share this photo.");
            return;
        }

        redirect(base_url("user/index/{$_SESSION['user_id']}"));
    }

    public function likes($photo_id, $offset=0)
    {
        $photo = $this->photo_model->get_photo($photo_id);
        if (!$photo) {
            $this->permission_denied("You don't have the proper permissions to view this photo.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who liked this photo";
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
        if (($photo['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->photo_model->get_likes($photo_id, $offset, $limit);

        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view("show-likes", $data);
        $this->load->view("common/footer");
    }

    public function comments($photo_id, $offset=0)
    {
        $photo = $this->photo_model->get_photo($photo_id);
        if (!$photo) {
            $this->permission_denied("You don't have the proper permissions to view this photo.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Comments on this photo';
        $this->load->view("common/header", $data);

        // Maximum number of comments to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        if (($photo['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['comments'] = $this->photo_model->get_comments($photo_id, $offset, $limit);

        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view("show-comments", $data);
        $this->load->view("common/footer");
    }

    public function shares($photo_id, $offset=0)
    {
        $photo = $this->photo_model->get_photo($photo_id);
        if (!$photo) {
            $this->permission_denied("You don't have the proper permissions to view this photo.");
            return;
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = "People who shared this photo";
        $this->load->view("common/header", $data);

        // Maximum number of shares to display.
        $limit = 10;

        if ($offset != 0) {
            $data['has_prev'] = TRUE;
            $data['prev_offset'] = 0;
            if ($offset > $limit) {
                $data['prev_offset'] = ($offset - $limit);
            }
        }

        $data['has_next'] = FALSE;
        if (($photo['num_shares'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['shares'] = $this->photo_model->get_shares($photo_id, $offset, $limit);

        $data['object'] = 'photo';
        $data['photo'] = $photo;
        $this->load->view("show-shares", $data);
        $this->load->view("common/footer");
    }
}
?>
