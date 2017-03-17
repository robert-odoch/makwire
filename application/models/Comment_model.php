<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/CommentNotFoundException.php');

/**
 * Contains functions relating to comments on a post, photo.
 */
class Comment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model', 'user_model', 'reply_model']);
    }

    /**
     * Checks whether a user has already liked a comment.
     *
     * A user is not allowed ot like his own comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @return TRUE if the user has already liked the comment, or is the owner of the comment.
     */
    private function has_liked($comment_id)
    {
        // Check whether this comment is from the user liking the comment.
        $user_sql = sprintf("SELECT commenter_id FROM comments " .
                            "WHERE comment_id = %d LIMIT 1",
                            $comment_id);
        $query = $this->utility_model->run_query($user_sql);
        if ($query->row_array()['commenter_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether user has liked to comment already.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = 'comment' AND liker_id = %d) " .
                            "LIMIT 1",
                            $comment_id, $_SESSION['user_id']);
        return ($this->utility_model->run_query($like_sql)->num_rows() == 1);
    }

    /**
     * Gets a comment plus other comment metadata.
     *
     * Throws CommentNotFoundException if the comment cannot be found.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @return comment with the given ID.
     */
    public function get_comment($comment_id)
    {
        $comment_sql = sprintf("SELECT commenter_id, comment, source_id, source_type, date_entered " .
                                "FROM comments " .
                                "WHERE (comment_id = %d AND parent_id = 0)",
                                $comment_id);
        $comment_query = $this->utility_model->run_query($comment_sql);
        if ($comment_query->num_rows() == 0) {
            throw new CommentNotFoundException();
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

    /**
     * Gets the number of users who liked a comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @return the number of users who have like this comment.
     */
    public function get_num_likes($comment_id)
    {
        $likes_sql = sprintf("SELECT COUNT(like_id) FROM likes " .
                                "WHERE (source_type = 'comment' AND source_id = %d)",
                                $comment_id);
        return $this->utility_model->run_query($likes_sql)->row_array()['COUNT(like_id)'];
    }

    /**
     * Gets the users who liked a comment plus their metadata.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @param $offset the record to begin fetching from.
     * @param $limit the maximum number of records to return.
     * @return the users who have liked a comment.
     */
    public function get_likes($comment_id, $offset, $limit)
    {
        $likes_sql = sprintf("SELECT * FROM likes " .
                                "WHERE (source_type = 'comment' AND source_id = %d) " .
                                "LIMIT %d, %d",
                                $comment_id, $offset, $limit);
        $likes_query = $this->utility_model->run_query($likes_sql);

        $likes = $likes_query->result_array();
        foreach ($likes as &$like) {
            $like['profile_pic_path'] = $this->user_model->get_profile_pic_path($like['liker_id']);
            $like['liker'] = $this->user_model->get_profile_name($like['liker_id']);
            $like['timespan'] = timespan(mysql_to_unix($like['date_liked']), now(), 1);
        }
        unset($like);

        return $likes;
    }

    /**
     * Gets the number of replies on a comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @return the number of replies on this comment.
     */
    public function get_num_replies($comment_id)
    {
        $replies_sql = sprintf("SELECT COUNT(comment_id) FROM comments " .
                                "WHERE (source_type = 'comment' AND source_id = %d)",
                                $comment_id);
        return $this->utility_model->run_query($replies_sql)->row_array()['COUNT(comment_id)'];
    }

    /**
     * Gets the replies on a comment plus their metadata.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @param $offset the record to begin fetching from.
     * @param $limit the maximum number of records to return.
     * @return the replies on this comment.
     */
    public function get_replies($comment_id, $offset, $limit)
    {
        $replies_sql = sprintf("SELECT comment_id FROM comments " .
                                "WHERE (source_type = 'comment' AND parent_id = %d) " .
                                "LIMIT %d, %d",
                                $comment_id, $offset, $limit);
        $replies_query = $this->utility_model->run_query($replies_sql);
        $results = $replies_query->result_array();

        $replies = array();
        foreach ($results as $r) {
            // Get the detailed reply.
            $reply = $this->reply_model->get_reply($r['comment_id']);
            array_push($replies, $reply);
        }

        return $replies;
    }

    /**
     * Records a like of a comment.
     *
     * Throws CommentNotFoundException exception if the comment can't be found.
     * It may also throw IllegalAccessException if a user attempts to like a comment
     * that was made by a user who is not his friend.
     * A user is not allowed to like his own comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     */
    public function like($comment_id)
    {
        // Get the id of the user who commented.
        $user_sql = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d",
                            $comment_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            throw new CommentNotFoundException();
        }

        if ($this->has_liked($comment_id)) {
            return;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['commenter_id'])) {
            throw new IllegalAccessException();
        }

        // Record the like.
        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'comment')",
                            $_SESSION['user_id'], $comment_id);
        $this->utility_model->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'comment', 'like')",
                                $_SESSION['user_id'], $user_result['commenter_id'], $comment_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Records a reply on a comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @param $reply the reply on this comment.
     */
    public function reply($comment_id, $reply)
    {
        // Record the reply.
        $reply_sql = sprintf("INSERT INTO comments " .
                                "(commenter_id, parent_id, source_id, source_type, comment) " .
                                "VALUES (%d, %d, %d, 'comment', %s)",
                                $_SESSION['user_id'], $comment_id, $comment_id,
                                $this->db->escape($reply));
        $this->utility_model->run_query($reply_sql);

        // Get the id of the user who commented.
        $user_sql = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d",
                                $comment_id);
        $user_result= $this->utility_model->run_query($user_sql)->row_array();

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'comment', 'reply')",
                                $_SESSION['user_id'], $user_result['commenter_id'],
                                $comment_id);
        $this->utility_model->run_query($activity_sql);
    }
}
?>
