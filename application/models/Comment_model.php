<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimplePost.php');
require_once('classes/SimpleLink.php');
require_once('classes/SimplePhoto.php');
require_once('classes/SimpleVideo.php');
require_once('classes/SimpleComment.php');
require_once('exceptions/NotFoundException.php');
require_once('exceptions/IllegalAccessException.php');

/**
 * Contains functions relating to comments on a post, photo.
 */
class Comment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'activity_model', 'user_model', 'reply_model'
        ]);
    }

    /**
     * Gets a comment plus other comment metadata.
     *
     * Throws NotFoundException if the comment cannot be found.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @return comment with the given ID.
     */
    public function get_comment($comment_id)
    {
        $comment_sql = sprintf("SELECT commenter_id, comment, source_id, source_type, " .
                                "date_entered, u.profile_name AS commenter " .
                                "FROM comments c " .
                                "LEFT JOIN users u ON(c.commenter_id = u.user_id) " .
                                "WHERE (comment_id = %d AND parent_id = 0)",
                                $comment_id);
        $comment_query = $this->utility_model->run_query($comment_sql);
        if ($comment_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $comment = $comment_query->row_array();

        // Get the profile picture of the commenter.
        $comment['profile_pic_path'] = $this->user_model->get_profile_pic_path($comment['commenter_id']);

        // The comment ID.
        $comment['comment_id'] = $comment_id;

        // Add the timespan.
        $comment['timespan'] = timespan(mysql_to_unix($comment['date_entered']), now(), 1);

        // Add data used by views.
        $comment['viewer_is_friend_to_owner'] = $this->user_model->are_friends($comment['commenter_id']);

        $simpleComment = new SimpleComment($comment['comment_id'], $comment['commenter_id']);

        // Add the number of likes and replies.
        $comment['num_likes'] = $this->activity_model->getNumLikes($simpleComment);
        $comment['num_replies'] = $this->activity_model->getNumReplies($simpleComment);

        // Has the user liked this comment?
        $comment['liked'] = $this->activity_model->isLiked($simpleComment);

        return $comment;
    }

    /**
     * Records a like of a comment.
     *
     * Throws NotFoundException exception if the comment can't be found.
     * It may also throw IllegalAccessException if a user attempts to like a comment
     * that was made by a user who is not his friend.
     * A user is not allowed to like his own comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     */
    public function like($comment_id)
    {
        // Get the id of the user who commented.
        $owner_sql = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d",
                            $comment_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['commenter_id'];
        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        // Record the like.
        $this->activity_model->like(new SimpleComment($comment_id, $owner_id));
    }

    /**
     * Records a reply on a comment.
     *
     * @param $comment_id the ID of the comment in the comments table.
     * @param $reply the reply on this comment.
     */
    public function reply($comment_id, $reply)
    {
        // Get the id of the user who commented.
        $owner_sql = sprintf("SELECT commenter_id FROM comments WHERE comment_id = %d",
                            $comment_id);
        $owner_result= $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['commenter_id'];

        // Record the reply.
        $this->activity_model->reply(
            new SimpleComment($comment_id, $owner_id),
            $reply
        );
    }

    /**
     * Gets the users who liked a comment plus their metadata.
     *
     * @param $comment an array containing comment data.
     * @param $offset the record to begin fetching from.
     * @param $limit the maximum number of records to return.
     * @return the users who have liked a comment.
     */
    public function get_likes(&$comment, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimpleComment($comment['comment_id'], $comment['commenter_id']),
            $offset,
            $limit
        );
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

    public function delete_comment($comment_id)
    {
        $source_sql = sprintf('SELECT commenter_id FROM comments WHERE comment_id = %d',
                                $comment_id);
        $source_query = $this->db->query($source_sql);
        if ($source_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $source_result = $source_query->row_array();
        if ($source_result['commenter_id'] != $_SESSION['user_id']) {
            throw new IllegalAccessException();
        }

        $commentable = NULL;
        switch ($source_result['source_type']) {
        case 'post':
            $owner_sql = sprintf('SELECT user_id FROM posts WHERE post_id = %d',
                                    $source_result['source_id']);
            $owner_id = $this->db->query($owner_query)->row_array()['user_id'];
            $commentable = new SimplePost($source_result['source_id'], $owner_id);
            break;
        case 'photo':
            $owner_sql = sprintf('SELECT user_id FROM user_photos WHERE photo_id = %d',
                                    $source_result['source_id']);
            $owner_id = $this->db->query($owner_query)->row_array()['user_id'];
            $commentable = new SimplePhoto($source_result['source_id'], $owner_id);
            break;
        case 'video':
            $owner_sql = sprintf('SELECT user_id FROM videos WHERE video_id = %d',
                                    $source_result['source_id']);
            $owner_id = $this->db->query($owner_query)->row_array()['user_id'];
            $commentable = new SimpleVideo($source_result['source_id'], $owner_id);
            break;
        case 'link':
            $owner_sql = sprintf('SELECT user_id FROM links WHERE link_id = %d',
                                    $source_result['source_id']);
            $owner_id = $this->db->query($owner_query)->row_array()['user_id'];
            $commentable = new SimpleLink($source_result['source_id'], $owner_id);
            break;
        default:
            # do nothing...
            break;
        }

        $this->activity_model->deleteComment($commentable, $comment_id);
    }
}
?>
