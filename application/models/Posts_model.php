<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('post_model');
    }

    /*** Utility ***/
    private function handle_error($error)
    {
        print($error);
        exit(1);
    }

    private function run_query($q)
    {
        $query = $this->db->query($q);
        if ( ! $query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function get_num_posts($user_id)
    {
        return count($this->get_posts($user_id, 0, 0, FALSE));
    }

    public function get_posts($user_id, $offset, $limit, $use_limit=TRUE)
    {
        if ($use_limit) {
            $q = sprintf("SELECT post_id FROM posts " .
                         "WHERE (audience=%s AND author_id=%d) " .
                         "ORDER BY date_posted DESC LIMIT %d, %d",
                         $this->db->escape('timeline'), $user_id, $offset, $limit);
        }
        else {
            $q = sprintf("SELECT post_id FROM posts " .
                         "WHERE (audience=%s AND author_id=%d) " .
                         "ORDER BY date_posted DESC",
                         $this->db->escape('timeline'), $user_id);
        }

        $query = $this->run_query($q);
        $results = $query->result_array();

        $posts = array();
        foreach($results as $r) {
            // Get the detailed post.
            $post = $this->post_model->get_post($r['post_id']);

            // Get only 540 characters from post if possible.
            $short_post = $this->post_model->get_short_post($post['post'], 540);
            $post['post'] = $short_post['body'];
            $post['has_more'] = $short_post['has_more'];

            array_push($posts, $post);
        }

        return $posts;
    }
}
?>
