<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_feed extends CI_Controller
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
            'user_model', 'news_feed_model'
        ]);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function index($offset = 0)
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Makwire | News Feed';
        $this->load->view('common/header', $data);

        if (isset($_SESSION['post_error']) && !empty($_SESSION['post_error'])) {
            $data['post_error'] = $_SESSION['post_error'];
            unset($_SESSION['post_error']);
        }

        $limit = 10;  // Maximum number of items to show.
        $data['has_next'] = FALSE;
        $num_news_feed_items = $this->news_feed_model->get_num_news_feed_items();
        if (($num_news_feed_items - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $news_feed_items = $this->news_feed_model->get_news_feed_items($offset, $limit);
        $data['items'] = $news_feed_items;
        $data['is_visitor'] = FALSE;
        $data['page'] = 'news-feed';
        $this->load->view('show/user', $data);
        $this->load->view('common/footer');
    }

}
