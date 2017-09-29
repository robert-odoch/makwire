<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

/**
 * Contains functions relating to a message sent to a user on his birthday.
 */
class Birthday_message_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['activity_model', 'user_model']);
    }

    /**
     * Gets a birthday message plus other message metadata.
     *
     * Throws NotFoundException if the message cannot be found on record.
     *
     * @param $birthday_message_id the id of the message in the birthday_messages table.
     * @return birthday message with the given ID plus other data.
     */
    public function get_message($birthday_message_id, $visitor_id)
    {
        $message_sql = sprintf("SELECT b.*, u.profile_name AS sender
                                FROM birthday_messages b
                                LEFT JOIN users u ON(b.sender_id = u.user_id)
                                WHERE (id = %d)",
                                $birthday_message_id);
        $message_query = $this->db->query($message_sql);
        if ($message_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $message = $message_query->row_array();

        // Get the profile picture of the sender.
        $message['profile_pic_path'] = $this->user_model->get_profile_pic_path($message['sender_id']);

        // Add the timespan.
        $message['timespan'] = timespan(mysql_to_unix($message['date_sent']), now(), 1);

        // Has the user liked this comment?
        $simpleBirthdayMessage = new SimpleBirthdayMessage($message['id'], $message['sender_id']);
        $message['liked'] = $this->activity_model->isLiked($simpleBirthdayMessage, $visitor_id);

        // Get the number of likes.
        $message['num_likes'] = $this->activity_model->getNumLikes($simpleBirthdayMessage);

        // Get number of replies.
        $message['num_replies'] = $this->activity_model->getNumReplies($simpleBirthdayMessage);

        // This will allow us to only show thelike link to friends of the owner.
        $message['viewer_is_friend_to_owner'] = $this->user_model->are_friends($visitor_id, $message['sender_id']);

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
    public function like($message_id, $user_id)
    {
        // Get the id of the user who sent the message.
        $owner_sql = sprintf("SELECT user_id, sender_id FROM birthday_messages WHERE id = %d",
                            $message_id);
        $owner_query = $this->db->query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['sender_id'];

        if ( ! $this->user_model->are_friends($user_id, $owner_id)) {
            throw new IllegalAccessException(
                "You don't have the proper permissions to like this message."
            );
        }

        // Record the like.
        $simpleBirthdayMessage = new SimpleBirthdayMessage($message_id, $owner_id);
        return $this->activity_model->like($simpleBirthdayMessage, $user_id);
    }

    public function get_num_likes($message_id)
    {
        $owner_sql = sprintf('SELECT sender_id FROM birthday_messages WHERE id = %d',
                                $message_id);
        $owner_query = $this->db->query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_id = $owner_query->row_array()['sender_id'];
        $simpleBirthdayMessage = new SimpleBirthdayMessage($message_id, $owner_id);

        return $this->activity_model->getNumLikes($simpleBirthdayMessage);
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
            new SimpleBirthdayMessage($message['id'], $message['sender_id']),
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
    public function reply($message_id, $reply, $user_id)
    {
        // Get the ID of the owner of this photo.
        $owner_sql = sprintf("SELECT sender_id FROM birthday_messages WHERE id = %d",
                            $message_id);
        $owner_result = $this->db->query($owner_sql)->row_array();
        $owner_id = $owner_result['sender_id'];

        // Record the reply.
        $this->activity_model->reply(
            new SimpleBirthdayMessage($message_id, $owner_id),
            $reply,
            $user_id
        );
    }

    public function get_replies(&$message, $offset, $limit, $visitor_id)
    {
        return $this->activity_model->getReplies(
            new SimpleBirthdayMessage($message['id'], $message['sender_id']),
            $offset,
            $limit,
            $visitor_id
        );
    }

    /**
    * Gets the number of messages sent to a user on his birthday.
    *
    * @param $user_id ID of the user who had a birthday.
    * @param $age the age he had reached on his birthday.
    * @return number of messages sent to a user on his birthday.
    */
    public function get_num_birthday_messages($user_id, $age)
    {
        $sql = sprintf("SELECT COUNT(id) FROM birthday_messages WHERE (user_id = %d AND age = %d)",
                        $user_id, $age);
        $query = $this->db->query($sql);

        return $query->row_array()['COUNT(id)'];
    }

    /**
    * Gets the messages that were sent to a user on his birthday.
    *
    * @param $user_id ID of the user who had a birthday.
    * @param $age the age he had reached on his birthday.
    * @param $offset
    * @param $limit
    */
    public function get_birthday_messages($user_id, $age, $offset, $limit)
    {
        $sql = sprintf("SELECT id FROM birthday_messages WHERE (user_id = %d AND age = %d)
                        LIMIT %d, %d",
                        $user_id, $age, $offset, $limit);
        $query = $this->db->query($sql);
        $messages = $query->result_array();

        foreach ($messages as &$m) {
            $m = $this->get_message($m['id'], $user_id);
        }
        unset($m);

        return $messages;
    }

    /**
     * Sends a birthday message to a user.
     *
     * @param $message the message to be sent.
     * @param $receiver_id ID of the user to send the message to.
     * @param $age the age he had reached on his birthday.
     */
    public function send_birthday_message($sender_id, $message, $receiver_id, $age)
    {
        // Record the message.
        $sql = sprintf("INSERT INTO birthday_messages (user_id, sender_id, message, age)
                        VALUES (%d, %d, %s, %d)",
                        $receiver_id, $sender_id,
                        $this->db->escape($message), $age);
        $this->db->query($sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities
                                (actor_id, subject_id, source_id, source_type, activity)
                                VALUES (%d, %d, %d, 'user', 'message')",
                                $sender_id, $receiver_id, $receiver_id);
        $this->db->query($activity_sql);
    }
}
?>
