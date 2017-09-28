<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_feed extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        ensure_user_is_logged_in();

        $this->load->model(['user_model', 'news_feed_model']);
    }

    public function index($offset = 0)
    {
        $limit = 10;  // Maximum number of items to show.

        if (is_ajax_request()) {
            $data['has_next'] = FALSE;
            $num_news_feed_items = $this->news_feed_model->get_num_news_feed_items($_SESSION['user_id']);
            if (($num_news_feed_items - $offset) > $limit) {
                $data['has_next'] = TRUE;
                $data['next_offset'] = ($offset + $limit);
            }

            $news_feed_items = $this->news_feed_model->get_news_feed_items($_SESSION['user_id'], $offset, $limit);
            $data['page'] = 'news-feed';
            $data['items'] = $news_feed_items;

            $html = $this->load->view('common/items', $data, TRUE);
            echo $html;
            return;
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Makwire - News Feed';
        $data['page'] = 'news-feed';
        $this->load->view('common/header', $data);

        if (isset($_SESSION['post_error']) && !empty($_SESSION['post_error'])) {
            $data['post_error'] = $_SESSION['post_error'];
            unset($_SESSION['post_error']);
        }

        $data['has_next'] = FALSE;
        $num_news_feed_items = $this->news_feed_model->get_num_news_feed_items($_SESSION['user_id']);
        if (($num_news_feed_items - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $news_feed_items = $this->news_feed_model->get_news_feed_items($_SESSION['user_id'], $offset, $limit);
        $data['items'] = $news_feed_items;
        $data['is_visitor'] = FALSE;
        $this->load->view('show/user', $data);
        $this->load->view('common/footer');
    }

}
