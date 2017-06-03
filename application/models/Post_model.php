<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimplePost.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/NotFoundException.php');

/**
 * Contains functions related to a post.
 */
class Post_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model', 'activity_model', 'comment_model']);
    }

    /**
     * Gets a post plus other post metadata.
     *
     * Throws NotFoundException if the post cannot be found.
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
            throw new NotFoundException();
        }

        $post = $post_query->row_array();

        // Get the name of the author.
        $post['author'] = $this->user_model->get_profile_name($post['user_id']);

        // Get profile picture of the author.
        $post['profile_pic_path'] = $this->user_model->get_profile_pic_path($post['user_id']);

        // Get the timespan.
        $post['timespan'] = timespan(mysql_to_unix($post['date_entered']), now(), 1);

        // Add data used by views.
        $post['shared'] = FALSE;

        // Check whether the user currently viewing the page is a friend to the
        // original author of the post. This will allow us to only show the
        // like, comment and share buttons to friends of the original author.
        $post['viewer_is_friend_to_owner'] = $this->user_model->are_friends($post['user_id']);

        $simplePost = new SimplePost($post['post_id'], 'post', $post['user_id']);

        // Get the number of likes.
        $post['num_likes'] = $this->activity_model->getNumLikes($simplePost);

        // Get the number of comments.
        $post['num_comments'] = $this->activity_model->getNumComments($simplePost);

        // Get the number of shares.
        $post['num_shares'] = $this->activity_model->getNumShares($simplePost);

        return $post;
    }

    /**
     * Allows a user to add a new post on his status.
     *
     * @param $post the contents of the post.
     * @param $audience target audience for the post. May be group or timeline.
     * @param $audience_id the ID of the targe audience. Same as  user ID
     * if audience timeline.
     */
    public function post($post, $audience, $audience_id)
    {
        // Save the post.
        $post_sql = sprintf("INSERT INTO posts (audience_id, audience, post, user_id) " .
                            "VALUES (%d, '%s', %s, %d)",
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
     * Throws NotFoundException exception if the post is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a post published by a user who is not his friend.
     *
     * @param $post_id the ID of the post in the posts table.
     */
    public function like($post_id)
    {
        // Get the ID of the owner of this post.
        $owner_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {  // Post doesn't exist.
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];
        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        // Record the like.
        $this->activity_model->like(new SimplePost($post_id, 'post', $owner_id));
    }

    /**
     * Shares a post on a user's timeline.
     *
     * Throws NotFoundException if a post is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a post that was published by a user who is not his friend.
     *
     * @param $post_id the ID of the post in the posts table.
     */
    public function share($post_id)
    {
        // Get the ID of the owner of this post.
        $owner_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];
        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        // Share the post.
        $this->activity_model->share(new SimplePost($post_id, 'post', $owner_id));
    }

    /**
     * Records a comment on a post.
     *
     * @param $post_id the ID of the post in the posts table.
     * @param $comment the comment a user made.
     */
    public function comment($post_id, $comment)
    {
        // Get the ID of the owner of this post.
        $owner_sql = sprintf("SELECT user_id FROM posts WHERE post_id = %d",
                            $post_id);
        $owner_result = $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['user_id'];

        // Record the comment.
        $this->utility_model->comment(
            new SimplePost($post_id, 'post', $owner_id),
            $comment
        );
    }

    /**
     * Get users who liked a post.
     *
     * @param $post an array containing post data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this post.
     */
    public function get_likes(&$post, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimplePost($post['post_id'], 'post', $post['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the users who shared a photo.
     *
     * @param $post an array containing post data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who shared this post.
     */
    public function get_shares(&$post, $offset, $limit)
    {
        return $this->activity_model->getShares(
            new SimplePost($post['post_id'], 'post', $post['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the comments made on a post.
     *
     * @param $post an array containing post data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the comments made on this post.
     */
    public function get_comments(&$post, $offset, $limit)
    {
        return $this->activity_model->getComments(
            new SimplePost($post['post_id'], 'post', $post['user_id']),
            $offset,
            $limit
        );
    }
}
?>
