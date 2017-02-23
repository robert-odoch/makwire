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

        $friends = array();
        $results = $query->result_array();
        foreach ($results as &$f) {
            if ($f['friend_id'] == $_SESSION['user_id']) {
                $f['friend_id'] = $f['user_id'];
            }
            array_push($friends, $f['friend_id']);
        }
        unset($f);

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
            $photo_id = $query->row_array()['profile_pic_id'];
            // Get the full path of the profile picture.
            $q = sprintf("SELECT full_path FROM user_photos " .
                         "WHERE (user_id=%d AND photo_id=%d)",
                         $user_id, $photo_id);
            $query = $this->run_query($q);
            $profile_pic_path = $query->row_array()['full_path'];
            $profile_pic_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $profile_pic_path);
        }

        return $profile_pic_path;
    }

    public function are_friends($user_id)
    {
        if ($user_id == $_SESSION['user_id']) {
            return TRUE;
        }

        $friends = $this->get_friend_ids($_SESSION['user_id']);
        return in_array($user_id, $friends);
    }

    public function get_num_posts($user_id)
    {
        $q = sprintf("SELECT post_id FROM posts " .
                     "WHERE (audience='timeline') AND (author_id=%d)",
                     $user_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_posts($user_id, $offset, $limit)
    {
        // Query to get ID of the shared posts.
        $shared_posts_q = sprintf("SELECT subject_id, date_entered FROM shares WHERE (user_id = %d)",
                                 $user_id);
        $primary_posts_q = sprintf("SELECT post_id, date_entered FROM posts " .
                                    "WHERE (author_id=%d)",
                                    $user_id);
        $posts_q = sprintf("(%s) UNION (%s) ORDER BY date_entered DESC LIMIT %d, %d",
                            $primary_posts_q, $shared_posts_q, $offset, $limit);
        $query = $this->run_query($posts_q);
        $results = $query->result_array();

        $posts = array();
        foreach ($results as $r) {
            // Get the detailed post.
            $post = $this->post_model->get_post($r['post_id']);

            if (!$post) {
                // This post was shared by the user's friend but the user is
                // not a friend to the original author of the post.
                continue;
            }

            // Check whether it's a shared post.
            $post['shared'] = FALSE;
            if ($post['author_id'] != $user_id) {
                $post['shared'] = TRUE;
                $post['sharer_id'] = $user_id;
                $post['sharer'] = $this->user_model->get_name($user_id);
            }

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

        return $this->run_query($q)->num_rows();
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

        $messages = $query->result_array();
        foreach ($messages as &$msg) {
            $msg['sender'] = $this->get_name($msg['sender_id']);
            if (!$msg['seen'] && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $q = sprintf("UPDATE messages SET seen=1 WHERE (message_id=%d) LIMIT 1",
                             $msg['message_id']);
                $this->run_query($q);
            }

            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
        }
        unset($msg);

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
        return $this->run_query($q)->num_rows();
    }

    public function get_friends($user_id, $offset, $limit)
    {
        $q = sprintf("SELECT user_id, friend_id FROM friends " .
                     "WHERE (user_id=%d) OR (friend_id=%d) LIMIT %d, %d",
                     $user_id, $user_id, $offset, $limit);
        $query = $this->run_query($q);

        $friends = $query->result_array();
        foreach ($friends as &$f) {
            if ($f['friend_id'] == $user_id) {
                $f['friend_id'] = $f['user_id'];
            }

            // Get this friend's name.
            $f['display_name'] = $this->get_name($f['friend_id']);
            $f['profile_pic_path'] = $this->get_profile_picture($f['friend_id']);
        }
        unset($f);

        return $friends;
    }

    public function get_all_friends($user_id)
    {
        $q = sprintf("SELECT user_id, friend_id FROM friends " .
                     "WHERE (user_id=%d) OR (friend_id=%d)",
                     $user_id, $user_id);
        $query = $this->run_query($q);

        $friends = $query->result_array();
        foreach ($friends as &$f) {
            if ($f['friend_id'] == $user_id) {
                $f['friend_id'] = $f['user_id'];
            }

            // Get this friend's name.
            $f['display_name'] = $this->get_name($f['friend_id']);
        }
        unset($f);

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
        // Notifications that can/can't be combined together if the are performed on
        // the same object.
        $combined_notifs_array = ['like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'];
        $combined_notifs = "'like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'";
        $atomic_notifs = "'birthday', 'profile_pic_change', 'confirmed_friend_request', 'confirmed_join_group_request', 'added_photo'";

        // Get the ID's of all this user's friends.
        $friends = $this->get_friend_ids();

        if (count($friends) == 0) {
            $friends_string = '';
        }
        elseif (count($friends) == 1) {
            $friends_string = "{$friends[0]}";
        }
        else {
            $friends_string = '';
            for ($i = 0; $i < count($friends)-1; ++$i) {
                $friends_string .= "{$friends[$i]}, ";
            }
            $friends_string .= "{$friends[$i]}";
        }

        // WHERE clause for notifications having this user as a direct targert.
        $primary_notifs_clause = sprintf("subject_id=%d AND actor_id != %d",
                                         $_SESSION['user_id'], $_SESSION['user_id']);

        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_q = sprintf("SELECT date_read FROM notification_read " .
                                        "WHERE (user_id=%d) ORDER BY date_read DESC LIMIT 1",
                                        $_SESSION['user_id']);
            $last_read_date_query = $this->run_query($last_read_date_q);
            if ($last_read_date_query->num_rows() == 0) {
                // User has'nt read any notifications before, use his account creation date.
                $last_read_date_q = sprintf("SELECT date_created FROM users WHERE (user_id=%d)",
                                            $_SESSION['user_id']);
            }

            if (count($friends) > 0) {
                // Query to get activities that were performed by this user..
                $acted_on_q = sprintf("SELECT DISTINCT source_id FROM activities " .
                                        "WHERE (actor_id = %d AND subject_id != %d AND activity IN('comment', 'reply'))",
                                        $_SESSION['user_id'], $_SESSION['user_id']);

                // WHERE clause for notifications from other sources like profile_pic_change, comment and reply.
                // Only applies to users with friends.
                $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                                "((source_id IN(%s) AND activity IN('comment', 'reply')) OR activity = 'profile_pic_change')",
                                                $friends_string, $friends_string, $acted_on_q);

                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE (((%s) OR (%s)) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity AND date_entered > (%s))",
                                          $primary_notifs_clause, $other_notifs_clause, $combined_notifs, $last_read_date_q);

                // Query to get all notifications from activities.
        		$notifs_q = sprintf("SELECT activity_id, subject_id, activity FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND activity IN(%s) AND date_entered > (%s)))",
                    				$latest_notif_q, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs, $last_read_date_q);

                // Get Birthday notifications.
                $today = date("Y-m-d");
                $birthdays_q = sprintf("SELECT user_id FROM users WHERE user_id IN(%s) AND dob='%s'",
                                       $friends_string, $today);
            }
            else {
                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE ((%s) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity AND date_entered > (%s))",
                                          $primary_notifs_clause, $combined_notifs, $last_read_date_q);

                // Query to get all notifications from activities.
        		$notifs_q = sprintf("SELECT activity_id FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR ((%s) AND activity IN(%s) AND date_entered > (%s)))",
                    				$latest_notif_q, $primary_notifs_clause, $atomic_notifs, $last_read_date_q);
            }
        }
        else {
            if (count($friends) > 0) {
                // Query to get activities that were performed by this user..
                $acted_on_q = sprintf("SELECT DISTINCT source_id FROM activities " .
                                        "WHERE (actor_id = %d AND subject_id != %d AND activity IN('comment', 'reply'))",
                                        $_SESSION['user_id'], $_SESSION['user_id']);

                // WHERE clause for notifications from other sources like profile_pic_change, comment, reply and birthday.
                // Only applies to users with friends.
                $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                                "((source_id IN(%s) AND activity IN('comment', 'reply')) OR activity IN('profile_pic_change','birthday'))",
                                                $friends_string, $friends_string, $acted_on_q);

                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE (((%s) OR (%s)) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity)",
                                          $primary_notifs_clause, $other_notifs_clause, $combined_notifs);

                // Query to get all notifications from activities.
                $notifs_q = sprintf("SELECT activity_id, subject_id, activity FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND activity IN(%s)))",
                                    $latest_notif_q, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs);
            }
            else {
                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE ((%s) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity)",
                                          $primary_notifs_clause, $combined_notifs);

                // Query to get all notifications from activities.
                $notifs_q = sprintf("SELECT activity_id FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR ((%s) AND activity IN(%s)))",
                                    $latest_notif_q, $primary_notifs_clause, $atomic_notifs);
            }
        }

        $num_notifications = $this->run_query($notifs_q)->num_rows();

        if (isset($birthdays_q)) {
            $birthdays = $this->run_query($birthdays_q)->result_array();

            foreach ($birthdays as $bd) {
                // Check if this birthday has been inserted in activities table.
                $q = sprintf("SELECT activity_id FROM activities " .
                             "WHERE (actor_id=%d AND activity='birthday' AND YEAR(date_entered)=%d)",
                             $bd['user_id'], date('Y'));
                $query = $this->run_query($q);

                if ($query->num_rows() == 0) {
                    // It is a brand new birthday.
                    ++$num_notifications;
                }
                else {
                    // Not so new, but check whether this user has seen it.
                    $q = sprintf("SELECT user_id FROM notification_read " .
                                 "WHERE (user_id = %d AND activity_id = %d)",
                                 $_SESSION['user_id'], $query->row()->activity_id);
                    if ($this->run_query($q)->num_rows() == 0) {
                        // Hasn't seen.
                        ++$num_notifications;
                    }
                }
            }
        }

        return $num_notifications;
    }

    public function get_notifications($offset, $limit, $filter=TRUE)
    {
        // Notifications that can/can't be combined together if the are performed on
        // the same object.
        $combined_notifs_array = ['like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'];
        $combined_notifs = "'like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'";
        $atomic_notifs = "'birthday', 'profile_pic_change', 'confirmed_friend_request', 'confirmed_join_group_request', 'added_photo'";

        // Get the ID's of all this user's friends.
        $friends = $this->get_friend_ids();

        if (count($friends) == 0) {
            $friends_string = '';
        }
        elseif (count($friends) == 1) {
            $friends_string = "{$friends[0]}";
        }
        else {
            $friends_string = '';
            for ($i = 0; $i < count($friends)-1; ++$i) {
                $friends_string .= "{$friends[$i]}, ";
            }
            $friends_string .= "{$friends[$i]}";
        }

        // WHERE clause for notifications having this user as a direct targert.
        $primary_notifs_clause = sprintf("subject_id=%d AND actor_id != %d",
                                         $_SESSION['user_id'], $_SESSION['user_id']);

        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_q = sprintf("SELECT date_read FROM notification_read " .
                                        "WHERE (user_id=%d) ORDER BY date_read DESC LIMIT 1",
                                        $_SESSION['user_id']);
            $last_read_date_query = $this->run_query($last_read_date_q);
            if ($last_read_date_query->num_rows() == 0) {
                // User has'nt read any notifications before, use his account creation date.
                $last_read_date_q = sprintf("SELECT date_created FROM users WHERE (user_id=%d)",
                                            $_SESSION['user_id']);
            }

            if (count($friends) > 0) {
                // Query to get activities that were performed by this user..
                $acted_on_q = sprintf("SELECT DISTINCT source_id FROM activities " .
                                        "WHERE (actor_id = %d AND subject_id != %d AND activity IN('comment', 'reply'))",
                                        $_SESSION['user_id'], $_SESSION['user_id']);

                // WHERE clause for notifications from other sources like profile_pic_change, comment and reply.
                // Only applies to users with friends.
                $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                                "((source_id IN(%s) AND activity IN('comment', 'reply'))  OR activity = 'profile_pic_change')",
                                                $friends_string, $friends_string, $acted_on_q);

                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE (((%s) OR (%s)) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity AND date_entered > (%s))",
                                          $primary_notifs_clause, $other_notifs_clause, $combined_notifs, $last_read_date_q);

                // Query to get all notifications from activities.
        		$notifs_q = sprintf("SELECT * FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND activity IN(%s) AND date_entered > (%s))) " .
                                    "ORDER BY date_entered DESC LIMIT %d, %d",
                    				$latest_notif_q, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs, $last_read_date_q, $offset, $limit);

                // Get Birthday notifications.
                $today = date("Y-m-d");
                $birthdays_q = sprintf("SELECT user_id, dob, CONCAT(dob, ' 00:00:00') as date_entered FROM users " .
                                       "WHERE (user_id IN (%s) AND dob='%s')",
                                       $friends_string, $today);
            }
            else {
                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE ((%s) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity AND date_entered > (%s))",
                                          $primary_notifs_clause, $combined_notifs, $last_read_date_q);

                // Query to get all notifications from activities.
        		$notifs_q = sprintf("SELECT * FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR ((%s) AND activity IN(%s) AND date_entered > (%s))) " .
                                    "ORDER BY date_entered DESC LIMIT %d, %d",
                    				$latest_notif_q, $primary_notifs_clause, $atomic_notifs, $last_read_date_q, $offset, $limit);
            }
        }
        else {
            if (count($friends) > 0) {
                // Query to get activities that were performed by this user..
                $acted_on_q = sprintf("SELECT DISTINCT source_id FROM activities " .
                                        "WHERE (actor_id = %d AND subject_id != %d AND activity IN('comment', 'reply'))",
                                        $_SESSION['user_id'], $_SESSION['user_id']);

                // WHERE clause for notifications from other sources like profile_pic_change, comment, reply and birthday.
                // Only applies to users with friends.
                $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                                "((source_id IN(%s) AND activity IN('comment', 'reply')) OR activity IN('profile_pic_change','birthday'))",
                                                $friends_string, $friends_string, $acted_on_q);

                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE (((%s) OR (%s)) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity)",
                                          $primary_notifs_clause, $other_notifs_clause, $combined_notifs);

                // Query to get all notifications from activities.
                $notifs_q = sprintf("SELECT * FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND activity IN(%s))) " .
                                    "ORDER BY date_entered DESC LIMIT %d, %d",
                                    $latest_notif_q, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs, $offset, $limit);
            }
            else {
                // Query to get the latest notification from a group of activities
                // performed on the same object.
                $latest_notif_q = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                          "WHERE ((%s) AND activity IN(%s) AND a1.subject_id=a2.subject_id AND " .
                                          "a1.source_id=a2.source_id AND a1.source_type=a2.source_type AND " .
                                          "a1.activity=a2.activity)",
                                          $primary_notifs_clause, $combined_notifs);

                // Query to get all notifications from activities.
                $notifs_q = sprintf("SELECT * FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR ((%s) AND activity IN(%s))) " .
                                    "ORDER BY date_entered DESC LIMIT %d, %d",
                                    $latest_notif_q, $primary_notifs_clause, $atomic_notifs, $offset, $limit);
            }
        }

        $notifications = $this->run_query($notifs_q)->result_array();
        foreach ($notifications as &$n) {
            if (in_array($n['activity'], $combined_notifs_array)) {
                // Get the number of times an activity was performed on the same object.
                if ($filter) {
                    $num_actors_q = sprintf("SELECT DISTINCT actor_id FROM activities " .
                                            "WHERE (source_id = %d AND source_type = '%s' AND " .
                                            "activity = '%s' AND subject_id = %d AND " .
                                            "activity_id != %d AND actor_id NOT IN(%d, %d) AND " .
                                            "date_entered > (%s))",
                                            $n['source_id'], $n['source_type'], $n['activity'],
                                            $n['subject_id'], $n['activity_id'], $n['actor_id'],
                                            $_SESSION['user_id'], $last_read_date_q);
                }
                else {
                    $num_actors_q = sprintf("SELECT DISTINCT actor_id FROM activities " .
                                            "WHERE (source_id = %d AND source_type = '%s' AND " .
                                            "activity = '%s' AND subject_id = %d AND " .
                                            "activity_id != %d AND actor_id NOT IN(%d, %d))",
                                            $n['source_id'], $n['source_type'],
                                            $n['activity'], $n['subject_id'],
                                            $n['activity_id'], $n['actor_id'], $_SESSION['user_id']);
                }
                $n['num_actors'] = $this->run_query($num_actors_q)->num_rows();
            }
        }
        unset($n);

        if (isset($birthdays_q)) {
            $birthdays = $this->run_query($birthdays_q)->result_array();
            foreach ($birthdays as &$bd) {
                // Get the activity_id.
                $q = sprintf("SELECT activity_id FROM activities " .
                             "WHERE (actor_id=%d AND activity='birthday' AND YEAR(date_entered)=%d)",
                             $bd['user_id'], date('Y'));
                $query = $this->run_query($q);

                if ($query->num_rows() == 0) {
                    // It hasn't been recorded in activities table, add it.
                    $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                                 "VALUES (%d, %d, %d, 'user', 'birthday')",
                                 $bd['user_id'], $bd['user_id'], $bd['user_id']);
                    $this->run_query($q);

                    $bd['activity_id'] = $this->db->insert_id();
                    $bd['activity'] = "birthday";
                    $bd['subject_id'] = $bd['user_id'];
                    $bd['actor_id'] = $bd['user_id'];
                }
                else {
                    $activity_id = $query->row()->activity_id;

                    // Check whether this user has seen it before.
                    $q = sprintf("SELECT user_id FROM notification_read " .
                                 "WHERE (user_id=%d AND activity_id=%d)",
                                 $_SESSION['user_id'], $activity_id);
                    if ($this->run_query($q)->num_rows() == 0) {
                        // Hasn't seen.
                        $bd['activity_id'] = $activity_id;
                        $bd['activity'] = "birthday";
                        $bd['subject_id'] = $bd['user_id'];
                        $bd['actor_id'] = $bd['user_id'];
                    }
                }
            }
            unset($bd);

            $notifications = array_merge($notifications, $birthdays);
        }

        // Sort the results by date entered and get the first $limit.
        usort($notifications, $this->build_sorter('date_entered'));
        $notifications = array_slice($notifications, 0, $limit);

        if ($filter) {
            // Update notification_read to reflect that it has been seen.
            foreach ($notifications as &$r) {
                $q = sprintf("INSERT INTO notification_read (user_id, activity_id, date_read) " .
                            "VALUES (%d, %d, '%s')",
                            $_SESSION['user_id'], $r['activity_id'], $r['date_entered']);
                $this->run_query($q);
            }
            unset($r);
        }

        foreach ($notifications as &$notif) {
            // Get the name of the actor and subject.
            $notif['actor'] = $this->get_name($notif['actor_id']);
            $notif['subject'] = $this->get_name($notif['subject_id']);

            // If it is a like, comment, or share of a post,
            if (in_array($notif['activity'], array('like','comment','share')) &&
                ($notif['source_type'] === 'post')) {
                // Get brief contents of the post.
                $q = sprintf("SELECT post FROM posts WHERE (post_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $post = $query->row()->post;
                $short_post = $this->post_model->get_short_post($post, 75);

                $notif['post'] = $short_post['body'];
            }

            if (in_array($notif['activity'], array('comment','reply')) && $notif['subject_id'] != $_SESSION['user_id']) {
                // Get the gender of the subject.
                $gender_q = sprintf("SELECT gender FROM users WHERE user_id = %d LIMIT 1",
                                    $notif['subject_id']);
                $gender_query = $this->run_query($gender_q);
                $notif['subject_gender'] = ($gender_query->row_array()['gender'] == 'M')? 'his': 'her';
            }

            // If it is a like, or reply to a comment/reply,
            if (in_array($notif['activity'], array('like','reply')) &&
                in_array($notif['source_type'], array('comment','reply'))) {
                $q = sprintf("SELECT comment FROM comments WHERE (comment_id=%d) LIMIT 1",
                             $notif['source_id']);
                $query = $this->run_query($q);
                $comment = $query->row()->comment;

                // get short comment.
                $short_comment = $this->post_model->get_short_post($comment, 25);
                $notif['comment'] = $short_comment['body'];
            }

            // Add other actors.
            if (in_array($notif['activity'], $combined_notifs_array)) {
                if ($notif['num_actors'] > 0) {
                    $notif['others'] = " and {$notif['num_actors']}";
                    $notif['others'] .= ($notif['num_actors'] == 1)? " other ": " others";
                }
                else {
                    $notif['others'] = '';
                }
            }

            // Get date of birth for birthday notifications.
            if ($notif['activity'] == 'birthday') {
                $dob_q = sprintf("SELECT dob FROM users WHERE (user_id = %d) LIMIT 1",
                                 $notif['subject_id']);
                $dob_query = $this->run_query($dob_q);
                $notif['dob'] = $dob_query->row_array()['dob'];
            }

            // Add the timespan.
            $notif['timespan'] = timespan(mysql_to_unix($notif['date_entered']), now(), 1);
        }
        unset($notif);

        return $notifications;
    }

    public function get_friendship_status($user_id)
    {
        // Check whether the two users are already friends.
        $q = sprintf("SELECT id FROM friends " .
                     "WHERE (user_id=%d AND friend_id=%d) OR (user_id=%d AND friend_id=%d) LIMIT 1",
                     $_SESSION['user_id'], $user_id, $user_id, $_SESSION['user_id']);
        $query = $this->run_query($q);
        $data['are_friends'] = FALSE;
        if ($query->num_rows() == 1) {
            $data['are_friends'] = TRUE;
        }

        if ($data['are_friends']) {
            $data['fr_sent'] = TRUE;
        }
        else {
            // Check to see if a friend request has already been sent.
            $q = sprintf("SELECT request_id, user_id, target_id FROM friend_requests " .
                         "WHERE (user_id=%d AND target_id=%d) OR (user_id=%d AND target_id=%d) LIMIT 1",
                         $_SESSION['user_id'], $user_id, $user_id, $_SESSION['user_id']);
            $query = $this->run_query($q);

            $data['fr_sent'] = FALSE;
            if ($query->num_rows() == 1) {
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
            if (!$fr_status['are_friends'] && !$fr_status['fr_sent']) {
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

        return $this->run_query($q)->num_rows();
    }

    public function get_friend_requests()
    {
        $q = sprintf("SELECT user_id, seen FROM friend_requests " .
                     "WHERE (target_id=%d AND confirmed IS FALSE) ORDER BY date_entered DESC",
                     $_SESSION['user_id']);
        $query = $this->run_query($q);

        $friend_requests = $query->result_array();
        foreach ($friend_requests as &$fr) {
            if (!$fr['seen']) {
                $q = sprintf("UPDATE friend_requests SET seen=1 " .
                             "WHERE (target_id=%d AND user_id=%d)",
                             $_SESSION['user_id'], $fr['user_id']);
                $this->run_query($q);
            }

            $fr['name'] = $this->get_name($fr['user_id']);
        }
        unset($fr);

        return $friend_requests;
    }

    public function send_friend_request($target_id)
    {
        $friendship_status = $this->get_friendship_status($target_id);
        if ($friendship_status['are_friends'] || $friendship_status['fr_sent']) {
            return FALSE;
        }

        $q = sprintf("INSERT INTO friend_requests (user_id, target_id) VALUES (%d, %d)",
                     $_SESSION['user_id'], $target_id);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'user', 'friend_request')",
                     $_SESSION['user_id'], $target_id, $target_id);
        $this->run_query($q);

        return TRUE;
    }

    public function confirm_friend_request($friend_id)
    {
        // First check whether a friend request actually exist.
        $q = sprintf("SELECT request_id FROM friend_requests " .
                     "WHERE (target_id=%d AND user_id=%d)",
                     $_SESSION['user_id'], $friend_id);
        $query = $this->run_query($q);
        if ($query->num_rows() == 0) {
            return FALSE;
        }

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

        return TRUE;
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
        return $this->run_query($q)->num_rows();
    }

    public function get_conversation($user_id, $offset, $limit)
    {
        $q = sprintf("SELECT message_id, sender_id, receiver_id, message, seen, date_sent FROM messages " .
                     "WHERE (receiver_id=%d AND sender_id=%d) OR (receiver_id=%d AND sender_id=%d) " .
                     "ORDER BY date_sent DESC LIMIT %d, %d",
                     $user_id, $_SESSION['user_id'], $_SESSION['user_id'], $user_id, $offset, $limit);
        $query = $this->run_query($q);

        $messages = $query->result_array();
        $sender = $this->get_name($_SESSION['user_id']);
        $receiver = $this->get_name($user_id);
        foreach ($messages as &$msg) {
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
        }
        unset($msg);

        return $messages;
    }
}
?>
