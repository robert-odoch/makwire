<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('post_model');
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
            handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function confirm_logged_in()
    {
        $q = sprintf("SELECT logged_in FROM users WHERE user_id=%d",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);

        if ($query->row()->logged_in == 0) {
            unset($_SESSION['user_id']);
            $_SESSION = array();
            $_SESSION['message'] = "This account was logged out from another location, " .
                                   "please log in again to continue using this account. " .
                                   "We are sorry for bothering you.";
            redirect(base_url("login/"));
        }
    }

    public function create_dummy_user($user)
    {
        $q = sprintf("INSERT INTO users (dob, fname, lname, email, gender, uname, passwd) " .
                     "VALUES (%s, %s, %s, %s, %s, %s, %s)", $this->db->escape($user['dob']),
                     $this->db->escape($user['firstname']), $this->db->escape($user['lastname']),
                     $this->db->escape($user['email']), $this->db->escape($user['gender']),
                     $this->db->escape($user['username']), $this->db->escape(password_hash($user['password'], PASSWORD_BCRYPT)));
        $query = $this->run_query($q);
    }

    public function get_full_name($user_id)
    {
        $q = sprintf("SELECT fname, lname FROM users WHERE user_id=%d", $user_id);
        $query = $this->run_query($q);
        $full_name = ucfirst(strtolower($query->row()->lname)) . ' ' . ucfirst(strtolower($query->row()->fname));

        return $full_name;
    }

    public function get_num_messages($filter=TRUE)
    {
        if ($filter)
            return count($this->get_messages(0, 0, FALSE, TRUE));
        else
            return count($this->get_messages(0, 0, FALSE, FALSE));
    }

    public function get_messages($offset, $limit, $use_limit=TRUE, $filter=TRUE)
    {
        if ($use_limit) {
            if ($filter) {
                $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent FROM messages " .
                             "WHERE (seen=%d AND receiver_id=%d) ORDER BY date_sent DESC LIMIT %d, %d",
                             0, $_SESSION['user_id'], $offset, $limit);
            }
            else {
                $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent FROM messages " .
                             "WHERE receiver_id=%d ORDER BY date_sent DESC LIMIT %d, %d",
                             $_SESSION['user_id'], $offset, $limit);
            }
        }
        else {
            if ($filter) {
                $q = sprintf("SELECT sender_id, message, date_sent FROM messages " .
                             "WHERE (seen=%d AND receiver_id=%d)",
                             0, $_SESSION['user_id']);
            }
            else {
                $q = sprintf("SELECT sender_id, message, date_sent FROM messages WHERE receiver_id=%d",
                             $_SESSION['user_id']);
            }
        }

        $query = $this->run_query($q);
        $results = $query->result_array();

        $messages = array();
        foreach ($results as $msg) {
            $msg['sender'] = $this->get_full_name($msg['sender_id']);
            if (isset($msg['seen']) && ($msg['seen'] == 0) && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $q = sprintf("UPDATE messages SET seen=%d WHERE (message_id=%d) LIMIT 1",
                             1, $msg['message_id']);
                $query = $this->run_query($q);
            }

            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);

            array_push($messages, $msg);
        }

        return $messages;
    }

    public function get_num_chat_users($filter=TRUE)
    {
        return count($this->get_chat_users(TRUE));
    }

    public function active($user_id)
    {
        $q = sprintf("SELECT logged_in FROM users WHERE user_id=%d LIMIT 1",
                     $user_id);
        $query = $this->run_query($q);

        if ($query->row()->logged_in) {
            return TRUE;
        }

        return FALSE;
    }

    public function get_friends($user_id)
    {
        // Get the friends who sent her friend requests.
        $q = sprintf("SELECT friend_id FROM friends WHERE user_id=%d",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        $results = $query->result_array();

        // Get friends whom she sent friend requests.
        $q = sprintf("SELECT user_id FROM friends WHERE friend_id=%d",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        $results1 = $query->result_array();

        // Make friend_id uniform and merge the two arrays.
        foreach ($results1 as $r) {
            $r['friend_id'] = $r['user_id'];
            unset($r['user_id']);
            array_push($results, $r);
        }

        $friends = array();
        foreach ($results as $friend) {
            // Get this friend's name.
            $friend['full_name'] = $this->get_full_name($friend['friend_id']);

            array_push($friends, $friend);
        }

        return $friends;
    }

    public function get_chat_users($filter=TRUE)
    {
        $chat_users = array();

        // Get this user's friends.
        $friends = $this->get_friends($_SESSION['user_id']);

        if ($filter) {
            // Get the active friends.
            foreach ($friends as $friend) {
                if ($this->active($friend['friend_id'])) {
                    $friend['active'] = TRUE;
                    array_push($chat_users, $friend);
                }
            }
        }
        else {
            // TODO
            // Do some thing else like display the users she has previously
            // chatted with.
        }

        return $chat_users;
    }

    public function get_num_notifs($filter=TRUE)
    {
        if ($filter) {
			return count($this->get_notifications(0, 0, FALSE, TRUE));
        }

        return count($this->get_notifications(0, 0, FALSE, FALSE));
    }

    public function get_notifications($offset, $limit, $use_limit=TRUE, $filter=TRUE)
    {
        $notifications = array();

        // Get notifications from activities.
		if ($filter) {
			if ($use_limit) {
				$q = sprintf("SELECT activity_id, trigger_id, source_id, parent_id, source_type, activity, seen, date_entered " .
				             "FROM activities WHERE (parent_id=%d AND trigger_id != %d AND seen=%d) " .
				             "ORDER BY date_entered DESC LIMIT %d, %d",
							 $_SESSION['user_id'], $_SESSION['user_id'], 0, $offset, $limit);
			}
			else {
				$q = sprintf("SELECT trigger_id, source_id, parent_id, source_type, activity, date_entered " .
				             "FROM activities WHERE (parent_id=%d AND trigger_id != %d AND seen=%d) " .
				             "ORDER BY date_entered DESC",
							 $_SESSION['user_id'], $_SESSION['user_id'], 0);
			}
		}
		else {
			if ($use_limit) {
				$q = sprintf("SELECT activity_id, trigger_id, source_id, parent_id, source_type, activity, seen, date_entered " .
				             "FROM activities WHERE (parent_id=%d AND trigger_id != %d) " .
				             "ORDER BY date_entered DESC LIMIT %d, %d",
							 $_SESSION['user_id'], $_SESSION['user_id'], $offset, $limit);
			}
			else {
				$q = sprintf("SELECT trigger_id, source_id, parent_id, source_type, activity, date_entered " .
				             "FROM activities WHERE (parent_id=%d AND trigger_id != %d) " .
				             "ORDER BY date_entered DESC",
							 $_SESSION['user_id'], $_SESSION['user_id']);
			}
		}

        $query = $this->run_query($q);
        $results = $query->result_array();
        foreach ($results as $notif) {
            // Get the name of the user who performed this activity.
            $notif['user'] = $this->get_full_name($notif['trigger_id']);

			// If the notification is beeing displayed to the user, update to reflect that it has been seen.
			if (isset($notif['seen']) && ($notif['seen'] == 0)) {
                $q = sprintf("UPDATE activities SET seen=%d WHERE (activity_id=%d) LIMIT 1",
                             1, $notif['activity_id']);
                $query = $this->run_query($q);
            }

            // If it is a like, comment, or share on of a post,
            if ((($notif['activity'] === 'like') ||
                 ($notif['activity'] === 'comment') ||
                 ($notif['activity'] === 'share'))
                && ($notif['source_type'] === 'post')) {
                // Get the current post_id, helpful for displaying shared posts.
                $q = sprintf("SELECT post_id FROM posts WHERE (parent_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $notif['new_post_id'] = $query->row_array()['post_id'];

                // Get brief contents of the post.
                $q = sprintf("SELECT post FROM posts WHERE (post_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $post = $query->row()->post;

                // get short post.
                $short_post = $this->post_model->get_short_post($post, 75);
                $notif['post'] = $short_post['body'];
            }

            // If it is a like, or reply to a comment,
            if ((($notif['activity'] === 'like') ||
                 ($notif['activity'] === 'reply'))
                && ($notif['source_type'] === 'comment')) {
                $q = sprintf("SELECT comment FROM comments WHERE (comment_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $comment = $query->row()->comment;

                // get short comment.
                $short_comment = $this->post_model->get_short_post($comment, 25);
                $notif['comment'] = $short_comment['body'];
            }

            // If it is a like of a reply,
            if ($notif['activity'] === 'like' && $notif['source_type'] === 'reply') {
                $q = sprintf("SELECT comment FROM comments WHERE (comment_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $reply = $query->row()->comment;

                // get short reply.
                $short_reply = $this->post_model->get_short_post($reply, 25);
                $notif['reply'] = $short_reply['body'];
            }

            // Add the timespan.
            $notif['timespan'] = timespan(mysql_to_unix($notif['date_entered']), now(), 1);

			// Add it to the list of notifications.
            array_push($notifications, $notif);
        }

        return $notifications;
    }

    public function get_friendship_status($user_id)
    {
        // Check whether the two users are already friends.
        $q = sprintf("SELECT id FROM friends WHERE user_id=%d AND friend_id=%d " .
                     "OR user_id=%d AND friend_id=%d LIMIT 1",
                     $_SESSION['user_id'], $user_id, $user_id, $_SESSION['user_id']);
        $query = $this->run_query($q);
        $friends = FALSE;
        if ($query->num_rows() == 1) {
            $friends = TRUE;
        }

        if ($friends) {
            $data['friends'] = TRUE;
            $data['fr_sent'] = TRUE;
        }
        else {
            // Check to see if a friend request has already been sent.
            $q = sprintf("SELECT request_id, user_id, target_id FROM friend_requests " .
                         "WHERE (user_id=%d AND target_id=%d) " .
                         "OR (user_id=%d AND target_id=%d) LIMIT 1",
                         $_SESSION['user_id'], $user_id, $user_id, $_SESSION['user_id']);
            $query = $this->run_query($q);

            $data['friends'] = FALSE;
            $data['fr_sent'] = FALSE;
            if ($query->num_rows() == 1) {
                $data['friends'] = FALSE;
                $data['fr_sent'] = TRUE;
                $data['user_id'] = $query->row()->user_id;
                $data['target_id'] = $query->row()->target_id;
            }
        }

        return $data;
    }

    public function get_num_suggested_users()
    {
        return count($this->get_suggested_users(0, 0, FALSE));
    }

    public function get_suggested_users($offset, $limit, $use_limit=TRUE)
    {
        if ($use_limit) {
            $q = sprintf("SELECT user_id, fname, lname FROM users " .
                         "WHERE (user_id != %d) LIMIT %d, %d",
                         $_SESSION['user_id'], $offset, $limit);
        }
        else {
            $q = sprintf("SELECT user_id, fname, lname FROM users");
        }

        $query = $this->run_query($q);

        $results = $query->result_array();
        $users = array();
        foreach ($results as $user) {
            // Only add to list if they are not friends and friend request hasn't been sent.
            $fr_status = $this->get_friendship_status($user['user_id']);
            if (!$fr_status['friends'] && !$fr_status['fr_sent']) {
                $user['full_name'] = ucfirst(strtolower($user['lname'])) . ' ' . ucfirst(strtolower($user['fname']));
                array_push($users, $user);
            }
        }

        return $users;
    }

    public function get_num_friend_requests()
    {
        return count($this->get_friend_requests(TRUE));
    }

    public function get_friend_requests($filter=FALSE)
    {
        if ($filter) {
            $q = sprintf("SELECT user_id, seen FROM friend_requests " .
                         "WHERE (target_id=%d AND seen IS FALSE AND confirmed IS FALSE)",
                         $_SESSION['user_id']);
        }
        else {
            $q = sprintf("SELECT user_id, seen FROM friend_requests " .
                         "WHERE (target_id=%d AND confirmed IS FALSE) ORDER BY date_entered DESC",
                         $_SESSION['user_id']);
        }
        $query = $this->run_query($q);
        $results = $query->result_array();
        $friend_requests = array();
        foreach ($results as $fr) {
            if (!$filter && isset($fr['seen']) && $fr['seen']==0) {
                $q = sprintf("UPDATE friend_requests SET seen=TRUE " .
                             "WHERE (target_id=%d AND user_id=%d)",
                             $_SESSION['user_id'], $fr['user_id']);
                $query = $this->run_query($q);
            }

            $fr['name'] = $this->get_full_name($fr['user_id']);
            array_push($friend_requests, $fr);
        }

        return $friend_requests;
    }

    public function send_friend_request($target_id)
    {
        $q = sprintf("INSERT INTO friend_requests (user_id, target_id) VALUES (%d, %d)",
                     $_SESSION['user_id'], $target_id);
        $query = $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, source_type, activity, audience) " .
                     "VALUES (%d, %d, %d, %s, %s, %s)",
                     $_SESSION['user_id'], $target_id, $target_id, $this->db->escape('user'),
                     $this->db->escape('friend_request'), $this->db->escape('user'));
        $query = $this->run_query($q);
    }

    public function confirm_friend_request($friend_id)
    {
        // Add the user to the list of friends.
        $q = sprintf("INSERT INTO friends (user_id, friend_id) VALUES (%d, %d)",
                     $_SESSION['user_id'], $friend_id);
        $query = $this->run_query($q);

        // Update the friend_requests table.
        $q = sprintf("UPDATE friend_requests SET confirmed=%d WHERE user_id=%d AND target_id=%d",
                     1, $friend_id, $_SESSION['user_id']);
        $query = $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (trigger_id, parent_id, source_id, activity, audience) " .
                     "VALUES (%d, %d, %d, %s, %s)",
                     $_SESSION['user_id'], $friend_id, $friend_id,
                     $this->db->escape('confirmed_friend_request'), $this->db->escape('user'));
        $query = $this->run_query($q);
    }

    public function send_message($receiver_id, $message)
    {
        $q = sprintf("INSERT INTO messages (sender_id, receiver_id, message) VALUES (%d, %d, %s)",
                     $_SESSION['user_id'], $receiver_id, $this->db->escape($message));
        $query = $this->run_query($q);
    }

    public function get_num_conversation($user_id)
    {
        return count($this->get_conversation($user_id, 0, 0, FALSE));
    }

    public function get_conversation($user_id, $offset, $limit, $use_limit=TRUE)
    {
        // Get the messages sent to this user.
        if ($use_limit) {
            $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent FROM messages " .
                         "WHERE (receiver_id=%d AND sender_id=%d) OR (receiver_id=%d AND sender_id=%d) " .
                         "ORDER BY date_sent DESC LIMIT %d, %d",
                         $user_id, $_SESSION['user_id'], $_SESSION['user_id'], $user_id, $offset, $limit);
        }
        else {
            $q = sprintf("SELECT sender_id, receiver_id, message, date_sent FROM messages " .
                         "WHERE (receiver_id=%d AND sender_id=%d) OR (receiver_id=%d AND sender_id=%d) " .
                         "ORDER BY date_sent DESC",
                         $user_id, $_SESSION['user_id'], $_SESSION['user_id'], $user_id);
        }
        $query = $this->run_query($q);
        $results = $query->result_array();

        $messages = array();
        $sender = $this->get_full_name($_SESSION['user_id']);
        $receiver = $this->get_full_name($user_id);
        foreach ($results as $msg) {
            if ($msg['sender_id'] == $_SESSION['user_id']) {
                $msg['sender'] = $sender;
            }
            else if ($msg['sender_id'] == $user_id) {
                $msg['sender'] = $receiver;
            }

            if (isset($msg['seen']) && ($msg['seen'] == 0) && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $q = sprintf("UPDATE messages SET seen=%d WHERE (message_id=%d) LIMIT 1",
                             1, $msg['message_id']);
                $query = $this->run_query($q);
            }

            // Add the timespan.
            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
            array_push($messages, $msg);
        }

        return $messages;
    }

    public function add_college($college_id)
    {
        $q = sprintf("INSERT INTO user_profile (user_id, college_id) " .
                     "VALUES (%d, %d)",
                     $_SESSION['user_id'], $college_id);
        $query = $this->run_query($q);
    }
}
?>
