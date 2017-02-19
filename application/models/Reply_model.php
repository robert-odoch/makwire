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

    private function has_liked($reply_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='reply' AND liker_id=%d) " .
                     "LIMIT 1",
                     $reply_id, $_SESSION['user_id']);
        $query = $this->run_query($q);

        if ($query->num_rows() === 1) {
            return TRUE;
        }

        return FALSE;
    }

    public function get_reply($reply_id)
    {
        $q = sprintf("SELECT * FROM comments WHERE (comment_id=%s AND parent_id!=0)",
                     $reply_id);
        $query = $this->run_query($q);
        $reply = $query->row_array();

        // Get the name of the replier.
        $reply['replier'] = $this->user_model->get_name($reply['commenter_id']);

        // Add the timespan.
        $reply['timespan'] = timespan(mysql_to_unix($reply['date_entered']), now(), 1);

        // Has the user liked this reply?
        $reply['liked'] = $this->has_liked($reply['comment_id']);

        // Add the number of likes.
        $reply['num_likes'] = $this->get_num_likes($reply_id);

        return $reply;
    }

    public function like($reply_id)
    {
        if ($this->has_liked($reply_id)) {
            return;
        }

        // Record the like.
        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, 'reply')",
                     $_SESSION['user_id'], $reply_id);
        $this->run_query($q);

        // Get the id of the user who replied.
        $q = sprintf("SELECT commenter_id FROM comments WHERE (comment_id=%d) LIMIT 1",
                     $reply_id);
        $query = $this->run_query($q);
        $parent_id = $query->row()->commenter_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'reply', 'like')",
                     $_SESSION['user_id'], $parent_id, $reply_id);
        $this->run_query($q);
    }

    public function get_num_likes($reply_id)
    {
        $q = sprintf("SELECT like_id FROM likes WHERE (source_type='reply' AND source_id=%d)",
                     $reply_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_likes($reply_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes WHERE (source_type='reply' AND source_id=%d) " .
                     "LIMIT %d, %d",
                     $reply_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();
        $likes = array();
        foreach ($results as $like) {
            // Get the name of the liker.
            $like['liker'] = $this->user_model->get_name($like['liker_id']);

            array_push($likes, $like);
        }

        return $likes;
    }
}
?>
