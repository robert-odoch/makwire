<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("reply_model");
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

    public function get_comment($comment_id)
    {
        $q = sprintf("SELECT commenter_id, comment, date_entered FROM comments " .
                     "WHERE (comment_id=%d AND parent_id=%d)",
                     $comment_id, 0);
        $query = $this->run_query($q);
        $comment = $query->row_array();

        // Get the name of the commenter.
        $q = sprintf("SELECT fname, lname FROM users WHERE user_id=%d LIMIT 1",
                     $comment['commenter_id']);
        $query = $this->run_query($q);

        $commenter = ucfirst(strtolower($query->row(0)->lname)) . ' ' . ucfirst(strtolower($query->row(0)->fname));
        $comment['commenter'] = $commenter;

        // Add the number of likes and replies.
        $comment['num_likes'] = $this->get_num_likes($comment_id);
        $comment['num_replies'] = $this->get_num_replies($comment_id);

        // The comment ID.
        $comment['comment_id'] = $comment_id;

        // Add the timespan.
        $comment['timespan'] = timespan(mysql_to_unix($comment['date_entered']), now(), 1);

        return $comment;
    }

    public function get_num_likes($comment_id)
    {
        $q = sprintf("SELECT like_id FROM likes WHERE (source_type=%s AND source_id=%d)",
                     $this->db->escape("comment"), $comment_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_likes($comment_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes WHERE (source_type=%s AND source_id=%d) " .
                     "ORDER BY date_liked DESC LIMIT %d, %d",
                     $this->db->escape("comment"), $comment_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();
        $likes = array();
        foreach ($results as $like) {
            // Get the name of the liker.
            $q = sprintf("SELECT fname, lname FROM users WHERE user_id=%d LIMIT 1",
                         $like['liker_id']);
            $query = $this->run_query($q);

            $liker = ucfirst(strtolower($query->row(0)->lname)) . ' ' . ucfirst(strtolower($query->row(0)->fname));
            $like['liker'] = $liker;
            array_push($likes, $like);
        }

        return $likes;
    }

    public function get_num_replies($comment_id)
    {
        $q = sprintf("SELECT comment_id FROM comments WHERE (source_type=%s AND source_id=%d)",
                     $this->db->escape("comment"), $comment_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_replies($comment_id, $offset, $limit)
    {
        $q = sprintf("SELECT comment_id FROM comments WHERE (source_type=%s AND parent_id=%d) " .
                     "ORDER BY date_entered DESC LIMIT %d, %d",
                     $this->db->escape("comment"), $comment_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $replies = array();
        foreach ($results as $r) {
            // Get the detailed reply.
            $reply = $this->reply_model->get_reply($r['comment_id']);
            $reply['num_likes'] = $this->reply_model->get_num_likes($reply['comment_id']);

            array_push($replies, $reply);
        }

        return $replies;
    }

    public function like($comment_id)
    {
        // Record the like.
        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, %s)",
                     $_SESSION['user_id'], $comment_id, $this->db->escape("comment"));
        $this->run_query($q);

        // Get the id of the user who commented.
        $q = sprintf("SELECT commenter_id FROM comments WHERE (comment_id=%d) LIMIT %d",
                     $comment_id, 1);
        $query = $this->run_query($q);
        $parent_id = $query->row()->commenter_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $_SESSION['user_id'], $parent_id, $comment_id,
                     $this->db->escape("comment"), $this->db->escape("like"));
        $this->run_query($q);
    }

    public function reply($comment_id, $reply)
    {
        // Record the reply.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $_SESSION['user_id'], $comment_id, $comment_id,
                     $this->db->escape("comment"), $this->db->escape($reply));
        $this->run_query($q);

        // Get the id of the user who commented.
        $q = sprintf("SELECT commenter_id FROM comments WHERE (comment_id=%d) LIMIT %d",
                     $comment_id, 1);
        $query = $this->run_query($q);
        $parent_id = $query->row()->commenter_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $_SESSION['user_id'], $parent_id, $comment_id,
                     $this->db->escape("comment"), $this->db->escape("reply"));
        $this->run_query($q);
    }
}
?>
