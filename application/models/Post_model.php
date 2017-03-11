<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
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

    public function get_short_post($post, $num_chars)
    {
        $short_post = array();

        $word_count = str_word_count($post, 2);
        $word_positions = array_keys($word_count);
        $last_word_position = array_pop($word_positions);
        if ((strlen($post) < $num_chars) ||
            ((strlen($post) > $num_chars) && ($last_word_position <= $num_chars))) {
            $short_post['body'] = $post;
            $short_post['has_more'] = FALSE;
        }
        else {
            $sp = substr($post, 0, $num_chars);
            $word_count1 = str_word_count($sp, 2);
            $word_positions = array_keys($word_count1);
            $last_word_position = array_pop($word_positions);
            $short_post['body'] = substr($post, 0, ($last_word_position + strlen($word_count[$last_word_position]))) . "...";
            $short_post['has_more'] = TRUE;
        }

        return $short_post;
    }

    public function get_post($post_id)
    {
        $post_sql = sprintf("SELECT * FROM posts WHERE post_id = %d",
                     $post_id);
        $post_query = $this->run_query($post_sql);
        if ($post_query->num_rows() == 0){
            return FALSE;
        }

        $post = $post_query->row_array();

        // Get the name of the author.
        $post['author'] = $this->user_model->get_profile_name($post['user_id']);

        // Get the number of likes.
        $post['num_likes'] = $this->get_num_likes($post_id);

        // Get the number of comments.
        $post['num_comments'] = $this->get_num_comments($post_id);

        // Get the number of shares.
        $post['num_shares'] = $this->get_num_shares($post_id);

        // Get the timespan.
        $post['timespan'] = timespan(mysql_to_unix($post['date_entered']), now(), 1);

        // Add data used by views.
        $post['shared'] = FALSE;

        $post['author_name_ends_with_s'] = $this->user_model->name_ends_with('s', $post['author']);

        // Check whether the user currently viewing the page is a friend to the
        // original author of the post. This will allow us to only show the
        // like, comment and share buttons to friends of the original author.
        $post['viewer_is_friend_to_owner'] = $this->user_model->are_friends($post['user_id']);

        return $post;
    }

    private function has_liked($post_id)
    {
        // Check whether this post belongs to the current user.
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already liked the post.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = 'post' AND liker_id = %d) " .
                            "LIMIT 1",
                            $post_id, $_SESSION['user_id']);
        return ($this->run_query($like_sql)->num_rows() == 1);
    }

    private function has_shared($post_id)
    {
        // Check whether this post belongs to the current user.
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d LIMIT 1",
                            $post_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already shared the post.
        $share_sql = sprintf("SELECT share_id FROM shares " .
                                "WHERE (subject_id = %d AND user_id = %d AND subject_type='post') " .
                                "LIMIT 1",
                                $post_id, $_SESSION['user_id']);
        return ($this->run_query($share_sql)->num_rows() == 1);
    }

    public function get_num_likes($post_id)
    {
        $likes_sql = sprintf("SELECT COUNT(like_id) FROM likes " .
                                "WHERE (source_id = %d AND source_type = 'post')",
                                $post_id);
        return $this->run_query($likes_sql)->row_array()['COUNT(like_id)'];
    }

    public function get_num_comments($post_id)
    {
        $comments_sql = sprintf("SELECT COUNT(comment_id) FROM comments " .
                                "WHERE (source_type = 'post' AND source_id = %d AND parent_id = 0)",
                                $post_id);
        return $this->run_query($comments_sql)->row_array()['COUNT(comment_id)'];
    }

    public function get_num_shares($post_id)
    {
        $shares_sql = sprintf("SELECT COUNT(share_id) FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = 'post')",
                                $post_id);
        return $this->run_query($shares_sql)->row_array()['COUNT(share_id)'];
    }

    public function post($post, $audience, $audience_id)
    {
        // Save the post.
        $post_sql = sprintf("INSERT INTO posts (audience_id, audience, post, user_id) " .
                            "VALUES (%d, %s, %s, %d)",
                            $audience_id, $audience,
                            $this->db->escape($post), $_SESSION['user_id']);
        $this->run_query($post_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'post')",
                                $_SESSION['user_id'], $audience_id, $this->db->insert_id());
        $this->run_query($activity_sql);
    }

    public function like($post_id)
    {
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_liked($post_id)) {
            return TRUE;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['user_id'])) {
            return FALSE;
        }

        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'post')",
                            $_SESSION['user_id'], $post_id);
        $this->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'like')",
                                $_SESSION['user_id'], $post_result['user_id'], $post_id);
        $this->run_query($activity_sql);

        return TRUE;
    }

    public function comment($post_id, $comment)
    {
        // Record the comment.
        $comment_sql = sprintf("INSERT INTO comments " .
                                "(commenter_id, parent_id, source_id, source_type, comment) " .
                                "VALUES (%d, %d, %d, 'post', %s)",
                                $_SESSION['user_id'], 0, $post_id,
                                $this->db->escape($comment));
        $this->run_query($comment_sql);

        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_result = $this->run_query($user_sql)->row_array();

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'comment')",
                                $_SESSION['user_id'], $user_result['user_id'], $post_id);
        $this->run_query($activity_sql);
    }

    public function share($post_id)
    {
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_shared($post_id)) {
            return TRUE;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['user_id'])) {
            return FALSE;
        }

        // Insert it into the shares table.
        $share_sql = sprintf("INSERT INTO shares (subject_id, user_id, subject_type) " .
                                "VALUES (%d, %d, 'post')",
                                $post_id, $_SESSION['user_id']);
        $this->run_query($share_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'share')",
                                $_SESSION['user_id'], $user_result['user_id'], $post_id);
        $this->run_query($activity_sql);

        return TRUE;
    }

    public function get_likes($post_id, $offset, $limit)
    {
        $likes_sql = sprintf("SELECT * FROM likes " .
                                "WHERE (source_type = 'post' AND source_id = %d) " .
                                "LIMIT %d, %d",
                                $post_id, $offset, $limit);
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

    public function get_comments($post_id, $offset, $limit)
    {
        $comments_sql = sprintf("SELECT comment_id FROM comments " .
                                "WHERE (source_type = 'post' AND source_id = %d AND parent_id = 0) " .
                                "LIMIT %d, %d",
                                $post_id, $offset, $limit);
        $comments_query = $this->run_query($comments_sql);
        $results = $comments_query->result_array();

        $comments = array();
        foreach ($results as $r) {
            // Get the detailed comment.
            $comment = $this->comment_model->get_comment($r['comment_id']);
            array_push($comments, $comment);
        }

        return $comments;
    }

    public function get_shares($post_id, $offset, $limit)
    {
        $shares_sql = sprintf("SELECT user_id AS sharer_id, date_shared FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = 'post') " .
                                "LIMIT %d, %d",
                                $post_id, $offset, $limit);
        $shares_query = $this->run_query($shares_sql);

        $shares = $shares_query->result_array();
        foreach ($shares as &$share) {
            $share['profile_pic_path'] = $this->user_model->get_profile_pic_path($share['sharer_id']);
            $share['sharer'] = $this->user_model->get_profile_name($share['sharer_id']);
            $share['timespan'] = timespan(mysql_to_unix($share['date_shared']), now(), 1);
        }
        unset($share);

        return $shares;
    }
}
?>
