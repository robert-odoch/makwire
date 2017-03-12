<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Birthday_message_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['user_model', 'reply_model']);
    }

    /*** Utility ***/
    private function handle_error($error)
    {
        print($error);
        exit(1);
    }

    private function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }

    /*** End Utility ***/

    private function has_liked($birthday_message_id)
    {
        // Check whether user has liked to message already.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = 'birthday_message' " .
                                    "AND liker_id = %d) " .
                            "LIMIT 1",
                            $birthday_message_id, $_SESSION['user_id']);
        return ($this->run_query($like_sql)->num_rows() == 1);
    }

    public function get_message($birthday_message_id)
    {
        $message_sql = sprintf("SELECT * " .
                                "FROM birthday_messages " .
                                "WHERE (id = %d)",
                                $birthday_message_id);
        $message_query = $this->run_query($message_sql);
        if ($message_query->num_rows() == 0) {
            return FALSE;
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

    public function like($birthday_message_id)
    {
        // Get the id of the user who sent the message.
        $user_sql = sprintf("SELECT user_id, sender_id " .
                            "FROM birthday_messages WHERE id = %d",
                            $birthday_message_id);
        $user_query = $this->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_liked($birthday_message_id)) {
            return TRUE;
        }

        $user_result = $user_query->row_array();
        if ($user_result['user_id'] != $_SESSION['user_id']) {
            return FALSE;
        }

        // Record the like.
        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'birthday_message')",
                            $_SESSION['user_id'], $birthday_message_id);
        $this->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'birthday_message', 'like')",
                                $_SESSION['user_id'], $user_result['sender_id'],
                                $birthday_message_id);
        $this->run_query($activity_sql);

        return TRUE;
    }
}
?>
