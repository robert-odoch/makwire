<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimpleReply.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/NotFoundException.php');

/**
 * Contains functions related to a reply.
 */
class Reply_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model', 'activity_model');
    }

    /**
     * Gets a reply plus other reply metadata.
     *
     * Throws NotFoundException if the reply is not on record.
     *
     * @param $reply_id the ID of the reply in the comments table.
     * @return the reply with the given ID.
     */
    public function get_reply($reply_id)
    {
        $reply_sql = sprintf("SELECT c.*, u.profile_name AS commenter " .
                                "FROM comments c " .
                                "LEFT JOIN users u ON(c.commenter_id = u.user_id) " .
                                "WHERE (comment_id = %d AND parent_id != 0)",
                                $reply_id);
        $reply_query = $this->utility_model->run_query($reply_sql);
        if ($reply_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $reply = $reply_query->row_array();

        // Add profile picture.
        $reply['profile_pic_path'] = $this->user_model->get_profile_pic_path($reply['commenter_id']);

        // Add the timespan.
        $reply['timespan'] = timespan(mysql_to_unix($reply['date_entered']), now(), 1);

        // Add data used by views.
        $reply['viewer_is_friend_to_owner'] = $this->user_model->are_friends($reply['commenter_id']);

        $simpleReply = new SimpleReply($reply['comment_id'], 'reply', $reply['commenter_id']);

        // Add the number of likes.
        $reply['num_likes'] = $this->activity_model->getNumLikes($simpleReply);

        // Has the user liked the reply?
        $reply['liked'] = $this->activity_model->isLiked($simpleReply);

        return $reply;
    }

    /**
     * Records a like of a reply.
     *
     * Throws NotFoundException exception if the reply is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a reply that was made by a user who is not his friend.
     *
     * @param $reply_id the ID of the reply in the comments table.
     */
    public function like($reply_id)
    {
        $owner_sql = sprintf("SELECT commenter_id " .
                            "FROM comments " .
                            "WHERE comment_id = %d",
                            $reply_id);
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
        $this->activity_model->like(new SimpleReply($reply_id, 'reply', $owner_id));
    }

    /**
     * Gets users who liked a reply.
     *
     * @param $reply an array containing reply data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this reply.
     */
    public function get_likes(&$reply, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimpleReply($reply['comment_id'], 'reply', $reply['commenter_id']),
            $offset,
            $limit
        );
    }
}
?>
