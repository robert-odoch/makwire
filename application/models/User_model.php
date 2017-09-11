<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

/**
 * Contains functions related to a particular user.
 */
class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'post_model', 'photo_model',
            'video_model', 'birthday_message_model'
        ]);
    }

    /**
     * Closure used for sorting notifications.
     *
     * @param $key the field that will be used for sorting.
     */
    private function build_sorter($key)
    {
        return function ($a, $b) use ($key) {
            $interval = date_diff(date_create($a[$key]), date_create($b[$key]));
            $interval = $interval->format("%R%s");
            return $interval;
        };
    }

    /**
     * Checks whether a user is currently logged in.
     *
     * @param $user_id ID of the user to check for.
     * @return TRUE if this user is logged in.
     */
    public function active($user_id)
    {
        $logged_in_sql = sprintf("SELECT logged_in FROM users WHERE user_id = %d LIMIT 1",
                                    $user_id);
        $query = $this->utility_model->run_query($logged_in_sql);

        return $query->row_array()['logged_in'];
    }

    /**
     * Checks whether two users are friends.
     *
     * @param $user_id ID of the user to check for.
     * @return true if the current logged in user is a friend to the user
     * with the specified ID.
     */
    public function are_friends($user_id, $other_id)
    {
        if ($user_id == $other_id) {
            return TRUE;
        }

        $friends_ids = $this->get_friends_ids($user_id);
        return in_array($other_id, $friends_ids);
    }

    /**
     * Checks whether a user has reached age and
     * and whether the user reached the specified age when he was
     * already registered on makwire.
     *
     * @param $user_id the ID of the user having birthday.
     * @param $age the user's age on this birthday.
     * @return true if user has reached age and the user reached this age when
     * he was already registered on makwire
     */
    public function can_view_birthday($user_id, $age)
    {
        $sql = sprintf("SELECT YEAR(dob) AS year, MONTH(dob) AS month,
                        DAY(dob) AS day, date_created
                        FROM users WHERE (user_id = %d)", $user_id);
        $query = $this->utility_model->run_query($sql);
        if ($query->num_rows() == 0) {
            return FALSE;
        }

        $result = $query->row_array();
        return (date_create(($result['year']+$age) . "-{$result['month']}-{$result['day']}") >
                    date_create($result['date_created'])) &&
                ($age <= (date('Y') - $result['year']));
    }

    /**
     * Gets the IDs of the current logged in user's friends.
     *
     * @return Array for friends IDs.
     */
    public function get_friends_ids($user_id)
    {
        $friends_sql = sprintf("SELECT user_id, friend_id FROM friends
                                WHERE (user_id = %d) OR (friend_id = %d)",
                                $user_id, $user_id);
        $friends_query = $this->utility_model->run_query($friends_sql);

        $friends = array();
        $results = $friends_query->result_array();
        foreach ($results as &$f) {
            if ($f['friend_id'] == $user_id) {
                $f['friend_id'] = $f['user_id'];
            }
            array_push($friends, $f['friend_id']);
        }
        unset($f);

        return $friends;
    }

    /**
     * Gets the date of birth for a user.
     *
     * @param @user_id the ID of the user whose DOB is required.
     * @return DOB for this user.
     */
    public function get_dob($user_id)
    {
        $sql = sprintf("SELECT dob FROM users WHERE user_id = %d", $user_id);
        $query = $this->utility_model->run_query($sql);

        return $query->row_array()['dob'];
    }

    /**
     * Sets up variables common to all user accounts.
     *
     * @return Array of these common variables.
     */
    public function initialize_user($user_id)
    {
        $data['profile_pic_path'] = $this->get_profile_pic_path($user_id);
        $data['primary_user'] = $this->get_profile_name($user_id);
        $data['people_you_may_know'] = $this->get_suggested_users($user_id, 0, 4);
        $data['num_friend_requests'] = $this->get_num_friend_requests($user_id, TRUE);
        $data['num_active_friends'] = $this->get_num_chat_users($user_id, TRUE);
        $data['num_new_messages'] = $this->get_num_messages($user_id, TRUE);
        $data['num_new_notifs'] = $this->get_num_notifications($user_id, TRUE);
        $data['chat_users'] = $this->get_chat_users($user_id, TRUE);

        return $data;
    }

    /**
    * Gets the profile name for a user.
    *
    * @param $user_id the ID of the user whose name is required.
    * @return this user's profile name.
    */
    public function get_profile_name($user_id)
    {
        $name_sql = sprintf("SELECT profile_name FROM users WHERE user_id = %d",
                            $user_id);
        $query = $this->utility_model->run_query($name_sql);
        if ($query->num_rows() == 0) {
            throw new NotFoundException();
        }

        return ucfirst($query->row_array()['profile_name']);
    }

    /**
     * Gets a path for a user's profile picture that can be used on the web.
     *
     * @param $user_id the ID of the user whose profile picture is required.
     * @return path to profile picture if the user has set a profile picture or
     * path to the dafault profile picture.
     */
    public function get_profile_pic_path($user_id)
    {
        $path_sql = sprintf("SELECT profile_pic_path FROM users
                            WHERE (user_id = %d AND profile_pic_path IS NOT NULL)",
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

    /**
     * Gets the number of posts, photos, videos, and links
     * to be displayed on a user's timeline.
     *
     * @param $user_id ID of the user to be shown.
     * @return number of posts, photos, videos, and links to be displayed
     * on this user's timeline.
     */
    public function get_num_timeline_items($user_id)
    {
        // Get number of posts, photos, videos, and links posted by this user
        // and those shared by this user that are by friends
        // to the user viewing the page.
        $num_timeline_items_sql = sprintf("SELECT COUNT(activity_id) FROM activities
                                            WHERE ((actor_id = %d AND
                                            activity IN('post','photo','video','link','profile_pic_change')) OR
                                            (actor_id = %d AND activity = 'share'))",
                                            $user_id, $user_id);
        $query = $this->utility_model->run_query($num_timeline_items_sql);

        return $query->row_array()['COUNT(activity_id)'];
    }

    /**
     * Gets the posts, photos, videos, and links to be displayed on a user's timeline.
     *
     * @param $user_id ID of the user to be displayed.
     * @return posts, photos, videos, and links to be displayed.
     */
    public function get_timeline_items($user_id, $visitor_id, $offset, $limit)
    {
        // Get posts, photos, videos, and links posted/shared by this user.
        $timeline_items_sql = sprintf("SELECT * FROM activities
                                        WHERE ((actor_id = %d AND
                                                activity IN('post','photo','video','link','profile_pic_change')) OR
                                                (actor_id = %d AND activity = 'share'))
                                        ORDER BY date_entered DESC LIMIT %d, %d",
                                        $user_id, $user_id, $offset, $limit);
        $timeline_items = $this->utility_model->run_query($timeline_items_sql)->result_array();

        foreach ($timeline_items as &$r) {
            switch ($r['source_type']) {
                case 'post':
                    $r['post'] = $this->post_model->get_post($r['source_id'], $visitor_id);

                    // Get only 540 characters from post if possible.
                    $post_url = base_url("user/post/{$r['post']['post_id']}");
                    $r['post']['post'] = character_limiter($r['post']['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");

                    // Was it shared from another user?
                    $r['post']['shared'] = FALSE;
                    if ($r['activity'] == 'share') {
                        $r['post']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('post', $r);
                    }
                    break;
                case 'photo':
                    $r['photo'] = $this->photo_model->get_photo($r['source_id'], $visitor_id);

                    // Was it shared from another user?
                    $r['photo']['shared'] = FALSE;
                    if($r['activity'] == 'share') {
                        $r['photo']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('photo', $r);
                    }
                    break;
                case 'video':
                    $r['video'] = $this->video_model->get_video($r['source_id'], $visitor_id);

                    // Was it shared from another user?
                    $r['video']['shared'] = FALSE;
                    if ($r['activity'] == 'share') {
                        $r['video']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('video', $r);
                    }
                    break;
                case 'link':
                    $r['link'] = $this->link_model->get_link($r['source_id'], $visitor_id);

                    // Was it shared from another user?
                    $r['link']['shared'] = FALSE;
                    if($r['activity'] == 'share') {
                        $r['link']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('link', $r);
                    }
                    break;
                default:
                    # do nothing.
                    break;
            }
        }
        unset($r);

        return $timeline_items;
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
        $query = $this->utility_model->run_query($sql);

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
        $query = $this->utility_model->run_query($sql);
        $messages = $query->result_array();

        foreach ($messages as &$m) {
            $m = $this->birthday_message_model->get_message($m['id'], $user_id);
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
        $this->utility_model->run_query($sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities
                                (actor_id, subject_id, source_id, source_type, activity)
                                VALUES (%d, %d, %d, 'user', 'message')",
                                $sender_id, $receiver_id, $receiver_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Gets the number of messages sent to a user from chat.
     *
     * @param $filter whether to return only unread messages.
     * @return number of messages.
     */
    public function get_num_messages($user_id, $filter = TRUE)
    {
        if ($filter) {
            $sql = sprintf("SELECT COUNT(message_id) FROM messages WHERE (receiver_id = %d AND seen IS FALSE)",
                            $user_id);
        }
        else {
            $sql = sprintf("SELECT COUNT(message_id) FROM messages WHERE receiver_id = %d",
                            $user_id);
        }

        $query = $this->utility_model->run_query($sql);
        return $query->row_array()['COUNT(message_id)'];
    }

    /**
     * Gets the messages sent to a user from chat.
     *
     * @param $offset
     * @param $limit
     * @param $filter whether to return only unread messages.
     */
    public function get_messages($user_id, $offset, $limit, $filter = TRUE)
    {
        if ($filter) {
            $sql = sprintf("SELECT DISTINCT sender_id
                            FROM messages WHERE (receiver_id = %d AND seen IS FALSE)
                            ORDER BY date_sent DESC LIMIT %d, %d",
                            $user_id, $offset, $limit);
        }
        else {
            $sql = sprintf("SELECT DISTINCT sender_id
                            FROM messages WHERE (receiver_id = %d)
                            ORDER BY date_sent DESC LIMIT %d, %d",
                            $user_id, $offset, $limit);
        }
        $query = $this->utility_model->run_query($sql);

        $messages = $query->result_array();
        foreach ($messages as &$msg) {
            $message_sql = sprintf("SELECT message_id, message, seen, date_sent FROM messages
                                    WHERE (receiver_id = %d AND sender_id = %d)
                                    ORDER BY date_sent DESC LIMIT 1",
                                    $user_id, $msg['sender_id']);
            $message_query = $this->utility_model->run_query($message_sql);
            $msg = array_merge($msg, $message_query->row_array());

            if (!$msg['seen']) {
                $update_sql = sprintf("UPDATE messages SET seen = 1 WHERE (message_id = %d) LIMIT 1",
                                        $msg['message_id']);
                $this->utility_model->run_query($update_sql);
            }

            $msg['sender'] = $this->get_profile_name($msg['sender_id']);
            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
        }
        unset($msg);

        return $messages;
    }

    /**
     * Gets the number of friends for a user.
     *
     * @param $user_id ID of the user whose number of friends is required.
     * @return number of friends for this user.
     */
    public function get_num_friends($user_id)
    {
        $sql = sprintf("SELECT COUNT(id) FROM friends WHERE (user_id = %d) OR (friend_id = %d)",
                        $user_id, $user_id);
        $query = $this->utility_model->run_query($sql);

        return $query->row_array()['COUNT(id)'];
    }

    /**
     * Gets the friends for a user.
     *
     * @param $user_id ID of user whose friends are required.
     * @param $offset
     * @param $limit
     * @return this user's friends.
     */
    public function get_friends($user_id, $offset, $limit)
    {
        $sql = sprintf("SELECT user_id, friend_id FROM friends
                        WHERE (user_id = %d) OR (friend_id = %d)
                        LIMIT %d, %d",
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

    /**
     * Gets the number of photos uploaded by a user.
     *
     * @param $user_id ID of user whose number of photos is required.
     * @return number of photos uploaded by this user.
     */
    public function get_num_photos($user_id)
    {
        $sql = sprintf("SELECT COUNT(photo_id) FROM photos WHERE (user_id = %d)",
                        $user_id);
        $query = $this->utility_model->run_query($sql);

        return $query->row_array()['COUNT(photo_id)'];
    }

    /**
     * Gets the photos uploaded by a user.
     *
     * @param $user_id ID of user whose photos are required.
     * @param $offset
     * @param $limit
     * @return photos uploaded by this user.
     */
    public function get_photos($user_id, $visitor_id, $offset, $limit)
    {
        $sql = sprintf("SELECT photo_id FROM photos
                        WHERE (user_id = %d) ORDER BY date_entered DESC
                        LIMIT %d, %d",
                        $user_id, $offset, $limit);
        $results = $this->utility_model->run_query($sql)->result_array();

        $photos = [];
        foreach ($results as $r) {
            $photos[] = $this->photo_model->get_photo($r['photo_id'], $visitor_id);
        }

        return $photos;
    }

    /**
     * Gets all friends for a user.
     *
     * @param $user_id ID of user whose friends are required.
     * @return all friends for user with ID $user_id.
     */
    public function get_all_friends($user_id)
    {
        $sql = sprintf("SELECT user_id, friend_id FROM friends
                        WHERE (user_id = %d) OR (friend_id = %d)",
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

    /**
     * Gets the number of friends who are currently logged in.
     *
     * @param $filter whether to count only logged in users.
     * @return number friends who are currently logged in.
     */
    public function get_num_chat_users($user_id, $filter = TRUE)
    {
        return count($this->get_chat_users($user_id, TRUE));
    }

    /**
     * Gets friends who are currently logged in.
     *
     * @param $filter whether to strictly return only logged in users.
     * @return friends who are currently logged in.
     */
    public function get_chat_users($user_id, $filter = TRUE)
    {
        $chat_users = array();

        // Get this user's friends.
        $friends = $this->get_all_friends($user_id);

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

    /**
     * Gets users whose name or email address matches a query.
     *
     * @param $query name or email address entered by user.
     * @param $offset
     * @param $limit
     * @return Array users whose name or email address match the query.
     */
    public function get_searched_user($query, $visitor_id, $offset, $limit)
    {
        $friends_ids = $this->get_friends_ids($visitor_id);
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        if (filter_var($query, FILTER_VALIDATE_EMAIL)) {
            $sql = sprintf("SELECT ue.user_id, u.profile_name FROM user_emails ue
                            LEFT JOIN users u ON(ue.user_id = u.user_id)
                            WHERE (ue.email = '%s' AND ue.user_id NOT IN(%s))",
                            $query, $friends_ids);
        }
        else {
            $keywords = preg_split("/[\s,]+/", $query);
            foreach ($keywords as &$keyword) {
                $keyword = strtolower("+{$keyword}");

                // The @ sign breaks this query if it is used in an invalid email address.
                $keyword = str_replace('@', '', $keyword);
            }
            unset($keyword);

            $key = implode(' ', $keywords);
            $sql = sprintf("SELECT user_id, profile_name FROM users
                            WHERE MATCH(profile_name) AGAINST (%s IN BOOLEAN MODE) AND
                                    user_id NOT IN(%s)
                            LIMIT %d, %d", $this->db->escape($key), $friends_ids,
                            $offset, $limit);
        }

        $results = $this->utility_model->run_query($sql)->result_array();
        foreach ($results as &$r) {
            $r['profile_pic_path'] = $this->get_profile_pic_path($r['user_id']);
        }
        unset($r);

        return $results;
    }

    /**
     * Gets the number of notifications for a user.
     *
     * @param $fitlter specifies whether only unread notifications should be
     * counted.
     * @return number of notifications for this user.
     */
    public function get_num_notifications($user_id, $filter = TRUE)
    {
        // Notifications that can/can't be combined together if the are performed on
        // the same object.
        $combined_notifs = ['message', 'like', 'comment', 'reply', 'share'];
        $combined_notifs_str = "'message', 'like', 'comment', 'reply', 'share'";
        $atomic_notifs_str = "'birthday', 'profile_pic_change', 'friend_request',
                                'join_group_request', 'confirmed_friend_request',
                                'confirmed_join_group_request', 'added_photo'";

        // Get the ID's of all this user's friends.
        $friends_ids = $this->get_friends_ids($user_id);
        $friends_ids[] = 0;  // Add an extra lement for query-safety.
        $friends_ids_str = implode(',', $friends_ids);

        // WHERE clause for notifications having this user as a direct target.
        $primary_notifs_clause = sprintf("subject_id = %d AND actor_id != %d",
                                         $user_id, $user_id);

        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_sql = sprintf("SELECT date_read FROM notification_read
                                            WHERE (user_id = %d) ORDER BY date_read DESC
                                            LIMIT 1",
                                            $user_id);
            $last_read_date_query = $this->utility_model->run_query($last_read_date_sql);
            if ($last_read_date_query->num_rows() == 0) {

                // User has'nt read any notifications before,
                // use his account creation date.
                $last_read_date_sql = sprintf("SELECT date_created FROM users WHERE (user_id = %d)",
                                                $user_id);
            }

            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities
                                        WHERE (actor_id = %d AND subject_id != %d AND
                                                activity IN('share', 'comment', 'reply'))",
                                        $user_id, $user_id);

            // WHERE clause for notifications from other sources like profile_pic_change, comment and reply.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND
                                            ((source_id IN(%s) AND activity IN('comment', 'reply')) OR
                                            activity = 'profile_pic_change')",
                                            $friends_ids_str, $friends_ids_str, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2
                                            WHERE (((%s) OR (%s)) AND activity IN(%s)
                                                    AND a1.subject_id = a2.subject_id AND
                                                    a1.source_id = a2.source_id AND
                                                    a1.source_type = a2.source_type AND
                                                    a1.activity = a2.activity AND date_entered > (%s))",
                                            $primary_notifs_clause, $other_notifs_clause, $combined_notifs_str,
                                            $last_read_date_sql);

            // Query to get all notifications from activities.
    		$notifs_sql = sprintf("SELECT activity_id, subject_id, activity FROM activities a1
                                    WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND
                                    activity IN(%s) AND date_entered > (%s)))",
                				    $latest_notif_sql, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs_str, $last_read_date_sql);

            // Get Birthday notifications.
            $birthdays_sql = sprintf("SELECT user_id FROM users
                                        WHERE user_id IN(%s) AND DAY(dob) = %d AND MONTH(dob) = %d",
                                        $friends_ids_str, date('d'), date('m'));
        }
        else {
            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities
                                    WHERE (actor_id = %d AND subject_id != %d AND
                                    activity IN('share', 'comment', 'reply'))",
                                    $user_id, $user_id);

            // WHERE clause for notifications from other sources like profile_pic_change, comment, reply and birthday.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND
                                            ((source_id IN(%s) AND activity IN('comment', 'reply')) OR
                                            activity IN('profile_pic_change','birthday'))",
                                            $friends_ids_str, $friends_ids_str, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2
                                        WHERE (((%s) OR (%s)) AND activity IN(%s)
                                        AND a1.subject_id = a2.subject_id AND
                                        a1.source_id = a2.source_id AND
                                        a1.source_type = a2.source_type AND
                                        a1.activity = a2.activity)",
                                        $primary_notifs_clause, $other_notifs_clause,
                                        $combined_notifs_str);

            // Query to get all notifications from activities.
            $notifs_sql = sprintf("SELECT activity_id, subject_id, activity FROM activities a1
                                    WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND activity IN(%s)))",
                                    $latest_notif_sql, $primary_notifs_clause, $other_notifs_clause,
                                    $atomic_notifs_str);
        }

        $notifs_query = $this->utility_model->run_query($notifs_sql);
        $notifs_results = $notifs_query->result_array();

        if ($filter) {
            // Don't count friend_requests that have already been seen.
            $num_results = count($notifs_results);
            for ($i = 0; $i < $num_results; ++$i) {
                if ($notifs_results[$i]['activity'] == 'friend_request') {
                    $source_sql = sprintf("SELECT source_id FROM  activities WHERE activity_id = %d",
                                            $notifs_results[$i]['activity_id']);
                    $source_result = $this->utility_model->run_query($source_sql)->row_array();

                    $fr_seen_sql = sprintf("SELECT seen FROM friend_requests WHERE request_id = %d",
                                            $source_result['source_id']);
                    $fr_seen_result = $this->utility_model->run_query($fr_seen_sql)->row_array();
                    if ($fr_seen_result['seen']) {
                        unset($notifs_results[$i]);
                    }
                }
            }
        }

        $num_notifications = count($notifs_results);

        if (isset($birthdays_sql)) {
            $birthdays = $this->utility_model->run_query($birthdays_sql)->result_array();

            foreach ($birthdays as $bd) {
                // Check if this birthday has been inserted in activities table.
                $sql = sprintf("SELECT activity_id FROM activities
                                WHERE (actor_id = %d AND activity = 'birthday' AND
                                YEAR(date_entered) = %d)",
                                $bd['user_id'], date('Y'));
                $query = $this->utility_model->run_query($sql);

                if ($query->num_rows() == 0) {
                    // It is a brand new birthday.
                    ++$num_notifications;
                }
                else {
                    // Not so new, but check whether this user has seen it.
                    $seen_sql = sprintf("SELECT user_id FROM notification_read
                                        WHERE (user_id = %d AND activity_id = %d)",
                                        $user_id, $query->row_array()['activity_id']);
                    if ($this->utility_model->run_query($seen_sql)->num_rows() == 0) {
                        // Hasn't seen.
                        ++$num_notifications;
                    }
                }
            }
        }

        return $num_notifications;
    }

    /**
     * Gets notifications for a user.
     *
     * @param $offset
     * @param $limit
     * @param $fitlter specifies whether only unread notifications should be
     * returned.
     * @return notifications for this user.
     */
    public function get_notifications($user_id, $offset, $limit, $filter = TRUE)
    {
        // Notifications that can/can't be combined together if the are performed on
        // the same object.
        $combined_notifs = ['message', 'like', 'comment', 'reply', 'share'];
        $combined_notifs_str = "'message', 'like', 'comment', 'reply', 'share'";
        $atomic_notifs_str = "'birthday', 'profile_pic_change', 'friend_request', " .
                                "'join_group_request', 'confirmed_friend_request', " .
                                "'confirmed_join_group_request', 'added_photo'";

        // Get the ID's of all this user's friends.
        $friends_ids = $this->get_friends_ids($user_id);
        $friends_ids[] = 0;  // Add an extra element for query-safety.
        $friends_ids_str = implode(',', $friends_ids);

        // WHERE clause for notifications having this user as a direct target.
        $primary_notifs_clause = sprintf("subject_id = %d AND actor_id != %d",
                                         $user_id, $user_id);

        if ($filter) {
            // Query to get the last time the user read a notification.
            $last_read_date_sql = sprintf("SELECT date_read FROM notification_read
                                            WHERE (user_id = %d) ORDER BY date_read DESC
                                            LIMIT 1",
                                            $user_id);
            $last_read_date_query = $this->utility_model->run_query($last_read_date_sql);
            if ($last_read_date_query->num_rows() == 0) {
                // User has'nt read any notifications before, use his account creation date.
                $last_read_date_sql = sprintf("SELECT date_created FROM users WHERE (user_id = %d)",
                                                $user_id);
            }

            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities
                                    WHERE (actor_id = %d AND subject_id != %d AND
                                            activity IN('share', 'comment', 'reply'))",
                                    $user_id, $user_id);

            // WHERE clause for notifications from other sources like profile_pic_change, comment and reply.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND
                                            ((source_id IN(%s) AND activity IN('comment', 'reply'))  OR
                                            activity = 'profile_pic_change')",
                                            $friends_ids_str, $friends_ids_str, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2
                                        WHERE (((%s) OR (%s)) AND activity IN(%s)
                                        AND a1.subject_id = a2.subject_id AND
                                        a1.source_id = a2.source_id AND
                                        a1.source_type = a2.source_type AND
                                        a1.activity = a2.activity AND date_entered > (%s))",
                                        $primary_notifs_clause, $other_notifs_clause,
                                        $combined_notifs_str, $last_read_date_sql);

            // Query to get all notifications from activities.
    		$notifs_sql = sprintf("SELECT * FROM activities a1
                                    WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND
                                    activity IN(%s) AND date_entered > (%s)))
                                    ORDER BY date_entered DESC LIMIT %d, %d",
                				    $latest_notif_sql, $primary_notifs_clause,
                                    $other_notifs_clause, $atomic_notifs_str, $last_read_date_sql,
                                    $offset, $limit);

            // Get Birthday notifications.
            $birthdays_sql = sprintf("SELECT user_id, dob, CONCAT(dob, ' 00:00:00') as date_entered
                                        FROM users
                                        WHERE (user_id IN (%s) AND DAY(dob) = %d and MONTH(dob) = %d)",
                                        $friends_ids_str, date('d'), date('m'));
        }
        else {
            // Query to get activities that were performed by this user..
            $acted_on_sql = sprintf("SELECT DISTINCT source_id FROM activities
                                        WHERE (actor_id = %d AND subject_id != %d AND
                                                activity IN('share', 'comment', 'reply'))",
                                        $user_id, $user_id);

            // WHERE clause for notifications from other sources
            // like profile_pic_change, comment, reply and birthday.
            // Only applies to users with friends.
            $other_notifs_clause = sprintf("subject_id IN(%s) AND actor_id IN(%s) AND
                                            ((source_id IN(%s) AND activity IN('comment', 'reply')) OR
                                            activity IN('profile_pic_change','birthday'))",
                                            $friends_ids_str, $friends_ids_str, $acted_on_sql);

            // Query to get the latest notification from a group of activities
            // performed on the same object.
            $latest_notif_sql = sprintf("SELECT MAX(date_entered) FROM activities a2
                                            WHERE (((%s) OR (%s)) AND activity IN(%s)
                                                AND a1.subject_id = a2.subject_id AND
                                                a1.source_id = a2.source_id AND
                                                a1.source_type = a2.source_type AND
                                                a1.activity = a2.activity)",
                                            $primary_notifs_clause, $other_notifs_clause,
                                            $combined_notifs_str);

            // Query to get all notifications from activities.
            $notifs_sql = sprintf("SELECT * FROM activities a1
                                    WHERE (date_entered = (%s) OR (((%s) OR (%s)) AND
                                    activity IN(%s))) ORDER BY date_entered DESC
                                    LIMIT %d, %d",
                                    $latest_notif_sql, $primary_notifs_clause,
                                    $other_notifs_clause, $atomic_notifs_str,
                                    $offset, $limit);
        }

        /* Get IDs of items shared by this user (used for comments on items that this user shared) */
        /// Note: Although this looks similar to what's done in News_feed_model, they are quite different.
        ///       The difference lies in the IDs passed for `sharers_ids' parameter of `get_shared_items_ids()' method.
        /// IDs are got seperately b'se many items may share the same ID
        /// as they are stored in different tables.

        /// IDS of shared posts.
        $shared_posts_ids = $this->utility_model->get_shared_items_ids('post', [$user_id]);

        /// IDs of shared photos.
        $shared_photos_ids = $this->utility_model->get_shared_items_ids('photo', [$user_id]);

        /// IDs of shared videos.
        $shared_videos_ids = $this->utility_model->get_shared_items_ids('video', [$user_id]);

        /// IDs of shared links.
        $shared_links_ids = $this->utility_model->get_shared_items_ids('link', [$user_id]);

        $notifications = $this->utility_model->run_query($notifs_sql)->result_array();
        foreach ($notifications as &$n) {
            if ($n['activity'] == 'comment') {
                $n['from_shared'] = FALSE;
                switch ($n['source_type']) {
                case 'post':
                    if (in_array($n['source_id'], $shared_posts_ids)) {
                        $n['from_shared'] = TRUE;
                    }
                    break;
                case 'photo':
                    if (in_array($n['source_id'], $shared_photos_ids)) {
                        $n['from_shared'] = TRUE;
                    }
                    break;
                case 'video':
                    if (in_array($n['source_id'], $shared_videos_ids)) {
                        $n['from_shared'] = TRUE;
                    }
                    break;
                case 'link':
                    if (in_array($n['source_id'], $shared_links_ids)) {
                        $n['from_shared'] = TRUE;
                    }
                    break;
                default:
                    // do nothing...
                    break;
                }
            }

            if (in_array($n['activity'], $combined_notifs)) {
                // Get the number of times an activity was performed on the same object.
                if ($filter) {
                    $num_actors_sql = sprintf("SELECT DISTINCT actor_id FROM activities
                                                WHERE (source_id = %d AND source_type = '%s' AND
                                                activity = '%s' AND subject_id = %d AND
                                                activity_id != %d AND actor_id NOT IN(%d, %d) AND
                                                date_entered > (%s))",
                                                $n['source_id'], $n['source_type'], $n['activity'],
                                                $n['subject_id'], $n['activity_id'], $n['actor_id'],
                                                $user_id, $last_read_date_sql);
                }
                else {
                    $num_actors_sql = sprintf("SELECT DISTINCT actor_id FROM activities
                                                WHERE (source_id = %d AND source_type = '%s' AND
                                                activity = '%s' AND subject_id = %d AND
                                                activity_id != %d AND actor_id NOT IN(%d, %d))",
                                                $n['source_id'], $n['source_type'],
                                                $n['activity'], $n['subject_id'],
                                                $n['activity_id'], $n['actor_id'],
                                                $user_id);
                }
                $n['num_actors'] = $this->utility_model->run_query($num_actors_sql)->num_rows();
            }
        }
        unset($n);

        if (isset($birthdays_sql)) {
            $birthdays = $this->utility_model->run_query($birthdays_sql)->result_array();
            foreach ($birthdays as &$bd) {
                // Get the activity_id.
                $sql = sprintf("SELECT activity_id FROM activities
                                WHERE (actor_id = %d AND activity = 'birthday' AND
                                YEAR(date_entered) = %d)",
                                $bd['user_id'], date('Y'));
                $query = $this->utility_model->run_query($sql);

                if ($query->num_rows() == 0) {
                    // It hasn't been recorded in activities table, add it.
                    $activity_sql = sprintf("INSERT INTO activities
                                            (actor_id, subject_id, source_id, source_type, activity)
                                            VALUES (%d, %d, %d, 'user', 'birthday')",
                                            $bd['user_id'], $bd['user_id'], $bd['user_id']);
                    $this->utility_model->run_query($activity_sql);

                    $bd['activity_id'] = $this->db->insert_id();
                    $bd['activity'] = "birthday";
                    $bd['subject_id'] = $bd['user_id'];
                    $bd['actor_id'] = $bd['user_id'];
                    $bd['date_entered'] = date_format(date_create(), 'Y-m-d H:i:s');
                }
                else {
                    $activity_id = $query->row()->activity_id;

                    // Check whether this user has seen it before.
                    $seen_sql = sprintf("SELECT user_id FROM notification_read
                                        WHERE (user_id = %d AND activity_id = %d)",
                                        $user_id, $activity_id);
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
                $sql = sprintf("INSERT INTO notification_read (user_id, activity_id, date_read)
                                VALUES (%d, %d, '%s')",
                                $user_id, $r['activity_id'],
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
                $notif['subject_id'] != $user_id) {
                // Get the gender of the subject.
                $gender_sql = sprintf("SELECT gender FROM users WHERE user_id = %d LIMIT 1",
                                        $notif['subject_id']);
                $gender_query = $this->utility_model->run_query($gender_sql);
                $notif['subject_gender'] = ($gender_query->row_array()['gender'] == 'M')? 'his': 'her';
            }

            // If it is a like, or reply to a comment/reply,
            if (in_array($notif['activity'], array('like','reply')) &&
                in_array($notif['source_type'], array('comment','reply'))) {
                $comment_sql = sprintf("SELECT comment FROM comments WHERE (comment_id = %d) LIMIT 1",
                                        $notif['source_id']);
                $query = $this->utility_model->run_query($comment_sql);
                $comment = $query->row_array()['comment'];

                // get short comment.
                $notif['comment'] = character_limiter($comment, 25);
            }

            // Add other actors.
            if (in_array($notif['activity'], $combined_notifs)) {
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

    /**
     * Gets whether two users are friends or there exists a freind request
     * between them.
     *
     * @param $user_id ID of user to check for.
     * @return whether two users are friends or there exists a freind request
     * between them.
     */
    public function get_friendship_status($user_id, $other_id)
    {
        // Check whether the two users are already friends.
        $fr_sql = sprintf("SELECT id FROM friends
                            WHERE (user_id = %d AND friend_id = %d) OR
                                    (user_id = %d AND friend_id = %d)
                            LIMIT 1",
                            $user_id, $other_id,
                            $other_id, $user_id);
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
            $req_sql = sprintf("SELECT user_id, target_id FROM friend_requests
                                WHERE (user_id = %d AND target_id = %d) OR
                                        (user_id = %d AND target_id = %d)
                                LIMIT 1",
                                $user_id, $other_id,
                                $other_id, $user_id);
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

    /**
     * Gets users whom the current logged user might be knowing.
     *
     * @param $offset
     * @param $limit
     * @return users whom the current logged user might be knowing.
     */
    public function get_suggested_users($user_id, $offset, $limit)
    {
        $friends_ids = $this->get_friends_ids($user_id);
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        // Get users IDs from pending friend requests.
        $pending_fr_sql = sprintf("SELECT user_id, target_id FROM friend_requests
                                    WHERE (user_id = %d OR target_id = %d) AND
                                        confirmed IS FALSE
                                    LIMIT 1",
                                    $user_id, $user_id);
        $pending_fr_query = $this->utility_model->run_query($pending_fr_sql);
        $pending_fr_results = $pending_fr_query->result_array();

        $pending_fr_user_ids =  [0];
        foreach ($pending_fr_results as $pr) {
            if ($pr['user_id'] == $user_id)
                $pending_fr_user_ids[] = $pr['target_id'];
            else
                $pending_fr_user_ids[] = $pr['user_id'];
        }
        unset($pending_fr_sql, $pending_fr_query, $pending_fr_results);
        $pending_fr_user_ids = implode(',', $pending_fr_user_ids);

        $sql = sprintf("SELECT u.user_id, COUNT(*) FROM users AS u
                        LEFT JOIN friends AS f
                            ON (u.user_id = f.user_id OR u.user_id = f.friend_id)
                        WHERE u.user_id NOT IN (%s) AND u.user_id NOT IN(%s) AND u.user_id != %d AND
                            (f.friend_id IN(%s) OR f.user_id IN (%s))
                        GROUP BY u.user_id ORDER BY COUNT(*) DESC LIMIT %d, %d",
                        $friends_ids, $pending_fr_user_ids, $user_id,
                        $friends_ids, $friends_ids,
                        $offset, $limit);

        $users = $this->utility_model->run_query($sql)->result_array();
        foreach ($users as &$user) {
            $user['profile_pic_path'] = $this->get_profile_pic_path($user['user_id']);
            $user['profile_name'] = $this->get_profile_name($user['user_id']);
        }
        unset($user);

        return $users;
    }

    /**
     * Gets the number of friend requests sent to a user.
     *
     * @param $filter whether to count only friend request that haven't been
     * seen.
     * @return number of friend requests for the current logged in user.
     */
    public function get_num_friend_requests($user_id, $filter = TRUE)
    {
        if ($filter) {
            $req_sql = sprintf("SELECT COUNT(user_id) FROM friend_requests
                                WHERE (target_id = %d AND seen IS FALSE AND confirmed IS FALSE)",
                                $user_id);
        }
        else {
            $req_sql = sprintf("SELECT COUNT(user_id) FROM friend_requests
                                WHERE (target_id = %d AND confirmed IS FALSE)",
                                $user_id);
        }

        $req_query = $this->utility_model->run_query($req_sql);
        return $req_query->row_array()['COUNT(user_id)'];
    }

    /**
     * Gets friend requests sent to a user.
     *
     * @return friend requests for the current logged in user.
     */
    public function get_friend_requests($user_id, $offset, $limit)
    {
        $req_sql = sprintf("SELECT user_id, seen FROM friend_requests
                            WHERE (target_id = %d AND confirmed IS FALSE)
                            ORDER BY date_entered DESC LIMIT %d, %d",
                            $user_id, $offset, $limit);
        $req_query = $this->utility_model->run_query($req_sql);

        $friend_requests = $req_query->result_array();
        foreach ($friend_requests as &$fr) {
            if (!$fr['seen']) {
                $update_sql = sprintf("UPDATE friend_requests SET seen = 1
                                        WHERE (target_id = %d AND user_id = %d)",
                                        $user_id, $fr['user_id']);
                $this->utility_model->run_query($update_sql);
            }

            $fr['profile_name'] = $this->get_profile_name($fr['user_id']);
            $fr['profile_pic_path'] = $this->get_profile_pic_path($fr['user_id']);
        }
        unset($fr);

        return $friend_requests;
    }

    /**
     * Sends a friend request to a user.
     *
     * Throws IllegalAccessException if a user attempts to send a freind
     * request to a user with whom they are already friends or there exists
     * a pending friend request.
     *
     * @param $target_id ID of user to send request to.
     */
    public function send_friend_request($user_id, $target_id)
    {
        $friendship_status = $this->get_friendship_status($user_id, $target_id);
        if ($friendship_status['are_friends'] || $friendship_status['fr_sent']) {
            throw new IllegalAccessException(
                "Either the two of you are already friends, " .
                "or there exists a pending freind request between you."
            );
        }

        $fr_sql = sprintf("INSERT INTO friend_requests (user_id, target_id) VALUES (%d, %d)",
                            $user_id, $target_id);
        $this->utility_model->run_query($fr_sql);

        // Dispatch an activity.
        $request_id = $this->db->insert_id();
        $activity_sql = sprintf("INSERT INTO activities
                                (actor_id, subject_id, source_id, source_type, activity)
                                VALUES (%d, %d, %d, 'friend_request', 'friend_request')",
                                $user_id, $target_id, $request_id);
        $this->utility_model->run_query($activity_sql);
    }

    public function get_friend_request($user_id, $request_id)
    {
        $sql = sprintf("SELECT fr.*, u.profile_name FROM friend_requests fr
                        LEFT JOIN users u ON (fr.user_id = u.user_id)
                        WHERE request_id = %d AND target_id = %d",
                        $request_id, $user_id);
        $query = $this->utility_model->run_query($sql);
        if ($query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $request = $query->row_array();
        $request['profile_pic_path'] = $this->get_profile_pic_path($request['user_id']);
        return $request;
    }

    /**
     * Confirms a friend request sent to a user.
     *
     * Throws IllegalAccessException if a user attempts to confirm a
     * friend request that doesn't exist.
     *
     * @param $friend_id ID of the user who sent him a request.
     */
    public function confirm_friend_request($user_id, $friend_id)
    {
        // First check whether a friend request actually exist.
        $id_sql = sprintf("SELECT request_id FROM friend_requests
                            WHERE (target_id = %d AND user_id = %d AND confirmed IS FALSE)",
                            $user_id, $friend_id);
        $id_query = $this->utility_model->run_query($id_sql);
        if ($id_query->num_rows() == 0) {
            throw new IllegalAccessException(
                "This user didn't send you a friend request."
            );
        }

        // Add the user to the list of friends.
        $fr_sql = sprintf("INSERT INTO friends (user_id, friend_id) VALUES (%d, %d)",
                            $user_id, $friend_id);
        $this->utility_model->run_query($fr_sql);

        // Update the friend_requests table.
        $update_sql = sprintf("UPDATE friend_requests SET confirmed = 1
                                WHERE (user_id = %d AND target_id = %d)",
                                $friend_id, $user_id);
        $this->utility_model->run_query($update_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities
                                (actor_id, subject_id, source_id, source_type, activity)
                                VALUES (%d, %d, %d, 'user', 'confirmed_friend_request')",
                                $user_id, $friend_id, $user_id);
        $this->utility_model->run_query($activity_sql);
    }

    public function delete_friend_request($user_id, $requesting_user_id)
    {
        // Get friend request ID for deleting this activity from activities table.
        $request_id_sql = sprintf("SELECT request_id FROM friend_requests
                                    WHERE (user_id = %d AND target_id = %d) OR
                                    (user_id = %d AND target_id = %d) LIMIT 1",
                                    $user_id, $requesting_user_id,
                                    $requesting_user_id, $user_id);
        $request_id_query = $this->utility_model->run_query($request_id_sql);
        if ($request_id_query->num_rows() == 0) {
            throw new NotFoundException();
        }
        $request_id = $request_id_query->row_array()['request_id'];

        // Delete the activity.
        $activity_sql = sprintf("DELETE FROM activities
                                WHERE source_type = 'friend_request' AND source_id = %d
                                LIMIT 1",
                                $request_id);
        $this->utility_model->run_query($activity_sql);

        // Delete the friend request.
        $fr_sql = sprintf("DELETE from friend_requests
                            WHERE (user_id = %d AND target_id = %d) OR
                            (user_id = %d AND target_id = %d) LIMIT 1",
                            $user_id, $requesting_user_id,
                            $requesting_user_id, $user_id);
        $this->utility_model->run_query($fr_sql);
    }

    public function unfriend_user($user_id, $friend_id)
    {
        if ( ! $this->are_friends($user_id, $friend_id)) {
            throw new IllegalAccessException(
                "This user is not your friend."
            );
        }

        // Get friend request ID for deleting this activity from activities table.
        $request_id_sql = sprintf("SELECT request_id FROM friend_requests
                                    WHERE (user_id = %d AND target_id = %d) OR
                                    (user_id = %d AND target_id = %d) LIMIT 1",
                                    $user_id, $friend_id,
                                    $friend_id, $user_id);
        $request_id_query = $this->utility_model->run_query($request_id_sql);
        $request_id = $request_id_query->row_array()['request_id'];

        // Delete the activity.
        $activity_sql = sprintf("DELETE FROM activities
                                WHERE source_type = 'friend_request' AND source_id = %d
                                LIMIT 1",
                                $request_id);
        $this->utility_model->run_query($activity_sql);

        // Delete the friend request.
        $fr_sql = sprintf("DELETE from friend_requests
                            WHERE (user_id = %d AND target_id = %d) OR
                            (user_id = %d AND target_id = %d) LIMIT 1",
                            $user_id, $friend_id,
                            $friend_id, $user_id);
        $this->utility_model->run_query($fr_sql);

        // Un-friend the user.
        $unfr_sql = sprintf("DELETE FROM friends WHERE (user_id = %d AND friend_id = %d) OR
                            (user_id = %d AND friend_id = %d) LIMIT 1",
                            $user_id, $friend_id,
                            $friend_id, $user_id);
        $this->utility_model->run_query($unfr_sql);
    }

    /**
     * Sends a chat message to a user.
     *
     * @param $message the message to be sent.
     * @param $receiver_id ID the receiver
     */
    public function send_message($sender_id, $receiver_id, $message)
    {
        $message_sql = sprintf("INSERT INTO messages (sender_id, receiver_id, message)
                                VALUES (%d, %d, %s)",
                                $sender_id, $receiver_id,
                                $this->db->escape($message));
        $this->utility_model->run_query($message_sql);
    }

    /**
     * Gets the number of chat messages that exists between two users.
     *
     * @param $user_id ID of user whom this user was chatting with.
     * @return number of chat messages between these two users.
     */
    public function get_num_conversation($user_id, $other_id)
    {
        $sql = sprintf("SELECT COUNT(message_id) FROM messages
                        WHERE (receiver_id = %d AND sender_id = %d) OR
                                (receiver_id = %d AND sender_id = %d)",
                        $other_id, $user_id,
                        $user_id, $other_id);
        $query = $this->utility_model->run_query($sql);

        return $query->row_array()['COUNT(message_id)'];
    }

    /**
     * Gets chat messages that exists between two users.
     *
     * @param $user_id ID of user whom this user was chatting with.
     * @param $offset
     * @param $limit
     * @return chat messages between these two users.
     */
    public function get_conversation($user_id, $other_id, $offset, $limit)
    {
        $sql = sprintf("SELECT * FROM messages
                        WHERE (receiver_id = %d AND sender_id = %d) OR
                            (receiver_id = %d AND sender_id = %d)
                        ORDER BY date_sent DESC LIMIT %d, %d",
                        $other_id, $user_id,
                        $user_id, $other_id,
                        $offset, $limit);
        $query = $this->utility_model->run_query($sql);

        $messages = $query->result_array();
        $sender = $this->get_profile_name($user_id);
        $receiver = $this->get_profile_name($other_id);
        foreach ($messages as &$msg) {
            if ($msg['sender_id'] == $user_id) {
                $msg['sender'] = $sender;
            }
            else if ($msg['sender_id'] == $other_id) {
                $msg['sender'] = $receiver;
            }

            if (($msg['seen'] == 0) && ($msg['receiver_id'] == $user_id)) {
                $update_sql = sprintf("UPDATE messages SET seen = 1
                                        WHERE (message_id = %d) LIMIT 1",
                                        $msg['message_id']);
                $this->utility_model->run_query($update_sql);
            }

            // Add the timespan.
            $msg['timespan'] = timespan(mysql_to_unix($msg['date_sent']), now(), 1);
        }
        unset($msg);

        // Reverse the messages so that the latest messages are shown last.
        return array_reverse($messages);
    }
}
?>
