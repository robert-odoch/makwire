<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Link extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'link_model', 'utility_model']);
    }

    public function new()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $link_url = $this->input->post('link_url');
            if ( ! filter_var($link_url, FILTER_VALIDATE_URL) ||
                    !preg_match('/^(http[s]?:\/\/)/', $link_url)) {
                $data['link_url'] = $link_url;
                $data['error_message'] = 'Please enter a valid URL.';
            }
            else {
                $link_data = $this->get_link_data($link_url);
                $this->link_model->publish($link_data, $_SESSION['user_id']);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Add link to a resource from another website';
        $this->load->view('common/header', $data);

        $this->load->view('add/link', $data);
        $this->load->view('common/footer');
    }

    public function edit($link_id = 0)
    {
        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($link['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = "You don't have the proper permissions to edit this link.";
            redirect(base_url('error'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_comment = $this->input->post('description');
            $this->link_model->update_comment($link_id, $new_comment);
            redirect(base_url("user/link/{$link_id}"));
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Edit link - Makwire';
        $this->load->view('common/header', $data);

        $data['item'] = 'link';
        $data['link'] = $link;
        $data['description'] = $link['comment'];
        $data['form_action'] = base_url("link/edit/{$link_id}");
        $data['cancel_url'] = base_url("user/link/{$link_id}");
        $this->load->view('edit/description', $data);
        $this->load->view('common/footer');
    }

    public function delete($link_id = 0)
    {
        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->link_model->delete_link($link, $_SESSION['user_id']);
                $_SESSION['message'] = 'Your link has been successfully deleted.';
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
        $data['title'] = 'Delete link - Makwire';
        $this->load->view('common/header', $data);

        $link_data = [
            'item' => 'link',
            'link' => $link,
            'item_owner_id' => $link['user_id'],
            'form_action' => base_url("link/delete/{$link['link_id']}"),
            'cancel_url' => base_url("user/link/{$link['link_id']}")
        ];

        $data = array_merge($data, $link_data);
        $this->load->view('delete-item', $data);
        $this->load->view('common/footer');
    }

    public function add_comment($link_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comment = trim(strip_tags($this->input->post('description')));
            if (strlen($comment) == 0) {
                $data['error_message'] = 'Please enter a comment.';
            }
            else {
                $this->link_model->add_comment($comment, $link_id);
                redirect(base_url("user/{$_SESSION['user_id']}"));
            }
        }

        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if ($link['user_id'] != $_SESSION['user_id']) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions.';
            redirect(base_url('error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Say something about this link';
        $this->load->view('common/header', $data);

        $data['link'] = $link;
        $data['item'] = 'link';
        $data['form_action'] = base_url("link/add-comment/{$link_id}");
        $this->load->view('add/description', $data);
        $this->load->view('common/footer');
    }

    public function like($link_id = 0)
    {
        try {
            $this->link_model->like($link_id, $_SESSION['user_id']);
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

    public function comment($link_id = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comment = trim(strip_tags($this->input->post('comment')));
            if (!$comment) {
                $data['comment_error'] = 'Please enter a comment.';
            }
            else {
                $this->link_model->comment($link_id, $comment, $_SESSION['user_id']);
                redirect(base_url("link/comments/{$link_id}"));
            }
        }

        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        if (!$this->user_model->are_friends($_SESSION['user_id'], $link['user_id'])) {
            $_SESSION['title'] = 'Permission Denied!';
            $_SESSION['heading'] = 'Permission Denied';
            $_SESSION['message'] = 'You don\'t have the proper permissions to comment on this link.';
            redirect(base_url('error'));
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Comment on this link';
        $this->load->view('common/header', $data);

        $data['link'] = $link;
        $data['object'] = 'link';
        $this->load->view('comment', $data);
        $this->load->view('common/footer');
    }

    public function share($link_id = 0)
    {
        try {
            $this->link_model->share($link_id, $_SESSION['user_id']);
            redirect(base_url("user/{$_SESSION['user_id']}"));
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

    public function likes($link_id = 0, $offset = 0)
    {
        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'People who liked this link';
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
        if (($link['num_likes'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['likes'] = $this->link_model->get_likes($link, $offset, $limit);

        $data['object'] = 'link';
        $data['link'] = $link;
        $this->load->view('show/likes', $data);
        $this->load->view('common/footer');
    }

    public function comments($link_id = 0, $offset = 0)
    {
        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Comments on this link';
        $this->load->view('common/header', $data);

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
        if (($link['num_comments'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['comments'] = $this->link_model->get_comments($link, $offset, $limit, $_SESSION['user_id']);
        $data['object'] = 'link';
        $data['link'] = $link;
        $this->load->view('show/comments', $data);
        $this->load->view('common/footer');
    }

    public function shares($link_id = 0, $offset = 0)
    {
        try {
            $link = $this->link_model->get_link($link_id, $_SESSION['user_id']);
        }
        catch (NotFoundException $e) {
            show_404();
        }

        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'People who shared this link';
        $this->load->view('common/header', $data);

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
        if (($link['num_shares'] - $offset) > $limit) {
            $data['has_next'] = TRUE;
            $data['next_offset'] = ($offset + $limit);
        }

        $data['num_prev'] = $offset;
        $data['shares'] = $this->link_model->get_shares($link, $offset, $limit);

        $data['object'] = 'link';
        $data['link'] = $link;
        $this->load->view('show/shares', $data);
        $this->load->view('common/footer');
    }

    private function get_link_data($url)
    {
        $data = [
            'url' => $url,
            'title' => '',
            'description' => '',
            'image' => '',
            'site' => parse_url($url, PHP_URL_HOST)
        ];

        /// Fetch Open Graph metas.
        $og_metas = $this->fetch_og($url);

        if ( ! empty($og_metas['title'])) {
            $data['title'] = $og_metas['title'];
        }
        if ( ! empty($og_metas['description'])) {
            $data['description'] = $og_metas['description'];
        }
        if ( ! empty($og_metas['image'])) {
            $data['image'] = $og_metas['image'];
        }

        /// If OG metas can't be found, try twitter metas.

        // Fetch named meta tags.
        $meta_tags = get_meta_tags($url);

        if (empty($data['title']) && empty($data['description'] && empty($data['image']))) {
            if ( ! empty($meta_tags['twitter:title'])) {
                $data['title'] = $meta_tags['twitter:title'];
            }
            if ( ! empty($meta_tags['twitter:description'])) {
                $data['description'] = $meta_tags['twitter:description'];
            }
            if ( ! empty($meta_tags['twitter:image'])) {
                $data['image'] = $meta_tags['twitter:image'];
            }
        }

        /// If data is still empty, then fall back to standard HTML title and description.
        if (empty($data['title']) && empty($data['description'])) {
            $html_data = file_get_contents($url);
            $dom = new DomDocument;
            @$dom->loadHTML($html_data);

            $xpath = new DOMXPath($dom);
            # query metatags with og prefix
            $metas = $xpath->query('//*/title');
            $title = $metas[0]->textContent;
            $data['title'] = $title;

            if ( ! empty($meta_tags['description'])) {
                $data['description'] = $meta_tags['description'];
            }
        }

        return $data;
    }

    /**
     * Fetch OG Metatags
     * @param string $url
     *
     * @return array
     */
    private function fetch_og($url)
    {
        $data = file_get_contents($url);
        $dom = new DomDocument;
        @$dom->loadHTML($data);

        $xpath = new DOMXPath($dom);
        # query metatags with og prefix
        $metas = $xpath->query('//*/meta[starts-with(@property, \'og:\')]');

        $og = array();

        foreach($metas as $meta){
            # get property name without og: prefix
            $property = str_replace('og:', '', $meta->getAttribute('property'));
            # get content
            $content = $meta->getAttribute('content');
            $og[$property] = $content;
        }

        return $og;
    }
}
?>
