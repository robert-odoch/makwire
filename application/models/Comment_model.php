<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_model");
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

    private function has_liked($comment_id)
    {
        // Check whether this comment is from the user liking the comment.
        $q = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d LIMIT 1",
                     $comment_id);
        $query = $this->run_query($q);
        if ($query->row_array()['commenter_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether user has liked to comment already.
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='comment' AND liker_id=%d) " .
                     "LIMIT 1",
                     $comment_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    public function get_comment($comment_id)
    {
        $comment_sql = sprintf("SELECT commenter_id, comment, source_id, source_type, date_entered " .
                                "FROM comments " .
                                "WHERE (comment_id = %d AND parent_id = 0)",
                                $comment_id);
        $comment_query = $this->run_query($comment_sql);
        if ($comment_query->num_rows() == 0) {
            return FALSE;
        }

        $comment = $comment_query->row_array();

        // Get the name of the commenter.
        $comment['commenter'] = $this->user_model->get_profile_name($comment['commenter_id']);

        // Get the profile picture of the commenter.
        $comment['profile_pic_path'] = $this->user_model->get_profile_pic_path($comment['commenter_id']);

        // Add the number of likes and replies.
        $comment['num_likes'] = $this->get_num_likes($comment_id);
        $comment['num_replies'] = $this->get_num_replies($comment_id);

        // The comment ID.
        $comment['comment_id'] = $comment_id;

        // Add the timespan.
        $comment['timespan'] = timespan(mysql_to_unix($comment['date_entered']), now(), 1);

        // Add data used by views.
        $comment['viewer_is_friend_to_owner'] = $this->user_model->are_friends($comment['commenter_id']);

        // Has the user liked this comment?
        $comment['liked'] = $this->has_liked($comment_id);

        return $comment;
    }

    public function get_num_likes($comment_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_type='comment' AND source_id=%d)",
                     $comment_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_likes($comment_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes WHERE (source_type='comment' AND source_id=%d) " .
                     "LIMIT %d, %d",
                     $comment_id, $offset, $limit);
        $query = $this->run_query($q);

        $likes = $query->result_array();
        foreach ($likes as &$like) {
            $like['profile_pic_path'] = $this->user_model->get_profile_pic_path($like['liker_id']);
            $like['liker'] = $this->user_model->get_profile_name($like['liker_id']);
            $like['timespan'] = timespan(mysql_to_unix($like['date_liked']), now(), 1);
        }
        unset($like);

        return $likes;
    }

    public function get_num_replies($comment_id)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type='comment' AND source_id=%d)",
                     $comment_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_replies($comment_id, $offset, $limit)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type='comment' AND parent_id=%d) " .
                     "LIMIT %d, %d",
                     $comment_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $replies = array();
        foreach ($results as $r) {
            // Get the detailed reply.
            $reply = $this->reply_model->get_reply($r['comment_id']);
            array_push($replies, $reply);
        }

        return $replies;
    }

    public function like($comment_id)
    {
        // Get the id of the user who commented.
        $comment_sql = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d",
                                $comment_id);
        $comment_query = $this->run_query($comment_sql);
        if ($comment_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_liked($comment_id)) {
            return TRUE;
        }

        $comment_result = $comment_query->row_array();
        if (!$this->user_model->are_friends($comment_result['commenter_id'])) {
            return FALSE;
        }

        // Record the like.
        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, 'comment')",
                     $_SESSION['user_id'], $comment_id);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'comment', 'like')",
                     $_SESSION['user_id'], $comment_result['commenter_id'], $comment_id);
        $this->run_query($q);

        return TRUE;
    }

    public function reply($comment_id, $reply)
    {
        // Record the reply.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, 'comment', %s)",
                     $_SESSION['user_id'], $comment_id, $comment_id, $this->db->escape($reply));
        $this->run_query($q);

        // Get the id of the user who commented.
        $comment_sql = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d",
                                $comment_id);
        $comment_result= $this->run_query($comment_sql)->row_array();

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'comment', 'reply')",
                     $_SESSION['user_id'], $comment_result['commenter_id'], $comment_id);
        $this->run_query($q);
    }
}
?>
