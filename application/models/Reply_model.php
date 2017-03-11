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

    private function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    private function has_liked($reply_id)
    {
        // Check whether this reply belongs to the current user.
        $user_sql = sprintf("SELECT commenter_id " .
                                    "FROM comments " .
                                    "WHERE comment_id = %d " .
                                    "LIMIT 1",
                                    $reply_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->row_array()['commenter_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether the user has already liked the reply.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id = %d AND source_type = 'reply' AND liker_id = %d) " .
                     "LIMIT 1",
                     $reply_id, $_SESSION['user_id']);
        return ($this->run_query($like_sql)->num_rows() == 1);
    }

    public function get_reply($reply_id)
    {
        $reply_sql = sprintf("SELECT * " .
                                "FROM comments " .
                                "WHERE (comment_id = %s AND parent_id != 0)",
                                $reply_id);
        $reply_query = $this->run_query($reply_sql);
        if ($reply_query->num_rows() == 0) {
            return FALSE;
        }

        $reply = $reply_query->row_array();

        // Get the name of the replier.
        $reply['commenter'] = $this->user_model->get_profile_name($reply['commenter_id']);

        // Add profile picture.
        $reply['profile_pic_path'] = $this->user_model->get_profile_pic_path($reply['commenter_id']);

        // Add the timespan.
        $reply['timespan'] = timespan(mysql_to_unix($reply['date_entered']), now(), 1);

        // Add the number of likes.
        $reply['num_likes'] = $this->get_num_likes($reply_id);

        // Add data used by views.
        $reply['viewer_is_friend_to_owner'] = $this->user_model->are_friends($reply['commenter_id']);

        $reply['liked'] = $this->has_liked($reply_id);

        return $reply;
    }

    public function like($reply_id)
    {
        $user_sql = sprintf("SELECT commenter_id " .
                            "FROM comments " .
                            "WHERE comment_id = %d",
                            $reply_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_liked($reply_id)) {
            return TRUE;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['commenter_id'])) {
            return FALSE;
        }

        // Record the like.
        $like_sql = sprintf("INSERT INTO likes " .
                            "(liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'reply')",
                            $_SESSION['user_id'], $reply_id);
        $this->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'reply', 'like')",
                                $_SESSION['user_id'], $reply_result['commenter_id'], $reply_id);
        $this->run_query($activity_sql);

        return TRUE;
    }

    public function get_num_likes($reply_id)
    {
        $likes_sql = sprintf("SELECT COUNT(like_id) " .
                                "FROM likes " .
                                "WHERE (source_type = 'reply' AND source_id = %d)",
                                $reply_id);
        return $this->run_query($likes_sql)->row_array()['COUNT(like_id)'];
    }

    public function get_likes($reply_id, $offset, $limit)
    {
        $likes_sql = sprintf("SELECT * " .
                                "FROM likes " .
                                "WHERE (source_type = 'reply' AND source_id = %d) " .
                                "LIMIT %d, %d",
                                $reply_id, $offset, $limit);
        $likes_query = $this->run_query($likes_sql);

        $likes = $likes_query->result_array();
        foreach ($likes as &$like) {
            $like['profile_pic_path'] = $this->user_model->get_profile_pic_path($like['liker_id']);
            $like['liker'] = $this->user_model->get_profile_name($like['liker_id']);
            $like['timespan'] = timespan(mysql_to_unix($like['date_liked']), now(), 1);
        }
        unset($like);

        return $likes;
    }
}
?>
