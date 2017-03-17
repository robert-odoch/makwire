<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/UserNotFoundException.php');

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'post_model',
            'birthday_message_model'
        ]);
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

    private function get_friends_ids()
    {
        $friends_sql = sprintf("SELECT user_id, friend_id " .
                                "FROM friends " .
                                "WHERE (user_id = %d) OR (friend_id = %d)",
                                $_SESSION['user_id'], $_SESSION['user_id']);
        $friends_query = $this->utility_model->run_query($friends_sql);

        $friends = array();
        $results = $friends_query->result_array();
        foreach ($results as &$f) {
            if ($f['friend_id'] == $_SESSION['user_id']) {
                $f['friend_id'] = $f['user_id'];
            }
            array_push($friends, $f['friend_id']);
        }
        unset($f);

        return $friends;
    }

    /**
     * Checks whether a user reached the specified age when he/she was
     * already registered and the user has reached that age
     * i.e., age <= user's age.
     */
    public function can_view_birthday($user_id, $age)
    {
        $sql = sprintf("SELECT YEAR(dob) AS year, MONTH(dob) AS month, " .
                        "DAY(dob) AS day, date_created " .
                        "FROM users WHERE (user_id = %d)", $user_id);
        $query = $this->utility_model->run_query($sql);
        if ($query->num_rows() == 0) {
            return FALSE;
        }

        $result = $query->row_array();

        return (
            (date_create(($result['year']+$age) . "-{$result['month']}-{$result['day']}") >
             date_create($result['date_created'])) &&
            ($age <= (date('Y') - $result['year']))
        );
    }

    public function get_dob($user_id) {
        $sql = sprintf("SELECT dob FROM users WHERE user_id = %d", $user_id);
        return $this->utility_model->run_query($sql)->row_array()['dob'];
    }
    /*** End Utility ***/

    public function confirm_logged_in()
    {
        $logged_in_sql = sprintf("SELECT logged_in FROM users WHERE user_id = %d",
                                $_SESSION['user_id']);
        $logged_in_query = $this->utility_model->run_query($logged_in_sql);

        if (!$logged_in_query->row_array()['logged_in']) {
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
        $this->utility_model->run_query($q);
    }

    public function initialize_user()
    {
        $data['profile_pic_path'] = $this->get_profile_pic_path($_SESSION['user_id']);
        $data['primary_user'] = $this->get_profile_name($_SESSION['user_id']);
        $data['people_you_may_know'] = $this->get_suggested_users(0, 4);
        $data['num_friend_requests'] = $this->get_num_friend_requests(TRUE);
        $data['num_active_friends'] = $this->get_num_chat_users(TRUE);
        $data['num_new_messages'] = $this->get_num_messages(TRUE);
        $data['num_new_notifs'] = $this->get_num_notifs(TRUE);
        $data['chat_users'] = $this->get_chat_users(TRUE);

        return $data;
    }

    public function get_profile_name($user_id)
    {
        $name_sql = sprintf("SELECT profile_name FROM users WHERE user_id = %d",
                            $user_id);
        $query = $this->utility_model->run_query($name_sql);
        if ($query->num_rows() == 0) {
            throw new UserNotFoundException();
        }

        return ucfirst($query->row_array()['profile_name']);
    }

    public function get_profile_pic_path($user_id)
    {
        $path_sql = sprintf("SELECT profile_pic_path " .
                            "FROM users " .
                            "WHERE (user_id = %d AND profile_pic_path IS NOT NULL)",
                            $user_id);
        $path_query = $this->utility_model->run_query($path_sql);

        if ($path_query->num_rows() == 0) {
            // No profile pic set, use a dummy picture.
            $profile_pic_path = base_url("images/missing_user.png");
        }
        else {
            $profile_pic_path = $path_query->row_array()['profile_pic_path'];
            $profile_pic_path =  str_replace("{$_SERVER['DOCUMENT_ROOT']}makwire/", '', $profile_pic_path);
            $profile_pic_path = base_url($profile_pic_path);
        }

        return $profile_pic_path;
    }

    public function are_friends($user_id)
    {
        if ($user_id == $_SESSION['user_id']) {
            return TRUE;
        }

        $friends_ids = $this->get_friends_ids();
        return in_array($user_id, $friends_ids);
    }

    public function get_num_timeline_posts_and_photos($user_id)
    {
        // Get number of posts and photos posted by this user.
        // and posts and photos shared by this user that are by friends
        // to the user viewing the page.
        $num_posts_and_photos_sql = sprintf("SELECT COUNT(activity_id) " .
                                            "FROM activities " .
                                            "WHERE ((actor_id = %d AND " .
                                                     "activity IN('post','photo','profile_pic_change')) OR " .
                                                     "(actor_id = %d AND activity = 'share'))",
                                            $user_id, $user_id);

        return $this->utility_model->run_query($num_posts_and_photos_sql)->row_array()['COUNT(activity_id)'];
    }

    public function get_timeline_posts_and_photos($user_id, $offset, $limit)
    {
        // Get posts and photos posted/shared by this user.
        $posts_and_photos_sql = sprintf("SELECT * FROM activities " .
                                        "WHERE ((actor_id = %d AND " .
                                                "activity IN('post','photo','profile_pic_change')) OR " .
                                                "(actor_id = %d AND activity = 'share')) " .
                                        "ORDER BY date_entered DESC LIMIT %d, %d",
                                        $user_id, $user_id, $offset, $limit);
        $posts_and_photos = $this->utility_model->run_query($posts_and_photos_sql)->result_array();

        foreach ($posts_and_photos as &$r) {
            switch ($r['source_type']) {
                case 'post':
                    $r['post'] = $this->post_model->get_post($r['source_id']);

                    // Get only 540 characters from post if possible.
                    $post_url = base_url("user/post/{$r['post']['post_id']}");
                    $r['post']['post'] = character_limiter($r['post']['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");

                    // Is it a shared post.
                    $r['post']['shared'] = FALSE;
                    if ($r['activity'] == 'share') {
                        $r['post']['shared'] = TRUE;
                        $r['post']['sharer_id'] = $user_id;
                        $r['post']['sharer'] = $this->user_model->get_profile_name($user_id);

                        // Change timespan to match the date it was shared on.
                        $r['post']['timespan'] = timespan(mysql_to_unix($r['date_entered']), now(), 1);

                        // Replace author's profile_pic with the one for sharer.
                        $r['post']['profile_pic_path'] = $this->get_profile_pic_path($user_id);
                    }
                    break;
                case 'photo':
                    $r['photo'] = $this->photo_model->get_photo($r['source_id']);

                    // Is it a shared photo?.
                    $r['photo']['shared'] = FALSE;
                    if($r['activity'] == 'share') {
                        $r['photo']['shared'] = TRUE;
                        $r['photo']['sharer_id'] = $user_id;
                        $r['photo']['sharer'] = $this->user_model->get_profile_name($user_id);

                        // Change timespan to match the date it was shared on.
                        $r['photo']['timespan'] = timespan(mysql_to_unix($r['date_entered']), now(), 1);

                        // Replace author's profile_pic with the one for sharer.
                        $r['photo']['profile_pic_path'] = $this->get_profile_pic_path($user_id);
                    }
                    break;
                default:
                    # do nothing.
                    break;
            }
        }
        unset($r);

        return $posts_and_photos;
    }

    public function get_num_birthday_messages($user_id) {
        $sql = sprintf("SELECT COUNT(id) FROM birthday_messages " .
                        "WHERE (user_id = %d)",
                        $user_id);
        return $this->utility_model->run_query($sql)->row_array()['COUNT(id)'];
    }

    public function get_birthday_messages($user_id, $age, $offset, $limit)
    {
        $sql = sprintf("SELECT id " .
                        "FROM birthday_messages WHERE (user_id = %d AND age = %d) " .
                        "LIMIT %d, %d",
                        $user_id, $age, $offset, $limit);
        $query = $this->utility_model->run_query($sql);
        $messages = $query->result_array();

        foreach ($messages as &$m) {
            $m = $this->birthday_message_model->get_message($m['id']);
        }
        unset($m);

        return $messages;
    }

    public function send_birthday_message($message, $user_id, $age)
    {
        // Record the message.
        $sql = sprintf("INSERT INTO birthday_messages " .
                        "(user_id, sender_id, message, age) " .
                        "VALUES (%d, %d, %s, %d)",
                        $user_id, $_SESSION['user_id'],
                        $this->db->escape($message), $age);
        $this->utility_model->run_query($sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'user', 'message')",
                                $_SESSION['user_id'], $user_id, $user_id);
        $this->utility_model->run_query($activity_sql);
    }

    public function get_num_messages($filter=TRUE)
    {
        if ($filter) {
            $sql = sprintf("SELECT COUNT(message_id) " .
                            "FROM messages " .
                            "WHERE (receiver_id = %d AND seen IS FALSE)",
                            $_SESSION['user_id']);
        }
        else {
            $sql = sprintf("SELECT COUNT(message_id) " .
                            "FROM messages " .
                            "WHERE receiver_id = %d",
                            $_SESSION['user_id']);
        }

        return $this->utility_model->run_query($sql)->row_array()['COUNT(message_id)'];
    }

    public function get_messages($offset, $limit, $filter=TRUE)
    {
        if ($filter) {
            $sql = sprintf("SELECT * " .
                            "FROM messages WHERE (receiver_id = %d AND seen IS FALSE) " .
                            "ORDER BY date_sent DESC LIMIT %d, %d",
                            $_SESSION['user_id'], $offset, $limit);
        }
        else {
            $sql = sprintf("SELECT * " .
                            "FROM messages WHERE (receiver_id = %d) " .
                            "ORDER BY date_sent DESC LIMIT %d, %d",
                            $_SESSION['user_id'], $offset, $limit);
        }
        $query = $this->utility_model->run_query($sql);

        $messages = $query->result_array();
        foreach ($messages as &$msg) {
            $msg['sender'] = $this->get_profile_name($msg['sender_id']);
            if (!$msg['seen'] && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $update_sql = sprintf("UPDATE messages " .
                                        "SET seen = 1 WHERE (message_id = %d) " .
                                        "LIMIT 1",
                                        $msg['message_id']);
                $this->utility_model->run_query($update_sql);
            }

            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
        }
        unset($msg);

        return $messages;
    }

    public function active($user_id)
    {
        $logged_in_sql = sprintf("SELECT logged_in FROM users WHERE user_id = %d LIMIT 1",
                                    $user_id);
        $query = $this->utility_model->run_query($logged_in_sql);

        return $query->row_array()['logged_in'];
    }

    public function get_num_friends($user_id)
    {
        $sql = sprintf("SELECT COUNT(user_id) FROM friends " .
                        "WHERE (user_id = %d) OR (friend_id = %d)",
                        $user_id, $user_id);
        return $this->utility_model->run_query($sql)->row_array()['COUNT(user_id)'];
    }

    public function get_friends($user_id, $offset, $limit)
    {
        $sql = sprintf("SELECT user_id, friend_id FROM friends " .
                        "WHERE (user_id = %d) OR (friend_id = %d) " .
                        "LIMIT %d, %d",
                        $user_id, $user_id, $offset, $limit);
        $query = $this->utility_model->run_query($sql);

        $friends = $query->result_array();
        foreach ($friends as &$f) {
            if ($f['friend_id'] == $user_id) {
                $f['friend_id'] = $f['user_id'];
            }

            // Get this friend's name.
            $f['profile_name'] = $this->get_profile_name($f['friend_id']);
            $f['profile_pic_path'] = $this->get_profile_pic_path($f['friend_id']);
        }
        unset($f);

        return $friends;
    }

    public function get_all_friends($user_id)
    {
        $sql = sprintf("SELECT user_id, friend_id FROM friends " .
                        "WHERE (user_id = %d) OR (friend_id = %d)",
                        $user_id, $user_id);
        $query = $this->utility_model->run_query($sql);

        $friends = $query->result_array();
        foreach ($friends as &$f) {
            if ($f['friend_id'] == $user_id) {
                $f['friend_id'] = $f['user_id'];
            }

            // Get this friend's name.
            $f['profile_name'] = $this->get_profile_name($f['friend_id']);
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
                    $friend['profile_pic_path'] = $this->get_profile_pic_path($friend['friend_id']);
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
        $combined_notifs_array = ['message', 'like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'];
        $combined_notifs = "'message', 'like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'";
        $atomic_notifs = "'birthday', 'profile_pic_change', 'confirmed_friend_request', 'confirmed_join_group_request', 'added_photo'";

        // Get the ID's of all this user's friends.
        $friends_ids = $this->get_friends_ids();
        // Add an extra lement for safety.
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        // WHERE clause for notifications having this user as a direct targert.
        $primary_notifs_clause = sprintf("subject_id = %d AND actor_id != %d",
                                         $_SESSION['user_id'], $_SESSION['user_id']);

        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_sql = sprintf("SELECT date_read FROM notification_read " .
                                            "WHERE (user_id = %d) ORDER BY date_read DESC " .
                                            "LIMIT 1",
                                            $_SESSION['user_id']);
            $last_read_date_query = $this->utility_model->run_query($last_read_date_sql);
            if ($last_read_date_query->num_rows() == 0) {

                // User has'nt read any notifications before,
                // use his account creation date.
                $last_read_date_sql = sprintf("SELECT date_created FROM users " .
                                                "WHERE (user_id = %d)",
                                                $_SESSION['user_id']);
            }

            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities " .
                                    "WHERE (actor_id = %d AND subject_id != %d AND " .
                                            "activity IN('comment', 'reply'))",
                                    $_SESSION['user_id'], $_SESSION['user_id']);

            // WHERE clause for notifications from other sources like profile_pic_change, comment and reply.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                            "((source_id IN(%s) AND activity IN('comment', 'reply')) OR " .
                                            "activity = 'profile_pic_change')",
                                            $friends_ids, $friends_ids, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                      "WHERE (((%s) OR (%s)) AND activity IN(%s) " .
                                                "AND a1.subject_id = a2.subject_id AND " .
                                                "a1.source_id = a2.source_id AND " .
                                                "a1.source_type = a2.source_type AND " .
                                                "a1.activity = a2.activity AND date_entered > (%s))",
                                      $primary_notifs_clause, $other_notifs_clause, $combined_notifs,
                                      $last_read_date_sql);

            // Query to get all notifications from activities.
    		$notifs_sql = sprintf("SELECT activity_id, subject_id, activity FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND " .
                                    "activity IN(%s) AND date_entered > (%s)))",
                				    $latest_notif_sql, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs, $last_read_date_sql);

            // Get Birthday notifications.
            $today = date("Y-m-d");
            $birthdays_sql = sprintf("SELECT user_id FROM users " .
                                        "WHERE user_id IN(%s) AND dob = '%s'",
                                        $friends_ids, $today);
        }
        else {
            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities " .
                                    "WHERE (actor_id = %d AND subject_id != %d AND " .
                                    "activity IN('comment', 'reply'))",
                                    $_SESSION['user_id'], $_SESSION['user_id']);

            // WHERE clause for notifications from other sources like profile_pic_change, comment, reply and birthday.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                            "((source_id IN(%s) AND activity IN('comment', 'reply')) OR " .
                                            "activity IN('profile_pic_change','birthday'))",
                                            $friends_ids, $friends_ids, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                        "WHERE (((%s) OR (%s)) AND activity IN(%s) " .
                                        "AND a1.subject_id = a2.subject_id AND " .
                                        "a1.source_id = a2.source_id AND " .
                                        "a1.source_type = a2.source_type AND " .
                                        "a1.activity = a2.activity)",
                                        $primary_notifs_clause, $other_notifs_clause,
                                        $combined_notifs);

            // Query to get all notifications from activities.
            $notifs_sql = sprintf("SELECT activity_id, subject_id, activity FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND activity IN(%s)))",
                                    $latest_notif_sql, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs);
        }

        $num_notifications = $this->utility_model->run_query($notifs_sql)->num_rows();

        if (isset($birthdays_sql)) {
            $birthdays = $this->utility_model->utility_model->run_query($birthdays_sql)->result_array();

            foreach ($birthdays as $bd) {
                // Check if this birthday has been inserted in activities table.
                $sql = sprintf("SELECT activity_id FROM activities " .
                                "WHERE (actor_id = %d AND activity = 'birthday' AND " .
                                "YEAR(date_entered) = %d)",
                                $bd['user_id'], date('Y'));
                $query = $this->utility_model->run_query($sql);

                if ($query->num_rows() == 0) {
                    // It is a brand new birthday.
                    ++$num_notifications;
                }
                else {
                    // Not so new, but check whether this user has seen it.
                    $seen_sql = sprintf("SELECT user_id FROM notification_read " .
                                        "WHERE (user_id = %d AND activity_id = %d)",
                                        $_SESSION['user_id'], $query->row_array()['activity_id']);
                    if ($this->utility_model->run_query($seen_sql)->num_rows() == 0) {
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
        $combined_notifs_array = ['message', 'like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'];
        $combined_notifs = "'message', 'like', 'comment', 'reply', 'share', 'friend_request', 'join_group_request'";
        $atomic_notifs = "'birthday', 'profile_pic_change', 'confirmed_friend_request', 'confirmed_join_group_request', 'added_photo'";

        // Get the ID's of all this user's friends.
        $friends_ids = $this->get_friends_ids();
        // Add an extra element for safety.
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        // WHERE clause for notifications having this user as a direct targert.
        $primary_notifs_clause = sprintf("subject_id = %d AND actor_id != %d",
                                         $_SESSION['user_id'], $_SESSION['user_id']);

        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_sql = sprintf("SELECT date_read FROM notification_read " .
                                            "WHERE (user_id = %d) ORDER BY date_read DESC " .
                                            "LIMIT 1",
                                            $_SESSION['user_id']);
            $last_read_date_query = $this->utility_model->run_query($last_read_date_sql);
            if ($last_read_date_query->num_rows() == 0) {
                // User has'nt read any notifications before, use his account creation date.
                $last_read_date_sql = sprintf("SELECT date_created FROM users " .
                                                "WHERE (user_id = %d)",
                                                $_SESSION['user_id']);
            }

            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities " .
                                    "WHERE (actor_id = %d AND subject_id != %d AND " .
                                            "activity IN('comment', 'reply'))",
                                    $_SESSION['user_id'], $_SESSION['user_id']);

            // WHERE clause for notifications from other sources like profile_pic_change, comment and reply.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                            "((source_id IN(%s) AND activity IN('comment', 'reply'))  OR " .
                                            "activity = 'profile_pic_change')",
                                            $friends_ids, $friends_ids, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                        "WHERE (((%s) OR (%s)) AND activity IN(%s) " .
                                        "AND a1.subject_id = a2.subject_id AND " .
                                        "a1.source_id = a2.source_id AND " .
                                        "a1.source_type = a2.source_type AND " .
                                        "a1.activity = a2.activity AND date_entered > (%s))",
                                      $primary_notifs_clause, $other_notifs_clause,
                                      $combined_notifs, $last_read_date_sql);

            // Query to get all notifications from activities.
    		$notifs_sql = sprintf("SELECT * FROM activities a1 " .
                                    "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND " .
                                    "activity IN(%s) AND date_entered > (%s))) " .
                                    "ORDER BY date_entered DESC LIMIT %d, %d",
                				    $latest_notif_sql, $primary_notifs_clause,
                                    $other_notifs_clause, $atomic_notifs, $last_read_date_sql,
                                    $offset, $limit);

            // Get Birthday notifications.
            $today = date("Y-m-d");
            $birthdays_sql = sprintf("SELECT user_id, dob, CONCAT(dob, ' 00:00:00') as date_entered " .
                                        "FROM users " .
                                        "WHERE (user_id IN (%s) AND dob = '%s')",
                                        $friends_ids, $today);
        }
        else {
            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities " .
                                    "WHERE (actor_id = %d AND subject_id != %d AND " .
                                            "activity IN('comment', 'reply'))",
                                    $_SESSION['user_id'], $_SESSION['user_id']);

            // WHERE clause for notifications from other sources
            // like profile_pic_change, comment, reply and birthday.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND " .
                                            "((source_id IN(%s) AND activity IN('comment', 'reply')) OR " .
                                            "activity IN('profile_pic_change','birthday'))",
                                            $friends_ids, $friends_ids, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2 " .
                                      "WHERE (((%s) OR (%s)) AND activity IN(%s) " .
                                      "AND a1.subject_id = a2.subject_id AND " .
                                      "a1.source_id = a2.source_id AND " .
                                      "a1.source_type = a2.source_type AND " .
                                      "a1.activity = a2.activity)",
                                      $primary_notifs_clause, $other_notifs_clause,
                                      $combined_notifs);

            // Query to get all notifications from activities.
            $notifs_sql = sprintf("SELECT * FROM activities a1 " .
                                "WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND " .
                                "activity IN(%s))) ORDER BY date_entered DESC " .
                                "LIMIT %d, %d",
                                $latest_notif_sql, $primary_notifs_clause,
                                $other_notifs_clause, $atomic_notifs,
                                $offset, $limit);
        }

        $notifications = $this->utility_model->run_query($notifs_sql)->result_array();
        foreach ($notifications as &$n) {
            if (in_array($n['activity'], $combined_notifs_array)) {
                // Get the number of times an activity was performed on the same object.
                if ($filter) {
                    $num_actors_sql = sprintf("SELECT DISTINCT actor_id FROM activities " .
                                                "WHERE (source_id = %d AND source_type = '%s' AND " .
                                                "activity = '%s' AND subject_id = %d AND " .
                                                "activity_id != %d AND actor_id NOT IN(%d, %d) AND " .
                                                "date_entered > (%s))",
                                                $n['source_id'], $n['source_type'], $n['activity'],
                                                $n['subject_id'], $n['activity_id'], $n['actor_id'],
                                                $_SESSION['user_id'], $last_read_date_sql);
                }
                else {
                    $num_actors_sql = sprintf("SELECT DISTINCT actor_id FROM activities " .
                                                "WHERE (source_id = %d AND source_type = '%s' AND " .
                                                "activity = '%s' AND subject_id = %d AND " .
                                                "activity_id != %d AND actor_id NOT IN(%d, %d))",
                                                $n['source_id'], $n['source_type'],
                                                $n['activity'], $n['subject_id'],
                                                $n['activity_id'], $n['actor_id'],
                                                $_SESSION['user_id']);
                }
                $n['num_actors'] = $this->utility_model->run_query($num_actors_sql)->num_rows();
            }
        }
        unset($n);

        if (isset($birthdays_sql)) {
            $birthdays = $this->utility_model->run_query($birthdays_sql)->result_array();
            foreach ($birthdays as &$bd) {
                // Get the activity_id.
                $sql = sprintf("SELECT activity_id FROM activities " .
                                "WHERE (actor_id = %d AND activity = 'birthday' AND " .
                                "YEAR(date_entered) = %d)",
                                $bd['user_id'], date('Y'));
                $query = $this->utility_model->run_query($sql);

                if ($query->num_rows() == 0) {
                    // It hasn't been recorded in activities table, add it.
                    $activity_sql = sprintf("INSERT INTO activities " .
                                            "(actor_id, subject_id, source_id, source_type, activity) " .
                                            "VALUES (%d, %d, %d, 'user', 'birthday')",
                                            $bd['user_id'], $bd['user_id'], $bd['user_id']);
                    $this->utility_model->run_query($activity_sql);

                    $bd['activity_id'] = $this->db->insert_id();
                    $bd['activity'] = "birthday";
                    $bd['subject_id'] = $bd['user_id'];
                    $bd['actor_id'] = $bd['user_id'];
                    $bd['date_entered'] = date_format(now(), 'Y-m-d H:i:s');
                }
                else {
                    $activity_id = $query->row()->activity_id;

                    // Check whether this user has seen it before.
                    $seen_sql = sprintf("SELECT user_id FROM notification_read " .
                                        "WHERE (user_id = %d AND activity_id = %d)",
                                        $_SESSION['user_id'], $activity_id);
                    if ($this->utility_model->run_query($seen_sql)->num_rows() == 0) {

                        // Hasn't seen.
                        $bd['activity_id'] = $activity_id;
                        $bd['activity'] = "birthday";
                        $bd['subject_id'] = $bd['user_id'];
                        $bd['actor_id'] = $bd['user_id'];
                        $bd['date_entered'] = $query->row_array()['date_entered'];
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
                $sql = sprintf("INSERT INTO notification_read " .
                                "(user_id, activity_id, date_read) " .
                                "VALUES (%d, %d, '%s')",
                                $_SESSION['user_id'], $r['activity_id'],
                                $r['date_entered']);
                $this->utility_model->run_query($sql);
            }
            unset($r);
        }

        foreach ($notifications as &$notif) {
            // Get the name of the actor and subject.
            $notif['actor'] = $this->get_profile_name($notif['actor_id']);
            $notif['subject'] = $this->get_profile_name($notif['subject_id']);

            // If it is a like, comment, or share of a post,
            if (in_array($notif['activity'], array('like','comment','share')) &&
                ($notif['source_type'] === 'post')) {
                // Get brief contents of the post.
                $post_sql = sprintf("SELECT post FROM posts WHERE (post_id = %d) LIMIT 1",
                                    $notif['source_id']);
                $query = $this->utility_model->run_query($post_sql);
                $post = $query->row_array()['post'];
                $notif['post'] = character_limiter($post, 75);
            }

            if (in_array($notif['activity'], array('comment','reply')) &&
                $notif['subject_id'] != $_SESSION['user_id']) {
                // Get the gender of the subject.
                $gender_sql = sprintf("SELECT gender FROM users WHERE user_id = %d LIMIT 1",
                                        $notif['subject_id']);
                $gender_query = $this->utility_model->run_query($gender_sql);
                $notif['subject_gender'] = ($gender_query->row_array()['gender'] == 'M')? 'his': 'her';
            }

            // If it is a like, or reply to a comment/reply,
            if (in_array($notif['activity'], array('like','reply')) &&
                in_array($notif['source_type'], array('comment','reply'))) {
                $comment_sql = sprintf("SELECT comment FROM comments " .
                                        "WHERE (comment_id = %d) LIMIT 1",
                                        $notif['source_id']);
                $query = $this->utility_model->run_query($comment_sql);
                $comment = $query->row_array()['comment'];

                // get short comment.
                $notif['comment'] = character_limiter($comment, 25);
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

            // If it's a birthday, birthday message, or like of a birthday message.
            if ($notif['activity'] == 'birthday' ||
                $notif['activity'] == 'message' ||
                ($notif['activity'] == 'like' && $notif['source_type'] == 'birthday_message')) {

                // Get date of birth for birthday notifications.
                $notif['dob'] = $this->get_dob($notif['subject_id']);

                // Add age.
                $notif['age'] = date_format(date_create($notif['date_entered']), 'Y') -
                                date_format(date_create($notif['dob']), 'Y');
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
        $fr_sql = sprintf("SELECT id FROM friends " .
                            "WHERE (user_id = %d AND friend_id = %d) OR " .
                                    "(user_id = %d AND friend_id = %d) " .
                            "LIMIT 1",
                            $_SESSION['user_id'], $user_id,
                            $user_id, $_SESSION['user_id']);
        $fr_query = $this->utility_model->run_query($fr_sql);
        $data['are_friends'] = FALSE;
        if ($fr_query->num_rows() == 1) {
            $data['are_friends'] = TRUE;
        }

        if ($data['are_friends']) {
            $data['fr_sent'] = TRUE;
        }
        else {
            // Check to see if a friend request has already been sent.
            $req_sql = sprintf("SELECT request_id, user_id, target_id " .
                                "FROM friend_requests " .
                                "WHERE (user_id = %d AND target_id = %d) OR " .
                                        "(user_id = %d AND target_id = %d) " .
                                "LIMIT 1",
                                $_SESSION['user_id'], $user_id,
                                $user_id, $_SESSION['user_id']);
            $req_query = $this->utility_model->run_query($req_sql);

            $data['fr_sent'] = FALSE;
            if ($req_query->num_rows() == 1) {
                $data['fr_sent'] = TRUE;
                $data['user_id'] = $req_query->row_array()['user_id'];
                $data['target_id'] = $req_query->row_array()['target_id'];
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
            $users_sql = sprintf("SELECT user_id, profile_name FROM users " .
                                "WHERE (user_id != %d) LIMIT %d, %d",
                                $_SESSION['user_id'], $offset, $limit);
        }
        else {
            $users_sql = sprintf("SELECT user_id, profile_name FROM users " .
                                "WHERE (user_id != %d)",
                                $_SESSION['user_id']);
        }
        $users_query = $this->utility_model->run_query($users_sql);
        $results = $users_query->result_array();

        $users = array();
        foreach ($results as $user) {
            // Only add to list if they are not friends and friend request hasn't been sent.
            $fr_status = $this->get_friendship_status($user['user_id']);
            if (!$fr_status['are_friends'] && !$fr_status['fr_sent']) {
                $user['profile_name'] = ucfirst($user['profile_name']);
                $user['profile_pic_path'] = $this->get_profile_pic_path($user['user_id']);
                array_push($users, $user);
            }
        }

        return $users;
    }

    public function get_num_friend_requests($filter=TRUE)
    {
        if ($filter) {
            $req_sql = sprintf("SELECT COUNT(user_id) FROM friend_requests " .
                                "WHERE (target_id = %d AND seen IS FALSE AND confirmed IS FALSE)",
                                $_SESSION['user_id']);
        }
        else {
            $req_sql = sprintf("SELECT COUNT(user_id) FROM friend_requests " .
                                "WHERE (target_id = %d AND confirmed IS FALSE)",
                                $_SESSION['user_id']);
        }

        return $this->utility_model->run_query($req_sql)->row_array()['COUNT(user_id)'];
    }

    public function get_friend_requests()
    {
        $req_sql = sprintf("SELECT user_id, seen FROM friend_requests " .
                            "WHERE (target_id = %d AND confirmed IS FALSE) " .
                            "ORDER BY date_entered DESC",
                            $_SESSION['user_id']);
        $req_query = $this->utility_model->run_query($req_sql);

        $friend_requests = $req_query->result_array();
        foreach ($friend_requests as &$fr) {
            if (!$fr['seen']) {
                $update_sql = sprintf("UPDATE friend_requests SET seen = 1 " .
                                        "WHERE (target_id = %d AND user_id = %d)",
                                        $_SESSION['user_id'], $fr['user_id']);
                $this->utility_model->run_query($update_sql);
            }

            $fr['profile_name'] = $this->get_profile_name($fr['user_id']);
            $fr['profile_pic_path'] = $this->get_profile_pic_path($fr['user_id']);
        }
        unset($fr);

        return $friend_requests;
    }

    public function send_friend_request($target_id)
    {
        $friendship_status = $this->get_friendship_status($target_id);
        if ($friendship_status['are_friends'] || $friendship_status['fr_sent']) {
            throw new IllegalAccessException();
        }

        $fr_sql = sprintf("INSERT INTO friend_requests " .
                            "(user_id, target_id) VALUES (%d, %d)",
                            $_SESSION['user_id'], $target_id);
        $this->utility_model->run_query($fr_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'user', 'friend_request')",
                                $_SESSION['user_id'], $target_id, $target_id);
        $this->utility_model->run_query($activity_sql);
    }

    public function confirm_friend_request($friend_id)
    {
        // First check whether a friend request actually exist.
        $id_sql = sprintf("SELECT request_id FROM friend_requests " .
                            "WHERE (target_id = %d AND user_id = %d)",
                            $_SESSION['user_id'], $friend_id);
        $id_query = $this->utility_model->run_query($id_sql);
        if ($id_query->num_rows() == 0) {
            throw new IllegalAccessException();
        }

        // Add the user to the list of friends.
        $fr_sql = sprintf("INSERT INTO friends " .
                            "(user_id, friend_id) VALUES (%d, %d)",
                            $_SESSION['user_id'], $friend_id);
        $this->utility_model->run_query($fr_sql);

        // Update the friend_requests table.
        $update_sql = sprintf("UPDATE friend_requests SET confirmed = 1 " .
                                "WHERE (user_id = %d AND target_id = %d)",
                                $friend_id, $_SESSION['user_id']);
        $this->utility_model->run_query($update_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'user', 'confirmed_friend_request')",
                                $_SESSION['user_id'], $friend_id, $_SESSION['user_id']);
        $this->utility_model->run_query($activity_sql);

        return TRUE;
    }

    public function send_message($message, $receiver_id)
    {
        $message_sql = sprintf("INSERT INTO messages " .
                                "(sender_id, receiver_id, message) " .
                                "VALUES (%d, %d, %s)",
                                $_SESSION['user_id'], $receiver_id,
                                $this->db->escape($message));
        $this->utility_model->run_query($message_sql);
    }

    public function get_num_conversation($user_id)
    {
        $sql = sprintf("SELECT COUNT(message_id) FROM messages " .
                        "WHERE (receiver_id = %d AND sender_id = %d) OR " .
                                "(receiver_id = %d AND sender_id = %d)",
                        $user_id, $_SESSION['user_id'],
                        $_SESSION['user_id'], $user_id);
        return $this->utility_model->run_query($sql)->row_array()['COUNT(message_id)'];
    }

    public function get_conversation($user_id, $offset, $limit)
    {
        $sql = sprintf("SELECT * FROM messages " .
                     "WHERE (receiver_id = %d AND sender_id = %d) OR " .
                            "(receiver_id = %d AND sender_id = %d) " .
                     "ORDER BY date_sent DESC LIMIT %d, %d",
                     $user_id, $_SESSION['user_id'],
                     $_SESSION['user_id'], $user_id,
                     $offset, $limit);
        $query = $this->utility_model->run_query($sql);

        $messages = $query->result_array();
        $sender = $this->get_profile_name($_SESSION['user_id']);
        $receiver = $this->get_profile_name($user_id);
        foreach ($messages as &$msg) {
            if ($msg['sender_id'] == $_SESSION['user_id']) {
                $msg['sender'] = $sender;
            }
            else if ($msg['sender_id'] == $user_id) {
                $msg['sender'] = $receiver;
            }

            if (($msg['seen'] == 0) && ($msg['receiver_id'] == $_SESSION['user_id'])) {
                $update_sql = sprintf("UPDATE messages SET seen = 1 " .
                                        "WHERE (message_id = %d) LIMIT 1",
                                        $msg['message_id']);
                $this->utility_model->run_query($update_sql);
            }

            // Add the timespan.
            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
        }
        unset($msg);

        return $messages;
    }

    public function get_num_news_feed_posts_and_photos()
    {
        $friends_ids = $this->get_friends_ids();
        // Add a zero element; so if network is empty the IN part of the query won't fail
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        /* Get IDs of shared posts and photos. */

        // Query to get IDS of shared posts.
        $shared_posts_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (user_id IN(%s) AND subject_type = 'post')",
                                        $friends_ids);
        $shared_posts_ids_results = $this->utility_model->run_query($shared_posts_ids_sql)->result_array();
        // Add an extra element for safety.
        $shared_posts_ids[] = 0;
        foreach ($shared_posts_ids_results as $r) {
            $shared_posts_ids[] = $r['subject_id'];
        }
        $shared_posts_ids = implode(',', $shared_posts_ids);

        // Get IDs of shared photos.

        $shared_photos_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                         "WHERE (user_id IN(%s) AND subject_type = 'photo')",
                                         $friends_ids);
        $shared_photos_ids_results = $this->utility_model->run_query($shared_photos_ids_sql)->result_array();
        // Add an extra element for safety.
        $shared_photos_ids[] = 0;
        foreach ($shared_photos_ids_results as $r) {
            $shared_photos_ids[] = $r['subject_id'];
        }
        $shared_photos_ids = implode(',', $shared_photos_ids);

        $num_posts_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                 "WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND " .
                                        "activity = 'post')",
                                 $friends_ids, $shared_posts_ids);
        $num_posts = $this->utility_model->run_query($num_posts_sql)->row_array()['COUNT(source_id)'];

        $num_photos_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                 "WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND " .
                                        "activity IN('photo','profile_pic_change'))",
                                 $friends_ids, $shared_photos_ids);
        $num_photos = $this->utility_model->run_query($num_photos_sql)->row_array()['COUNT(source_id)'];

        $num_shared_posts_and_photos_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                                    "WHERE (actor_id IN(%s) AND activity = 'share' AND " .
                                                    "source_type IN('post', 'photo') AND subject_id != %d)",
                                                    $friends_ids, $_SESSION['user_id']);
        $num_shared_posts_and_photos = $this->utility_model->run_query($num_shared_posts_and_photos_sql)->row_array()['COUNT(source_id)'];

        return ($num_posts + $num_photos + $num_shared_posts_and_photos);
    }

    public function get_news_feed_posts_and_photos($offset, $limit)
    {
        $friends_ids = $this->get_friends_ids();
        // Add a zero element; so if network is empty the IN part of the query won't fail
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        /* Get IDs of shared posts and photos.
         * IDs are got seperately b'se an ID of a shared post can be equal to an ID of
         * a shared photo as they are in different tables.
         */

        // Query to get IDS of shared posts.
        $shared_posts_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (user_id IN(%s) AND subject_type = 'post')",
                                        $friends_ids);
        $shared_posts_ids_results = $this->utility_model->run_query($shared_posts_ids_sql)->result_array();
        // Add an extra element for safety.
        $shared_posts_ids[] = 0;
        foreach ($shared_posts_ids_results as $r) {
            $shared_posts_ids[] = $r['subject_id'];
        }
        $shared_posts_ids = implode(',', $shared_posts_ids);

        // Get IDs of shared photos.
        $shared_photos_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                         "WHERE (user_id IN(%s) AND subject_type = 'photo')",
                                         $friends_ids);
        $shared_photos_ids_results = $this->utility_model->run_query($shared_photos_ids_sql)->result_array();
        // Add an extra element for safety.
        $shared_photos_ids[] = 0;
        foreach ($shared_photos_ids_results as $r) {
            $shared_photos_ids[] = $r['subject_id'];
        }
        $shared_photos_ids = implode(',', $shared_photos_ids);

        // Query to get all posts and photos by this user's friends.
        // Get shared posts and photos.

        // If the last user to share a post or photo is the current viewer of the page,
        // then we pick the second last user who shared the same posts or photo.
        $latest_share_date_sql = sprintf("SELECT MAX(date_shared) FROM shares s2 " .
                                        "WHERE (s1.subject_id = s2.subject_id AND " .
                                                "s1.subject_type = s2.subject_type AND " .
                                                "s2.user_id != %d)",
                                        $_SESSION['user_id']);

        $latest_shared_posts_user_ids_sql = sprintf("SELECT user_id FROM shares s1 " .
                                                    "WHERE (user_id IN(%s) AND subject_type = 'post' AND " .
                                                    "date_shared = (%s))",
                                                    $friends_ids, $latest_share_date_sql);
        $latest_shared_posts_user_ids_results = $this->utility_model->run_query($latest_shared_posts_user_ids_sql)->result_array();
        // Add an extra element for safety.
        $latest_shared_posts_user_ids[] = 0;
        foreach ($latest_shared_posts_user_ids_results as $r) {
            $latest_shared_posts_user_ids[] = $r['user_id'];
        }
        $latest_shared_posts_user_ids = implode(',', $latest_shared_posts_user_ids);

        $latest_shared_photos_user_ids_sql = sprintf("SELECT user_id FROM shares s1 " .
                                                    "WHERE (user_id IN(%s) AND subject_type = 'photo' AND " .
                                                    "date_shared = (%s))",
                                                    $friends_ids, $latest_share_date_sql);
        $latest_shared_photos_user_ids_results = $this->utility_model->run_query($latest_shared_photos_user_ids_sql)->result_array();
        // Add an extra element for safety.
        $latest_shared_photos_user_ids[] = 0;
        foreach ($latest_shared_photos_user_ids_results as $r) {
            $latest_shared_photos_user_ids[] = $r['user_id'];
        }
        $latest_shared_photos_user_ids = implode(',', $latest_shared_photos_user_ids);

        // We also don't show posts by this user that was shared by his/her friends.
        $posts_and_photos_sql = sprintf("SELECT * FROM activities " .
                                        "WHERE ((actor_id IN(%s) AND activity = 'post' AND source_id NOT IN(%s)) OR " .
                                                "(actor_id IN(%s) AND activity IN('photo', 'profile_pic_change') AND " .
                                                    "source_id NOT IN(%s)) OR " .
                                                "(actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND " .
                                                    "source_type = 'photo' AND subject_id != %d) OR " .
                                                "(actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND " .
                                                    "source_type = 'post' AND subject_id != %d)) " .
                                        "ORDER BY date_entered DESC LIMIT %d, %d",
                                        $friends_ids, $shared_posts_ids,
                                        $friends_ids, $shared_photos_ids,
                                        $friends_ids, $latest_shared_photos_user_ids, $_SESSION['user_id'],
                                        $friends_ids, $latest_shared_posts_user_ids, $_SESSION['user_id'],
                                        $offset, $limit);
        $posts_and_photos = $this->utility_model->run_query($posts_and_photos_sql)->result_array();


        foreach ($posts_and_photos as &$r) {
            switch ($r['source_type']) {
                case 'post':
                    $r['post'] = $this->post_model->get_post($r['source_id']);

                    // Get only 540 characters from post if possible.r['post']
                    $post_url = base_url("user/post/{$r['post']['post_id']}");
                    $r['post']['post'] = character_limiter($r['post']['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");

                    // Is it a shared post.
                    $r['post']['shared'] = FALSE;
                    if (in_array($r['source_id'], explode(',', $shared_posts_ids))) {
                        $r['post']['shared'] = TRUE;
                        $r['post']['sharer_id'] = $r['actor_id'];
                        $r['post']['sharer'] = $this->user_model->get_profile_name($r['actor_id']);

                        // Change timespan to match the date it was shared on.
                        $r['post']['timespan'] = timespan(mysql_to_unix($r['date_entered']), now(), 1);

                        // Replace author's profile_pic with the one for sharer.
                        $r['post']['profile_pic_path'] = $this->get_profile_pic_path($r['actor_id']);
                    }
                    break;
                case 'photo':
                    $r['photo'] = $this->photo_model->get_photo($r['source_id']);

                    // Is it a shared photo.
                    $r['photo']['shared'] = FALSE;
                    if (in_array($r['source_id'], explode(',', $shared_photos_ids))) {
                        $r['photo']['shared'] = TRUE;
                        $r['photo']['sharer_id'] = $r['actor_id'];
                        $r['photo']['sharer'] = $this->user_model->get_profile_name($r['actor_id']);

                        // Change the timespan to match the date it was shared.
                        $r['photo']['timespan'] = timespan(mysql_to_unix($r['date_entered']), now(), 1);

                        // Replace author's profile_pic with the one for sharer.
                        $r['photo']['profile_pic_path'] = $this->get_profile_pic_path($r['actor_id']);
                    }
                    break;
                default:
                    break;
                    # do nothing.
            }
        }
        unset($r);

        return $posts_and_photos;
    }
}
?>
