<?php
defined('BASEPATH') OR exit('No direct script access allowed');

spl_autoload_register(function ($class) {
    include("exceptions/{$class}.php");
});

/**
 * Contains functions relating to a message sent to a user on his birthday.
 */
class Birthday_message_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model', 'user_model', 'reply_model']);
    }

    /**
     * Checks whether a user has already liked a message.
     *
     * @param $birthday_message_id the id of the message in the birthday_messages table.
     * @return TRUE if the user has already liked this message, or is the owner of the message.
     */
    private function has_liked($birthday_message_id)
    {
        // Check whether user has liked to message already.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = 'birthday_message' " .
                                    "AND liker_id = %d) " .
                            "LIMIT 1",
                            $birthday_message_id, $_SESSION['user_id']);
        return ($this->utility_model->run_query($like_sql)->num_rows() == 1);
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
        $message['liked'] = $this->has_liked($birthday_message_id);

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
        $user_sql = sprintf("SELECT user_id, sender_id " .
                            "FROM birthday_messages WHERE id = %d",
                            $birthday_message_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            throw new MessageNotFoundException();
        }

        if ($this->has_liked($birthday_message_id)) {
            return;
        }

        $user_result = $user_query->row_array();
        if ($user_result['user_id'] != $_SESSION['user_id']) {
            throw new IllegalAccessException();
        }

        // Record the like.
        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'birthday_message')",
                            $_SESSION['user_id'], $birthday_message_id);
        $this->utility_model->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'birthday_message', 'like')",
                                $_SESSION['user_id'], $user_result['sender_id'],
                                $birthday_message_id);
        $this->utility_model->run_query($activity_sql);
    }
}
?>
