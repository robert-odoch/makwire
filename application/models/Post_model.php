<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/PostNotFoundException.php');

/**
 * Contains functions related to a post.
 */
class Post_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model', 'comment_model']);
    }

    /**
     * Gets a post plus other post metadata.
     *
     * Throws PostNotFoundException if the post cannot be found.
     *
     * @param $post_id the ID of the post in the posts table.
     * @return the post with the given ID.
     */
    public function get_post($post_id)
    {
        $post_sql = sprintf("SELECT * FROM posts WHERE post_id = %d",
                     $post_id);
        $post_query = $this->utility_model->run_query($post_sql);
        if ($post_query->num_rows() == 0){
            throw new PostNotFoundException();
        }

        $post = $post_query->row_array();

        // Get the name of the author.
        $post['author'] = $this->user_model->get_profile_name($post['user_id']);

        // Get profile picture of the author.
        $post['profile_pic_path'] = $this->user_model->get_profile_pic_path($post['user_id']);

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

        // Check whether the user currently viewing the page is a friend to the
        // original author of the post. This will allow us to only show the
        // like, comment and share buttons to friends of the original author.
        $post['viewer_is_friend_to_owner'] = $this->user_model->are_friends($post['user_id']);

        return $post;
    }

    /**
     * Checks whether a user has already liked a post.
     *
     * A user is not allowed to like his own post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @return TRUE if this user has already liked the post, or is the owner of the post.
     */
    private function has_liked($post_id)
    {
        // Check whether this post belongs to the current user.
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already liked the post.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = 'post' AND liker_id = %d) " .
                            "LIMIT 1",
                            $post_id, $_SESSION['user_id']);
        return ($this->utility_model->run_query($like_sql)->num_rows() == 1);
    }

    /**
     * Checks whether a user has already shared a post.
     *
     * Until groups are implemented, a user is not allowed to share his own post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @return TRUE this user has already shared the post, or is the owner of the post.
     */
    private function has_shared($post_id)
    {
        // Check whether this post belongs to the current user.
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d LIMIT 1",
                            $post_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already shared the post.
        $share_sql = sprintf("SELECT share_id FROM shares " .
                                "WHERE (subject_id = %d AND user_id = %d AND subject_type='post') " .
                                "LIMIT 1",
                                $post_id, $_SESSION['user_id']);
        return ($this->utility_model->run_query($share_sql)->num_rows() == 1);
    }

    /**
     * Gets the number of users who liked a post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @return the number of likes on this post.
     */
    public function get_num_likes($post_id)
    {
        $likes_sql = sprintf("SELECT COUNT(like_id) FROM likes " .
                                "WHERE (source_id = %d AND source_type = 'post')",
                                $post_id);
        return $this->utility_model->run_query($likes_sql)->row_array()['COUNT(like_id)'];
    }

    /**
     * Get the number of comments made on a post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @return the number of comments on this post.
     */
    public function get_num_comments($post_id)
    {
        $comments_sql = sprintf("SELECT COUNT(comment_id) FROM comments " .
                                "WHERE (source_type = 'post' AND source_id = %d AND parent_id = 0)",
                                $post_id);
        return $this->utility_model->run_query($comments_sql)->row_array()['COUNT(comment_id)'];
    }

    /**
     * Gets the number of users who shared a post.
     *
     * @param post_id the ID of the post in the posts table.
     * @return the number of users who shared this post.
     */
    public function get_num_shares($post_id)
    {
        $shares_sql = sprintf("SELECT COUNT(share_id) FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = 'post')",
                                $post_id);
        return $this->utility_model->run_query($shares_sql)->row_array()['COUNT(share_id)'];
    }

    /**
     * Allows a user to add a new post on his status.
     *
     * @param $post the contents of the post.
     * @param $audience can be group or timeline.
     * @param $audience_id the ID of the audience. Either group ID, or user ID
     * if post is made on a timeline.
     */
    public function post($post, $audience, $audience_id)
    {
        // Save the post.
        $post_sql = sprintf("INSERT INTO posts (audience_id, audience, post, user_id) " .
                            "VALUES (%d, %s, %s, %d)",
                            $audience_id, $audience,
                            $this->db->escape($post), $_SESSION['user_id']);
        $this->utility_model->run_query($post_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'post')",
                                $_SESSION['user_id'], $audience_id, $this->db->insert_id());
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Records a like of a post.
     *
     * Throws PostNotFoundException exception if the post is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a post published by a user who is not his friend.
     *
     * @param $post_id the ID of the post in the posts table.
     */
    public function like($post_id)
    {
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->num_rows() == 0) {  // Post doesn't exist.
            throw new PostNotFoundException();
        }
        if ($this->has_liked($post_id)) {
            return;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['user_id'])) {
            throw new IllegalAccessException();
        }

        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'post')",
                            $_SESSION['user_id'], $post_id);
        $this->utility_model->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'like')",
                                $_SESSION['user_id'], $user_result['user_id'], $post_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Records a comment on a post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @param $comment the comment a user made.
     */
    public function comment($post_id, $comment)
    {
        // Record the comment.
        $comment_sql = sprintf("INSERT INTO comments " .
                                "(commenter_id, parent_id, source_id, source_type, comment) " .
                                "VALUES (%d, %d, %d, 'post', %s)",
                                $_SESSION['user_id'], 0, $post_id,
                                $this->db->escape($comment));
        $this->utility_model->run_query($comment_sql);

        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_result = $this->utility_model->run_query($user_sql)->row_array();

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'comment')",
                                $_SESSION['user_id'], $user_result['user_id'], $post_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Shares a post on a user's timeline.
     *
     * Throws PostNotFoundException if a post is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a post that was published by a user who is not his friend.
     *
     * @param $post_id the ID of the post in the posts table.
     */
    public function share($post_id)
    {
        $user_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            throw new PostNotFoundException();
        }

        if ($this->has_shared($post_id)) {
            return;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['user_id'])) {
            throw new IllegalAccessException();
        }

        // Insert it into the shares table.
        $share_sql = sprintf("INSERT INTO shares (subject_id, user_id, subject_type) " .
                                "VALUES (%d, %d, 'post')",
                                $post_id, $_SESSION['user_id']);
        $this->utility_model->run_query($share_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'post', 'share')",
                                $_SESSION['user_id'], $user_result['user_id'], $post_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Get users who liked a post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this post.
     */
    public function get_likes($post_id, $offset, $limit)
    {
        $likes_sql = sprintf("SELECT * FROM likes " .
                                "WHERE (source_type = 'post' AND source_id = %d) " .
                                "LIMIT %d, %d",
                                $post_id, $offset, $limit);
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
     * Gets the comments made on a post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the comments made on this post.
     */
    public function get_comments($post_id, $offset, $limit)
    {
        $comments_sql = sprintf("SELECT comment_id FROM comments " .
                                "WHERE (source_type = 'post' AND source_id = %d AND parent_id = 0) " .
                                "LIMIT %d, %d",
                                $post_id, $offset, $limit);
        $comments_query = $this->utility_model->run_query($comments_sql);
        $results = $comments_query->result_array();

        $comments = array();
        foreach ($results as $r) {
            // Get the detailed comment.
            $comment = $this->comment_model->get_comment($r['comment_id']);
            array_push($comments, $comment);
        }

        return $comments;
    }

    /**
     * Gets the users who shared a photo.
     *
     * @param $post_id the ID of the post in the posts table.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who shared this post.
     */
    public function get_shares($post_id, $offset, $limit)
    {
        $shares_sql = sprintf("SELECT user_id AS sharer_id, date_shared FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = 'post') " .
                                "LIMIT %d, %d",
                                $post_id, $offset, $limit);
        $shares_query = $this->utility_model->run_query($shares_sql);

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
