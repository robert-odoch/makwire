<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimpleBirthdayMessage.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/MessageNotFoundException.php');

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
     * Throws MessageNotFoundException if the message cannot be found on record.
     *
     * @param $birthday_message_id the id of the message in the birthday_messages table.
     * @return birthday message with the given ID plus other data.
     */
    public function get_message($birthday_message_id)
    {
        $message_sql = sprintf("SELECT * " .
                                "FROM birthday_messages " .
                                "WHERE (id = %d)",
                                $birthday_message_id);
        $message_query = $this->utility_model->run_query($message_sql);
        if ($message_query->num_rows() == 0) {
            throw new MessageNotFoundException();
        }

        $message = $message_query->row_array();

        // Get the name of the sender.
        $message['sender'] = $this->user_model->get_profile_name($message['sender_id']);

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

        // Check whether the user currently viewing the page is a friend to the
        // owner of the birthday message. This will allow us to only show the
        // like button to friends of the owner.
        $message['viewer_is_friend_to_owner'] = $this->user_model->are_friends($message['sender_id']);

        return $message;
    }

    /**
     * Records a like of a birthday message.
     *
     * Throws MessageNotFoundException if the message cannot be found on record.
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
            throw new MessageNotFoundException();
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
}
?>
