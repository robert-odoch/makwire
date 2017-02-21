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

    private function user_can_access_reply($reply_id)
    {
        // A user must be a friend to the persion who posted what was commented upon.
        // Get the ID of the comment that was replied upon.
        $parent_id_q = sprintf("SELECT parent_id FROM comments WHERE (comment_id=%d)",
                            $reply_id);

        // Get the type of object that was commented upon.
        $comment_q = sprintf("SELECT source_id, source_type FROM comments WHERE (comment_id=(%s))",
                             $parent_id_q);
        $query = $this->run_query($comment_q);
        if ($query->num_rows() == 0) {
            // No user (even admin) has permission to access a file that doesn't exist.
            return FALSE;
        }

        // Get the post who's comment was replied upon.
        $reply_result = $query->row_array();
        switch ($reply_result['source_type']) {
        case 'photo':
            $author_q = sprintf("SELECT user_id AS author_id FROM user_images WHERE (image_id=%d)",
                                $reply_result['source_id']);
            break;
        case 'post':
            $author_q = sprintf("SELECT author_id FROM posts WHERE (post_id=%d)",
                                $reply_result['source_id']);
            break;
        default:
            # Do nothing.
            break;
        }
        $author_id = $this->run_query($author_q)->row_array()['author_id'];

        return $this->user_model->are_friends($author_id);
    }
    /*** End Utility ***/

    private function has_liked($reply_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='reply' AND liker_id=%d) " .
                     "LIMIT 1",
                     $reply_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    public function get_reply($reply_id)
    {
        if (!$this->user_can_access_reply($reply_id)) {
            return FALSE;
        }

        $q = sprintf("SELECT * FROM comments WHERE (comment_id=%s AND parent_id!=0)",
                     $reply_id);
        $query = $this->run_query($q);
        $reply = $query->row_array();

        // Get the name of the replier.
        $reply['replier'] = $this->user_model->get_name($reply['commenter_id']);

        // Add the timespan.
        $reply['timespan'] = timespan(mysql_to_unix($reply['date_entered']), now(), 1);

        // Has the user liked this reply?
        $reply['liked'] = $this->has_liked($reply_id);

        // Add the number of likes.
        $reply['num_likes'] = $this->get_num_likes($reply_id);

        return $reply;
    }

    public function like($reply_id)
    {
        if (!$this->user_can_access_reply($reply_id)) {
            return FALSE;
        }
        if ($this->has_liked($reply_id)) {
            return TRUE;
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
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'reply', 'like')",
                     $_SESSION['user_id'], $parent_id, $reply_id);
        $this->run_query($q);
    }

    public function get_num_likes($reply_id)
    {
        $q = sprintf("SELECT like_id FROM likes WHERE (source_type='reply' AND source_id=%d)",
                     $reply_id);
        return $this->run_query($q)->num_rows();
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
