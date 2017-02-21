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

    private function user_can_access_comment($comment_id)
    {
        // A user must be a friend to the persion who posted what was commented upon.
        $comment_q = sprintf("SELECT source_id, source_type FROM comments WHERE (comment_id=%d)",
                     $comment_id);
        $query = $this->run_query($comment_q);
        if ($query->num_rows() == 0) {
            // No user (even admin) has permission to access a file that doesn't exist.
            return FALSE;
        }

        $comment_result = $query->row_array();
        switch ($comment_result['source_type']) {
        case 'photo':
            $author_q = sprintf("SELECT user_id AS author_id FROM user_images WHERE (image_id=%d)",
                         $comment_result['source_id']);
            break;
        case 'post':
            $author_q = sprintf("SELECT author_id FROM posts WHERE (post_id=%d)",
                         $comment_result['source_id']);
            break;
        default:
            # Do nothing.
            break;
        }
        $author_id = $this->run_query($author_q)->row_array()['author_id'];

        return $this->user_model->are_friends($author_id);
    }
    /*** End Utility ***/

    private function has_liked($comment_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='comment' AND liker_id=%d) " .
                     "LIMIT 1",
                     $comment_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    public function get_comment($comment_id)
    {
        if (!$this->user_can_access_comment($comment_id)) {
            return FALSE;
        }

        $q = sprintf("SELECT commenter_id, comment, date_entered FROM comments " .
                     "WHERE (comment_id=%d AND parent_id=%d)",
                     $comment_id, 0);
        $query = $this->run_query($q);
        $comment = $query->row_array();

        // Get the name of the commenter.
        $comment['commenter'] = $this->user_model->get_name($comment['commenter_id']);

        // Add the number of likes and replies.
        $comment['num_likes'] = $this->get_num_likes($comment_id);
        $comment['num_replies'] = $this->get_num_replies($comment_id);

        // The comment ID.
        $comment['comment_id'] = $comment_id;

        // Add the timespan.
        $comment['timespan'] = timespan(mysql_to_unix($comment['date_entered']), now(), 1);

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
        $results = $query->result_array();

        $likes = array();
        foreach ($results as $like) {
            // Get the name of the liker.
            $like['liker'] = $this->user_model->get_name($like['liker_id']);
            $like['profile_pic_path'] = $this->user_model->get_profile_picture($like['liker_id']);

            array_push($likes, $like);
        }

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
        if (!$this->user_can_access_comment($comment_id)) {
            return FALSE;
        }
        if ($this->has_liked($comment_id)) {
            return TRUE;
        }

        // Record the like.
        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, 'comment')",
                     $_SESSION['user_id'], $comment_id);
        $this->run_query($q);

        // Get the id of the user who commented.
        $q = sprintf("SELECT commenter_id FROM comments WHERE (comment_id=%d) LIMIT 1",
                     $comment_id);
        $query = $this->run_query($q);
        $subject_id = $query->row()->commenter_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'comment', 'like')",
                     $_SESSION['user_id'], $subject_id, $comment_id);
        $this->run_query($q);
    }

    public function reply($comment_id, $reply)
    {
        // Record the reply.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, 'comment', %s)",
                     $_SESSION['user_id'], $comment_id, $comment_id, $this->db->escape($reply));
        $this->run_query($q);

        // Get the id of the user who commented.
        $q = sprintf("SELECT commenter_id FROM comments WHERE (comment_id=%d) LIMIT 1",
                     $comment_id);
        $query = $this->run_query($q);
        $subject_id = $query->row()->commenter_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'comment', 'reply')",
                     $_SESSION['user_id'], $subject_id, $comment_id);
        $this->run_query($q);
    }
}
?>
