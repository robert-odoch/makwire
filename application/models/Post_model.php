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
        $q = sprintf("SELECT * FROM posts WHERE post_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);

        $post = $query->row_array();

        // Get the name of the author.
        $post['author'] = $this->get_post_author($post['author_id']);

        // Get the number of likes.
        $post['num_likes'] = $this->get_num_likes($post['post_id']);

        // Get the number of comments.
        $post['num_comments'] = $this->get_num_comments($post['post_id']);

        // Get the number of shares.
        $post['num_shares'] = $this->get_num_shares($post['post_id']);

        // Has the user liked this post?
        $post['liked'] = $this->has_liked($post_id, $_SESSION['user_id']);

        // Is it a shared post.
        $post['shared'] = FALSE;

        if ($post['parent_id'] != 0) {
            $post['shared'] = TRUE;

            // Get the name and ID of the source.
            $q = sprintf("SELECT author_id FROM posts WHERE post_id=%d LIMIT 1",
                         $post['parent_id']);
            $query = $this->run_query($q);
            $source_id = $query->row()->author_id;

            $post['source_id'] = $source_id;
            $post['source'] = $this->get_post_author($source_id);
        }

        // Get the timespan.
        $unix_timestamp = mysql_to_unix($post['date_posted']);
        $post['timespan'] = timespan($unix_timestamp, now(), 1);

        return $post;
    }

    private function has_liked($post_id, $user_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type=%s AND liker_id=%d) " .
                     "LIMIT %d",
                     $post_id, $this->db->escape("post"),
                     $user_id, 1);
        $query = $this->run_query($q);

        if ($query->num_rows() === 1) {
            return TRUE;
        }

        return FALSE;
    }

    public function get_post_author($author_id)
    {
        $q = sprintf("SELECT fname, lname FROM users WHERE user_id=%d LIMIT 1",
                     $author_id);
        $query = $this->run_query($q);
        $author = ucfirst(strtolower($query->row()->lname)) . ' ' . ucfirst(strtolower($query->row()->fname));

        return $author;
    }

    public function get_num_likes($post_id)
    {
        $q = sprintf("SELECT like_id FROM likes WHERE (source_id=%d AND source_type=%s)",
                     $post_id, $this->db->escape("post"));
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_num_comments($post_id)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type=%s AND source_id=%d AND parent_id=%d)",
                     $this->db->escape("post"), $post_id, 0);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_num_shares($post_id)
    {
        $q = sprintf("SELECT post_id FROM posts WHERE parent_id=%d",
                     $post_id);
        $query = $this->run_query($q);

        return $query->num_rows();
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
        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, %s)",
                     $_SESSION['user_id'], $post_id, $this->db->escape("post"));
        $this->run_query($q);

        // Get the id of the user who posted.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);

        $parent_id = $query->row()->author_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity, audience) " .
                     " VALUES (%d, %d, %s, %s, %s, %s)",
                     $_SESSION['user_id'], $parent_id, $post_id,
                     $this->db->escape('post'), $this->db->escape('like'),
                     $this->db->escape('timeline'));
        $this->run_query($q);
    }
    public function comment($post_id, $comment)
    {
        // Record the comment.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $_SESSION['user_id'], 0, $post_id,
                     $this->db->escape("post"), $this->db->escape($comment));
        $this->run_query($q);

        // Get the parent_id.
        $q = sprintf("SELECT author_id FROM posts WHERE post_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);

        $parent_id = $query->row()->author_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity, audience) " .
                     " VALUES (%d, %d, %s, %s, %s, %s)",
                     $_SESSION['user_id'], $parent_id, $post_id,
                     $this->db->escape('post'), $this->db->escape('comment'),
                     $this->db->escape('timeline'));
        $this->run_query($q);
    }
    public function share($post_id, $audience, $audience_id)
    {
        // Get the post that is being shared.
        $q = sprintf("SELECT author_id, post FROM posts WHERE post_id=%d LIMIT %d",
                     $post_id, 1);
        $query = $this->run_query($q);
        $post = $query->row()->post;
        $post_author = $query->row()->author_id;

        // Insert it into the posts table.
        $q = sprintf("INSERT INTO posts (parent_id, author_id, audience_id, audience, post) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $post_id, $_SESSION['user_id'], $audience_id,
                     $this->db->escape($audience), $this->db->escape($post));
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity, audience) " .
                     " VALUES (%d, %d, %s, %s, %s, %s)",
                     $_SESSION['user_id'], $post_author, $post_id,
                     $this->db->escape('post'), $this->db->escape('share'),
                     $this->db->escape('timeline'));
        $this->run_query($q);
    }

    public function get_likes($post_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes WHERE (source_type=%s AND source_id=%d) " .
                     "ORDER BY date_liked DESC LIMIT %d, %d",
                     $this->db->escape("post"), $post_id, $offset, $limit);
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
    public function get_comments($post_id, $offset, $limit)
    {
        $q = sprintf("SELECT comment_id FROM comments WHERE (source_type=%s AND source_id=%d AND parent_id=%d) " .
                     "ORDER BY date_entered DESC LIMIT %d, %d",
                     $this->db->escape("post"), $post_id, 0, $offset, $limit);
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
    public function get_shares($post_id)
    {
        $q = sprintf("SELECT author_id AS sharer_id FROM posts WHERE parent_id=%d LIMIT 1",
                     $post_id);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $shares = array();
        foreach ($results as $share) {
            // Get the name of the user who shared.
            $q = sprintf("SELECT fname, lname FROM users WHERE user_id=%d LIMIT 1",
                         $share['sharer_id']);
            $query = $this->run_query($q);

            $sharer = ucfirst(strtolower($query->row(0)->lname)) . ' ' . ucfirst(strtolower($query->row(0)->fname));
            $share['sharer'] = $sharer;
            array_push($shares, $share);
        }

        return $shares;
    }
}
?>
