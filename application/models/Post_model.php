<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("comment_model");
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
        if (!$this->user_can_access_post($post_id)) {
            return FALSE;
        }

        $q = sprintf("SELECT * FROM posts WHERE post_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);

        $post = $query->row_array();

        // Get the name of the author.
        $post['author'] = $this->user_model->get_name($post['author_id']);

        // Get the number of likes.
        $post['num_likes'] = $this->get_num_likes($post_id);

        // Get the number of comments.
        $post['num_comments'] = $this->get_num_comments($post_id);

        // Get the number of shares.
        $post['num_shares'] = $this->get_num_shares($post_id);

        // Has the user liked this post?
        $post['liked'] = $this->has_liked($post_id);

        // Get the timespan.
        $unix_timestamp = mysql_to_unix($post['date_entered']);
        $post['timespan'] = timespan($unix_timestamp, now(), 1);

        return $post;
    }

    private function has_liked($post_id)
    {
        // Check whether this post belongs to the current user.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id = %d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);
        if ($query->row_array()['author_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already liked the post.
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='post' AND liker_id=%d) " .
                     "LIMIT 1",
                     $post_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    private function has_shared($post_id)
    {
        // Check whether this post belongs to the current user.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id = %d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);
        if ($query->row_array()['author_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already shared the post.
        $q = sprintf("SELECT share_id FROM shares " .
                     "WHERE (subject_id = %d AND user_id = %d AND subject_type='post') LIMIT 1",
                     $post_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    // Checks whether a user has the proper permision to access a given post
    private function user_can_access_post($post_id)
    {
        // Get the author of this post.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id=%d",
                     $post_id);
        $query = $this->run_query($q);
        if ($query->num_rows() == 0) {
            // No user (even admin) has permission to access a post that doesn't exist.
            return FALSE;
        }

        $author_id = $query->row()->author_id;
        return $this->user_model->are_friends($author_id);
    }

    public function get_num_likes($post_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='post')",
                     $post_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_num_comments($post_id)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type='post' AND source_id=%d AND parent_id=0)",
                     $post_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_num_shares($post_id)
    {
        $q = sprintf("SELECT share_id FROM shares WHERE (subject_id = %d AND subject_type = 'post')",
                     $post_id);
        return $this->run_query($q)->num_rows();
    }

    public function post($post, $audience_id)
    {
        $q = sprintf("INSERT INTO posts (audience_id, post, author_id) VALUES (%d, %s, %d)",
                     $audience_id, $this->db->escape($post), $_SESSION['user_id']);
        $query = $this->run_query($q);
        if ($query) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function like($post_id)
    {
        if (!$this->user_can_access_post($post_id)) {
            return FALSE;
        }
        if ($this->has_liked($post_id)) {
            return TRUE;
        }

        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, 'post')",
                     $_SESSION['user_id'], $post_id);
        $this->run_query($q);

        // Get the id of the user who posted.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);

        $subject_id = $query->row()->author_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'post', 'like')",
                     $_SESSION['user_id'], $subject_id, $post_id);
        $this->run_query($q);

        return TRUE;
    }

    public function comment($post_id, $comment)
    {
        if (!$this->user_can_access_post($post_id)) {
            return FALSE;
        }

        // Record the comment.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, 'post', %s)",
                     $_SESSION['user_id'], 0, $post_id, $this->db->escape($comment));
        $this->run_query($q);

        // Get the parent_id.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);

        $subject_id = $query->row()->author_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'post', 'comment')",
                     $_SESSION['user_id'], $subject_id, $post_id);
        $this->run_query($q);
    }

    public function share($post_id)
    {
        if (!$this->user_can_access_post($post_id)) {
            return FALSE;
        }
        if ($this->has_shared($post_id)) {
            return TRUE;
        }

        $post_q = sprintf("SELECT author_id, audience FROM posts WHERE post_id=%d LIMIT 1",
                            $post_id);
        $post_result = $this->run_query($post_q)->row_array();
        if ($post_result['audience'] == 'group') {
            return FALSE;  // Group posts can't be shared.
        }

        // Insert it into the shares table.
        $q = sprintf("INSERT INTO shares (subject_id, user_id, subject_type) " .
                     "VALUES (%d, %d, 'post')",
                     $post_id, $_SESSION['user_id']);
                     print($q);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'post', 'share')",
                     $_SESSION['user_id'], $post_result['author_id'], $post_id);
        $this->run_query($q);

        return TRUE;
    }

    public function get_likes($post_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes " .
                     "WHERE (source_type='post' AND source_id=%d) " .
                     "LIMIT %d, %d",
                     $post_id, $offset, $limit);
        $query = $this->run_query($q);

        $likes = $query->result_array();
        foreach ($likes as &$like) {
            // Get the name of the user who liked.
            $like['liker'] = $this->user_model->get_name($like['liker_id']);
            $like['profile_pic_path'] = $this->user_model->get_profile_picture($like['liker_id']);
        }
        unset($like);

        return $likes;
    }

    public function get_comments($post_id, $offset, $limit)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type='post' AND source_id=%d AND parent_id=0) " .
                     "LIMIT %d, %d",
                     $post_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

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
        $q = sprintf("SELECT user_id AS sharer_id FROM shares " .
                     "WHERE (subject_id = %d AND subject_type = 'post') " .
                     "LIMIT %d, %d",
                     $post_id, $offset, $limit);
        $query = $this->run_query($q);

        $shares = $query->result_array();
        foreach ($results as &$share) {
            // Get the name of the user who shared.
            $share['sharer'] = $this->user_model->get_name($share['sharer_id']);
            $share['profile_pic_path'] = $this->user_model->get_profile_picture($share['sharer_id']);
        }
        unset($share);

        return $shares;
    }
}
?>
