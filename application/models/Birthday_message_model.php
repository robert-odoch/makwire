<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimpleBirthdayMessage.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/NotFoundException.php');

/**
 * Contains functions relating to a message sent to a user on his birthday.
 */
class Birthday_message_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'activity_model', 'user_model'
        ]);
    }

    /**
     * Gets a birthday message plus other message metadata.
     *
     * Throws NotFoundException if the message cannot be found on record.
     *
     * @param $birthday_message_id the id of the message in the birthday_messages table.
     * @return birthday message with the given ID plus other data.
     */
    public function get_message($birthday_message_id)
    {
        $message_sql = sprintf("SELECT b.*, u.profile_name AS sender " .
                                "FROM birthday_messages b " .
                                "LEFT JOIN users u ON(b.sender_id = u.user_id) " .
                                "WHERE (id = %d)",
                                $birthday_message_id);
        $message_query = $this->utility_model->run_query($message_sql);
        if ($message_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $message = $message_query->row_array();

        // Get the profile picture of the sender.
        $message['profile_pic_path'] = $this->user_model->get_profile_pic_path($message['sender_id']);

        // Add the timespan.
        $message['timespan'] = timespan(mysql_to_unix($message['date_sent']), now(), 1);

        // Has the user liked this comment?
        $simpleBirthdayMessage = new SimpleBirthdayMessage(
            $message['id'], 'birthday_message', $message['sender_id']
        );
        $message['liked'] = $this->activity_model->isLiked($simpleBirthdayMessage);

        // Get the number of likes.
        $message['num_likes'] = $this->activity_model->getNumLikes($simpleBirthdayMessage);

        // Get number of replies.
        $message['num_replies'] = $this->activity_model->getNumReplies($simpleBirthdayMessage);

        // This will allow us to only show thelike link to friends of the owner.
        $message['viewer_is_friend_to_owner'] = $this->user_model->are_friends($message['sender_id']);

        // Only the user with a birthday and the user who sent this message can
        // reply to it.
        $message['user_can_reply'] = $_SESSION['user_id'] == $message['user_id'] ||
                                     ($_SESSION['user_id'] == $message['sender_id'] &&
                                      $message['num_replies'] > 0);

        return $message;
    }

    /**
     * Records a like of a birthday message.
     *
     * Throws NotFoundException if the message cannot be found on record.
     * It may also throw IllegalAccessException if the user attempts to like a
     * message that was not sent for his birthday.
     *
     * @param $birthday_message_id the id of the message in the birthday_messages table.
     */
    public function like($birthday_message_id)
    {
        // Get the id of the user who sent the message.
        $owner_sql = sprintf("SELECT user_id, sender_id " .
                            "FROM birthday_messages WHERE id = %d",
                            $birthday_message_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['sender_id'];

        if (! $this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        // Record the like.
        $this->activity_model->like(
            new SimpleBirthdayMessage($birthday_message_id, 'birthday_message', $owner_id)
        );
    }

    /**
     * Get users who liked a birthday message.
     *
     * @param $message an array containing photo data.
     * @param $offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this birthday message.
     */
    public function get_likes(&$message, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimpleBirthdayMessage($message['id'], 'birthday_message', $message['sender_id']),
            $offset,
            $limit
        );
    }

    /**
     * Records a reply to a birthday message.
     *
     * @param $message_id the ID of the comment in the birthday_messages table.
     * @param $reply the reply on this message.
     */
    public function reply($message_id, $reply)
    {
        // Get the ID of the owner of this photo.
        $owner_sql = sprintf("SELECT sender_id FROM birthday_messages WHERE id = %d",
                            $message_id);
        $owner_result = $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['sender_id'];

        // Record the reply.
        $this->activity_model->reply(
            new SimpleBirthdayMessage($message_id, 'birthday_message', $owner_id),
            $reply
        );
    }

    public function get_replies(&$message, $offset, $limit)
    {
        return $this->activity_model->getReplies(
            new SimpleBirthdayMessage($message['id'], 'birthday_message', $message['sender_id']),
            $offset,
            $limit
        );
    }
}
?>
