<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reply_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
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

    public function get_reply($reply_id)
    {
        $q = sprintf("SELECT * FROM comments WHERE (comment_id=%s AND parent_id!=%d)",
                     $reply_id, 0);
        $query = $this->run_query($q);
        $reply = $query->row_array();

        // Get the name of the replier.
        $q = sprintf("SELECT fname, lname FROM users WHERE user_id=%d LIMIT 1",
                     $reply['commenter_id']);
        $query = $this->run_query($q);

        $replier = ucfirst(strtolower($query->row(0)->lname)) . ' ' . ucfirst(strtolower($query->row(0)->fname));
        $reply['replier'] = $replier;

        // Add the timespan.
        $reply['timespan'] = timespan(mysql_to_unix($reply['date_entered']), now(), 1);

        // Add the number of likes.
        $reply['num_likes'] = $this->get_num_likes($reply_id);

        return $reply;
    }

    public function like($reply_id)
    {
        // Record the like.
        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, %s)",
                     $_SESSION['user_id'], $reply_id, $this->db->escape("reply"));
        $query = $this->run_query($q);

        // Get the id of the user who replied.
        $q = sprintf("SELECT commenter_id FROM comments WHERE (comment_id=%d) LIMIT %d",
                     $reply_id, 1);
        $query = $this->run_query($q);
        $parent_id = $query->row()->commenter_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $_SESSION['user_id'], $parent_id, $reply_id,
                     $this->db->escape("reply"), $this->db->escape("like"));
        $query = $this->run_query($q);
    }

    public function get_num_likes($reply_id)
    {
        $q = sprintf("SELECT like_id FROM likes WHERE (source_type=%s AND source_id=%d)",
                         $this->db->escape("reply"), $reply_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_likes($reply_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes WHERE (source_type=%s AND source_id=%d) " .
                     "ORDER BY date_liked DESC LIMIT %d, %d",
                     $this->db->escape("reply"), $reply_id, $offset, $limit);
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
}
?>
