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
    private function build_sorter($key)
    {
        return function ($a, $b) use ($key) {
            $interval = date_diff(date_create($a[$key]), date_create($b[$key]));
            $interval = $interval->format("%R%s");
            return $interval;
        };
    }

    private function get_friend_ids()
    {
        $q = sprintf("SELECT user_id, friend_id FROM friends " .
                     "WHERE (user_id=%d) OR (friend_id=%d)",
                     $_SESSION['user_id'], $_SESSION['user_id']);
        $query = $this->run_query($q);
        $friends = $query->result_array();

        for ($i = 0; $i != count($friends); ++$i) {
            if ($friends[$i]['friend_id'] == $_SESSION['user_id']) {
                $friends[$i]['friend_id'] = $friends[$i]['user_id'];
            }
            unset($friends[$i]['user_id']);
        }

        return $friends;
    }

    private function handle_error($error)
    {
        print($error);
        exit(1);
    }

    private function run_query($q)
    {
        $query = $this->db->query($q);
        if ( ! $query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function confirm_logged_in()
    {
        $q = sprintf("SELECT logged_in FROM users WHERE user_id=%d",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);

        if (!$query->row()->logged_in) {
            unset($_SESSION['user_id']);
            $_SESSION = array();
            $_SESSION['message'] = "This account was logged out from another location.<br>" .
                                   "Please log in again to continue using this account.<br>" .
                                   "We are sorry for bothering you.";
            redirect(base_url("login"));
        }
    }

    public function create_dummy_user($user)
    {
        $q = sprintf("INSERT INTO users (dob, fname, lname, email, gender, uname, passwd) " .
                     "VALUES (%s, %s, %s, %s, %s, %s, %s)", $this->db->escape($user['dob']),
                     $this->db->escape($user['firstname']), $this->db->escape($user['lastname']),
                     $this->db->escape($user['email']), $this->db->escape($user['gender']),
                     $this->db->escape($user['username']), $this->db->escape(password_hash($user['password'], PASSWORD_BCRYPT)));
        $this->run_query($q);
    }

    public function initialize_user()
    {
        $data['primary_user'] = $this->get_name($_SESSION['user_id']);
        $data['suggested_users'] = $this->get_suggested_users(0, 4);
        $data['num_friend_requests'] = $this->get_num_friend_requests(TRUE);
        $data['num_active_friends'] = $this->get_num_chat_users(TRUE);
        $data['num_new_messages'] = $this->get_num_messages(TRUE);
        $data['num_new_notifs'] = $this->get_num_notifs(TRUE);
        $data['chat_users'] = $this->get_chat_users(TRUE);
        $data['profile_pic_path'] = $this->get_profile_picture($_SESSION['user_id']);

        return $data;
    }

    public function get_name($user_id)
    {
        $q = sprintf("SELECT display_name FROM users WHERE user_id=%d",
                     $user_id);
        $query = $this->run_query($q);

        return ucfirst($query->row()->display_name);
    }

    public function get_profile_picture($user_id)
    {
        $q = sprintf("SELECT profile_pic_id FROM users " .
                     "WHERE (user_id=%d AND profile_pic_id IS NOT NULL)",
                     $user_id);
        $query = $this->run_query($q);
        if ($query->num_rows() == 0) {
            // Use a dummy picture.
            $profile_pic_path = base_url("images/missing_user.png");
        }
        else {
            $image_id = $query->row_array()['profile_pic_id'];
            // Get the full path of the profile picture.
            $q = sprintf("SELECT full_path FROM user_images " .
                         "WHERE (user_id=%d AND image_id=%d)",
                         $user_id, $image_id);
            $query = $this->run_query($q);
            $profile_pic_path = $query->row_array()['full_path'];
            $profile_pic_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $profile_pic_path);
        }

        return $profile_pic_path;
    }

    public function get_num_posts($user_id)
    {
        $q = sprintf("SELECT post_id FROM posts " .
                     "WHERE (audience='timeline') AND (author_id=%d)",
                     $user_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_posts($user_id, $offset, $limit)
    {
        $q = sprintf("SELECT post_id FROM posts " .
                     "WHERE (audience='timeline' AND author_id=%d) " .
                     "ORDER BY date_posted DESC LIMIT %d, %d",
                     $user_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $posts = array();
        foreach($results as $r) {
            // Get the detailed post.
            $post = $this->post_model->get_post($r['post_id']);

            // Get only 540 characters from post if possible.
            $short_post = $this->post_model->get_short_post($post['post'], 540);
            $post['post'] = $short_post['body'];
            $post['has_more'] = $short_post['has_more'];

            array_push($posts, $post);
        }

        return $posts;
    }

    public function get_num_messages($filter=TRUE)
    {
        if ($filter) {
            $q = sprintf("SELECT message_id FROM messages " .
                         "WHERE (seen IS FALSE AND receiver_id=%d)",
                         $_SESSION['user_id']);
        }
        else {
            $q = sprintf("SELECT message_id FROM messages WHERE receiver_id=%d",
                         $_SESSION['user_id']);
        }
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_messages($offset, $limit, $filter=TRUE)
    {
        if ($filter) {
            $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent " .
                         "FROM messages WHERE (seen IS FALSE AND receiver_id=%d) " .
                         "ORDER BY date_sent DESC LIMIT %d, %d",
                         $_SESSION['user_id'], $offset, $limit);
        }
        else {
            $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent " .
                         "FROM messages WHERE (receiver_id=%d) " .
                         "ORDER BY date_sent DESC LIMIT %d, %d",
                         $_SESSION['user_id'], $offset, $limit);
        }
        $query = $this->run_query($q);
        $results = $query->result_array();

        $messages = array();
        foreach ($results as $msg) {
            $msg['sender'] = $this->get_name($msg['sender_id']);
            if (!$msg['seen'] && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $q = sprintf("UPDATE messages SET seen=1 WHERE (message_id=%d) LIMIT 1",
                             $msg['message_id']);
                $this->run_query($q);
            }

            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);

            array_push($messages, $msg);
        }

        return $messages;
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

    public function get_num_friends($user_id)
    {
        $q = sprintf("SELECT user_id FROM friends " .
                     "WHERE (user_id=%d) OR (friend_id=%d)",
                     $user_id, $user_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_friends($user_id, $offset, $limit)
    {
        $q = sprintf("SELECT user_id, friend_id FROM friends " .
                     "WHERE (user_id=%d) OR (friend_id=%d) LIMIT %d, %d",
                     $user_id, $user_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $i = 0;
        foreach ($results as $r) {
            if ($r['friend_id'] == $user_id) {
                $results[$i]['friend_id'] = $r['user_id'];
            }
            unset($results[$i]['user_id']);
            ++$i;
        }

        $friends = array();
        foreach ($results as $friend) {
            // Get this friend's name.
            $friend['display_name'] = $this->get_name($friend['friend_id']);
            $friend['profile_pic_path'] = $this->get_profile_picture($friend['friend_id']);

            array_push($friends, $friend);
        }

        return $friends;
    }

    public function get_all_friends($user_id)
    {
        $q = sprintf("SELECT user_id, friend_id FROM friends " .
                     "WHERE (user_id=%d) OR (friend_id=%d)",
                     $user_id, $user_id);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $i = 0;
        foreach ($results as $r) {
            if ($r['friend_id'] == $user_id) {
                $results[$i]['friend_id'] = $r['user_id'];
            }
            unset($results[$i]['user_id']);
            ++$i;
        }

        $friends = array();
        foreach ($results as $friend) {
            // Get this friend's name.
            $friend['display_name'] = $this->get_name($friend['friend_id']);

            array_push($friends, $friend);
        }

        return $friends;
    }

    public function get_num_chat_users($filter=TRUE)
    {
        return count($this->get_chat_users(TRUE));
    }

    public function get_chat_users($filter=TRUE)
    {
        $chat_users = array();

        // Get this user's friends.
        $friends = $this->get_all_friends($_SESSION['user_id']);

        if ($filter) {
            // Get the active friends.
            foreach ($friends as $friend) {
                if ($this->active($friend['friend_id'])) {
                    $friend['active'] = TRUE;
                    $friend['profile_pic_path'] = $this->get_profile_picture($friend['friend_id']);
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
            // Query to get the last time the user read a notification.
            $last_read_date_query = sprintf("SELECT a.date_entered FROM activities as a " .
                                            "LEFT JOIN notification_read as nr ON (a.activity_id=nr.activity_id) " .
                                            "WHERE (nr.user_id=%d) ORDER BY nr.date_read DESC LIMIT 1",
                                            $_SESSION['user_id']);

            // Get notifications from activities having this user as a direct target.
            $primary_notifs_q = sprintf("SELECT activity_id FROM activities " .
                         "WHERE (subject_id=%d AND actor_id != %d AND date_entered > (%s))",
                         $_SESSION['user_id'], $_SESSION['user_id'], $last_read_date_query);

            // Get the ID's of all this user's friends.
            $friends = $this->get_friend_ids();
            $friends_string = '';
            for ($i = 0; $i != count($friends)-1; ++$i) {
                $friends_string .= "{$friends[$i]['friend_id']}, ";
            }
            $friends_string .= "{$friends[$i]['friend_id']}";

            // Get notifications from other sources like profile_pic_change, like, comment and reply.
            $other_notifs_q = sprintf("SELECT activity_id FROM activities " .
                         "WHERE (subject_id IN (%s) AND actor_id IN (%s) AND date_entered > (%s) " .
                         "AND (activity='like' OR activity='comment' OR activity='reply' OR activity='profile_pic_change' )) ",
    					 $friends_string, $friends_string, $last_read_date_query);

            // Get Birthday notifications.
            $today = date("Y-m-d");
            $birthdays_q = sprintf("SELECT user_id, dob, CONCAT(dob, ' 00:00:00') as date_entered FROM users " .
                         "WHERE (user_id IN (%s) AND dob='%s')",
                         $friends_string, $today);
        }
        else {
            // Get notifications from activities having this user as a direct target.
            $primary_notifs_q = sprintf("SELECT activity_id FROM activities " .
                         "WHERE (subject_id=%d AND actor_id != %d)",
                         $_SESSION['user_id'], $_SESSION['user_id']);

            // Get the ID's of all this user's friends.
            $friends = $this->get_friend_ids();
            $friends_string = '';
            for ($i = 0; $i != count($friends)-1; ++$i) {
                $friends_string .= "{$friends[$i]['friend_id']}, ";
            }
            $friends_string .= "{$friends[$i]['friend_id']}";

            // Get notifications from other sources like profile_pic_change, like, comment, reply and birthdays.
            $other_notifs_q = sprintf("SELECT activity_id FROM activities " .
                         "WHERE (subject_id IN (%s) AND actor_id IN (%s) " .
                         "AND (activity='like' OR activity='comment' OR activity='reply' OR activity='profile_pic_change' OR activity='birthday')) ",
    					 $friends_string, $friends_string);
        }

        $num_notifications = ($this->run_query($primary_notifs_q)->num_rows() + $this->run_query($other_notifs_q)->num_rows());
        if (isset($birthdays_q)) {
            $num_notifications += $this->run_query($birthdays_q)->num_rows();
        }

        return $num_notifications;
    }

    public function get_notifications($offset, $limit, $filter=TRUE)
    {
        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_query = sprintf("SELECT a.date_entered FROM activities as a " .
                                            "LEFT JOIN notification_read as nr ON (a.activity_id=nr.activity_id) " .
                                            "WHERE (nr.user_id=%d) ORDER BY nr.date_read DESC LIMIT 1",
                                            $_SESSION['user_id']);

            // Get notifications from activities having this user as a direct target.
    		$primary_notifs_q = sprintf("SELECT activity_id, actor_id, source_id, subject_id, source_type, activity, date_entered " .
                		                "FROM activities WHERE (subject_id=%d AND actor_id != %d AND date_entered > (%s)) " .
                		                "ORDER BY date_entered DESC LIMIT %d, %d",
                					    $_SESSION['user_id'], $_SESSION['user_id'], $last_read_date_query, $offset, $limit);

            // Get the ID's of all this user's friends.
            $friends = $this->get_friend_ids();

            $friends_string = '';
            for ($i = 0; $i != count($friends)-1; ++$i) {
                $friends_string .= "{$friends[$i]['friend_id']}, ";
            }
            $friends_string .= "{$friends[$i]['friend_id']}";

            // Get notifications from other sources like profile_pic_change, like, comment and reply.
    		$other_notifs_q = sprintf("SELECT activity_id, actor_id, source_id, subject_id, source_type, activity, date_entered " .
                		              "FROM activities WHERE (subject_id IN (%s) AND actor_id IN (%s) AND date_entered > (%s) " .
                                      "AND (activity='like' OR activity='comment' OR activity='reply' OR activity='profile_pic_change' )) " .
                		              "ORDER BY date_entered DESC LIMIT %d, %d",
                					  $friends_string, $friends_string, $last_read_date_query, $offset, $limit);

            // Get Birthday notifications.
            $today = date("Y-m-d");
            $birthdays_q = sprintf("SELECT user_id, dob, CONCAT(dob, ' 00:00:00') as date_entered FROM users " .
                                   "WHERE (user_id IN (%s) AND dob='%s')",
                                   $friends_string, $today);
        }
        else {
            // Get notifications from activities having this user as a direct target.
    		$primary_notifs_q = sprintf("SELECT activity_id, actor_id, source_id, subject_id, source_type, activity, date_entered " .
                		                "FROM activities WHERE (subject_id=%d AND actor_id != %d) " .
                		                "ORDER BY date_entered DESC LIMIT %d, %d",
                					    $_SESSION['user_id'], $_SESSION['user_id'], $offset, $limit);

            // Get the ID's of all this user's friends.
            $friends = $this->get_friend_ids();

            $friends_string = '';
            for ($i = 0; $i != count($friends)-1; ++$i) {
                $friends_string .= "{$friends[$i]['friend_id']}, ";
            }
            $friends_string .= "{$friends[$i]['friend_id']}";

            // Get notifications from other sources like profile_pic_change, like, comment, reply and birthday.
    		$other_notifs_q = sprintf("SELECT activity_id, actor_id, source_id, subject_id, source_type, activity, date_entered " .
                		              "FROM activities WHERE (subject_id IN (%s) AND actor_id IN (%s)) " .
                                      "AND (activity='like' OR activity='comment' OR activity='reply' OR activity='profile_pic_change' OR activity='birthday')) " .
                		              "ORDER BY date_entered DESC LIMIT %d, %d",
                					  $friends_string, $friends_string, $offset, $limit);
        }

        $results = $this->run_query($primary_notifs_q)->result_array();
        $results = array_merge($results, $this->run_query($other_notifs_q)->result_array());

        if (isset($birthdays_q)) {
            $birthdays = $this->run_query($birthdays_q)->result_array();
            for ($i = 0; $i != count($birthdays); ++$i) {
                // Get the activity_id.
                $q = sprintf("SELECT activity_id FROM activities " .
                             "WHERE (actor_id=%d AND activity='birthday' AND YEAR(date_entered)='%s')",
                             $birthdays[$i]['user_id'], date('Y'));
                $query = $this->run_query($q);

                if ($query->num_rows() == 0) {
                    // It hasn't been recorded in activities table, add it.
                    $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                                 "VALUES (%d, %d, %d, 'user', 'birthday')",
                                 $birthdays[$i]['user_id'], $birthdays[$i]['user_id'], $birthdays[$i]['user_id']);
                    $this->run_query($q);
                    $birthdays[$i]['activity_id'] = $this->db->insert_id();
                }
                else {
                    $birthdays[$i]['activity_id'] = $query->row_array()['activity_id'];
                }

                $birthdays[$i]['activity'] = "birthday";
                $birthdays[$i]['actor_id'] = $birthdays[$i]['user_id'];
            }

            $results = array_merge($results, $birthdays);
            usort($results, $this->build_sorter('date_entered'));
            $results = array_slice($results, 0, $limit);

            // Update notification_read to reflect that it has been seen.
            /*foreach ($results as $r) {
                $q = sprintf("INSERT INTO notification_read (user_id, activity_id) " .
                            "VALUES (%d, %d)",
                            $_SESSION['user_id'], $d['activity_id']);
                $this->run_query($q);
            }*/
        }
        else {
            usort($results, $this->build_sorter('date_entered'));
            $results = array_slice($results, 0, $limit);
        }

        $notifications = array();
        foreach ($results as $notif) {
            // Get the name of the actor.
            $notif['user'] = $this->get_name($notif['actor_id']);

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
                && (($notif['source_type'] === 'comment') ||
                    ($notif['source_type'] === 'reply'))) {
                $q = sprintf("SELECT comment FROM comments WHERE (comment_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $comment = $query->row()->comment;

                // get short comment.
                $short_comment = $this->post_model->get_short_post($comment, 25);
                $notif['comment'] = $short_comment['body'];
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
        $q = sprintf("SELECT id FROM friends " .
                     "WHERE (user_id=%d AND friend_id=%d) OR (user_id=%d AND friend_id=%d) LIMIT 1",
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
                         "WHERE (user_id=%d AND target_id=%d) OR (user_id=%d AND target_id=%d) LIMIT 1",
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
        return count($this->get_suggested_users(NULL, NULL, FALSE));
    }

    public function get_suggested_users($offset, $limit, $use_limit=TRUE)
    {
        if ($use_limit) {
            $q = sprintf("SELECT user_id, display_name FROM users " .
                         "WHERE (user_id != %d) LIMIT %d, %d",
                         $_SESSION['user_id'], $offset, $limit);
        }
        else {
            $q = sprintf("SELECT user_id, display_name FROM users " .
                         "WHERE (user_id != %d)",
                         $_SESSION['user_id']);
        }
        $query = $this->run_query($q);
        $results = $query->result_array();

        $users = array();
        foreach ($results as $user) {
            // Only add to list if they are not friends and friend request hasn't been sent.
            $fr_status = $this->get_friendship_status($user['user_id']);
            if (!$fr_status['friends'] && !$fr_status['fr_sent']) {
                $user['display_name'] = ucfirst($user['display_name']);
                $user['profile_pic_path'] = $this->get_profile_picture($user['user_id']);
                array_push($users, $user);
            }
        }

        return $users;
    }

    public function get_num_friend_requests($filter=TRUE)
    {
        if ($filter) {
            $q = sprintf("SELECT user_id FROM friend_requests " .
                         "WHERE (target_id=%d AND seen IS FALSE AND confirmed IS FALSE)",
                         $_SESSION['user_id']);
        }
        else {
            $q = sprintf("SELECT user_id FROM friend_requests " .
                         "WHERE (target_id=%d AND confirmed IS FALSE)",
                         $_SESSION['user_id']);
        }
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_friend_requests()
    {
        $q = sprintf("SELECT user_id, seen FROM friend_requests " .
                     "WHERE (target_id=%d AND confirmed IS FALSE) ORDER BY date_entered DESC",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $friend_requests = array();
        foreach ($results as $fr) {
            if (!$fr['seen']) {
                $q = sprintf("UPDATE friend_requests SET seen=1 " .
                             "WHERE (target_id=%d AND user_id=%d)",
                             $_SESSION['user_id'], $fr['user_id']);
                $this->run_query($q);
            }

            $fr['name'] = $this->get_name($fr['user_id']);
            array_push($friend_requests, $fr);
        }

        return $friend_requests;
    }

    public function send_friend_request($target_id)
    {
        $q = sprintf("INSERT INTO friend_requests (user_id, target_id) VALUES (%d, %d)",
                     $_SESSION['user_id'], $target_id);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'user', 'friend_request')",
                     $_SESSION['user_id'], $target_id, $target_id);
        $this->run_query($q);
    }

    public function confirm_friend_request($friend_id)
    {
        // Add the user to the list of friends.
        $q = sprintf("INSERT INTO friends (user_id, friend_id) VALUES (%d, %d)",
                     $_SESSION['user_id'], $friend_id);
        $this->run_query($q);

        // Update the friend_requests table.
        $q = sprintf("UPDATE friend_requests SET confirmed=1 " .
                     "WHERE (user_id=%d AND target_id=%d)",
                     $friend_id, $_SESSION['user_id']);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'user', 'confirmed_friend_request')",
                     $_SESSION['user_id'], $friend_id, $_SESSION['user_id']);
        $this->run_query($q);
    }

    public function send_message($receiver_id, $message)
    {
        $q = sprintf("INSERT INTO messages (sender_id, receiver_id, message) " .
                     "VALUES (%d, %d, %s)",
                     $_SESSION['user_id'], $receiver_id, $this->db->escape($message));
        $this->run_query($q);
    }

    public function get_num_conversation($user_id)
    {
        $q = sprintf("SELECT message_id FROM messages " .
                     "WHERE (receiver_id=%d AND sender_id=%d) OR (receiver_id=%d AND sender_id=%d)",
                     $user_id, $_SESSION['user_id'], $_SESSION['user_id'], $user_id);
        $query = $this->run_query($q);

        return $query->num_rows();
    }

    public function get_conversation($user_id, $offset, $limit)
    {
        $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent FROM messages " .
                     "WHERE (receiver_id=%d AND sender_id=%d) OR (receiver_id=%d AND sender_id=%d) " .
                     "ORDER BY date_sent DESC LIMIT %d, %d",
                     $user_id, $_SESSION['user_id'], $_SESSION['user_id'], $user_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $messages = array();
        $sender = $this->get_name($_SESSION['user_id']);
        $receiver = $this->get_name($user_id);
        foreach ($results as $msg) {
            if ($msg['sender_id'] == $_SESSION['user_id']) {
                $msg['sender'] = $sender;
            }
            else if ($msg['sender_id'] == $user_id) {
                $msg['sender'] = $receiver;
            }

            if (($msg['seen'] == 0) && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $q = sprintf("UPDATE messages SET seen=1 WHERE (message_id=%d) LIMIT 1",
                             $msg['message_id']);
                $this->run_query($q);
            }

            // Add the timespan.
            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
            array_push($messages, $msg);
        }

        return $messages;
    }
}
?>
