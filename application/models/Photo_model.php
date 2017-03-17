<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('exceptions/PhotoNotFoundException.php');
require_once('exceptions/IllegalAccessException.php');

/**
 * Contains functions related to a photo.
 */
class Photo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model', 'user_model', 'comment_model']);
    }

    /**
     * Gets a photo plus other photo metadata.
     *
     * Throws PhotoNotFoundException if the photo cannot be found.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @return the photo with the given ID.
     */
    public function get_photo($photo_id)
    {
        // Get the photo.
        $photo_sql = sprintf("SELECT * FROM user_photos WHERE photo_id = %d",
                     $photo_id);
        $photo_query = $this->utility_model->run_query($photo_sql);
        if ($photo_query->num_rows() == 0) {
            throw new PhotoNotFoundException();
        }

        $photo = $photo_query->row_array();
        $web_path = str_replace("{$_SERVER['DOCUMENT_ROOT']}makwire", '', $photo['full_path']);
        $photo['web_path'] = base_url("{$web_path}");

        // Get the name of the author.
        $photo['author'] = $this->user_model->get_profile_name($photo['user_id']);

        // Get the profile picture of the user.
        $photo['profile_pic_path'] = $this->user_model->get_profile_pic_path($photo['user_id']);

        // Get the number of likes.
        $photo['num_likes'] = $this->get_num_likes($photo_id);

        // Get the number of comments.
        $photo['num_comments'] = $this->get_num_comments($photo_id);

        // Get the number of shares.
        $photo['num_shares'] = $this->get_num_shares($photo_id);

        // Get the timespan.
        $photo['timespan'] = timespan(mysql_to_unix($photo['date_entered']), now(), 1);

        // Add data used by views.
        $photo['shared'] = FALSE;
        $photo['alt'] = format_name($photo['author']) . ' photo';

        // Check whether the user currently viewing the page is a friend to the
        // owner of the photo. This will allow us to only show the
        // like, comment and share buttons to friends of the owner.
        $photo['viewer_is_friend_to_owner'] = $this->user_model->are_friends($photo['user_id']);

        // Check if photo was used as a profile picture.
        $is_profile_pic_sql = sprintf("SELECT activity_id FROM activities " .
                                        "WHERE (source_id = %d AND source_type = 'photo' AND activity = 'profile_pic_change')",
                                        $photo['photo_id']);
        $photo['is_profile_pic'] = ($this->utility_model->run_query($is_profile_pic_sql)->num_rows() == 1);
        if ($photo['is_profile_pic']) {
            // Get the gender of this user.
            $gender_sql = sprintf("SELECT gender FROM users WHERE (user_id = %d)",
                                   $photo['user_id']);
            $photo['user_gender'] = ($this->utility_model->run_query($gender_sql)->row_array()['gender'] == 'M')? 'his': 'her';
        }

        return $photo;
    }

    /**
     * Checks whether a user has already liked a photo.
     *
     * A user is not allowed to like his own photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @return TRUE if this user has already liked the photo, or is the owner of the photo.
     */
    private function has_liked($photo_id)
    {
        // Check whether this photo belongs to the current user.
        $user_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already liked the photo.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = 'photo' AND liker_id = %d) " .
                            "LIMIT 1",
                            $photo_id, $_SESSION['user_id']);
        return ($this->utility_model->run_query($like_sql)->num_rows() == 1);
    }

    /**
     * Checks whether a user has already shared a photo.
     *
     * Until groups are implemented, a user is not allowed to share his own photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @return TRUE this user has already shared the photo, or is the owner of the photo.
     */
    private function has_shared($photo_id)
    {
        // Check whether this photo belongs to the current user.
        $user_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d LIMIT 1",
                            $photo_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already shared the photo.
        $share_sql = sprintf("SELECT share_id FROM shares " .
                                "WHERE (subject_id = %d AND user_id = %d AND subject_type = 'photo') " .
                                "LIMIT 1",
                                $photo_id, $_SESSION['user_id']);
        return ($this->utility_model->run_query($share_sql)->num_rows() == 1);
    }

    /**
     * Gets the number of users who liked a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @return the number of likes on this photo.
     */
    public function get_num_likes($photo_id)
    {
        $likes_sql = sprintf("SELECT COUNT(like_id) FROM likes " .
                                "WHERE (source_id = %d AND source_type = 'photo')",
                                $photo_id);
        return $this->utility_model->run_query($likes_sql)->row_array()['COUNT(like_id)'];
    }

    /**
     * Get the number of comments made on a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @return the number of comments on this photo.
     */
    public function get_num_comments($photo_id)
    {
        $comments_sql = sprintf("SELECT COUNT(comment_id) FROM comments " .
                                "WHERE (source_type = 'photo' AND source_id = %d AND parent_id = 0)",
                                $photo_id);
        return $this->utility_model->run_query($comments_sql)->row_array()['COUNT(comment_id)'];
    }

    /**
     * Gets the number of users who shared a photo.
     *
     * @param photo_id the ID of the photo in the user_photos table.
     * @return the number of users who shared this photo.
     */
    public function get_num_shares($photo_id)
    {
        $shares_sql = sprintf("SELECT COUNT(share_id) FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = 'photo')",
                                $photo_id);
        return $this->utility_model->run_query($shares_sql)->row_array()['COUNT(share_id)'];
    }

    /**
     * Allows a user to add a new photo on his status.
     *
     * @param $data an array containing details about the uploaded photo.
     */
    public function publish($data)
    {
    }

    /**
     * Records a like of a photo.
     *
     * Throws PhotoNotFoundException exception if photo is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a photo published by a user who is not his friend.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     */
    public function like($photo_id)
    {
        // Get the id of the owner of this photo.
        $user_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            throw new PhotoNotFoundException();
        }

        if ($this->has_liked($photo_id)) {
            return;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['user_id'])) {
            throw new IllegalAccessException();
        }

        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, 'photo')",
                            $_SESSION['user_id'], $photo_id);
        $this->utility_model->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'photo', 'like')",
                                $_SESSION['user_id'], $user_result['user_id'], $photo_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Records a comment on a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @param $comment the comment a user made.
     */
    public function comment($photo_id, $comment)
    {
        // Record the comment.
        $comment_sql = sprintf("INSERT INTO comments " .
                                "(commenter_id, parent_id, source_id, source_type, comment) " .
                                "VALUES (%d, %d, %d, 'photo', %s)",
                                $_SESSION['user_id'], 0, $photo_id, $this->db->escape($comment));
        $this->utility_model->run_query($comment_sql);

        // Get the ID of the owner of this photo.
        $user_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $user_result = $this->utility_model->run_query($user_sql)->row_array();

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'photo', 'comment')",
                                $_SESSION['user_id'], $user_result['user_id'], $photo_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Shares a photo on a user's timeline.
     *
     * Throws PhotoNotFoundException if a photo is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a photo that was published by a user who is not his friend.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     */
    public function share($photo_id)
    {
        $user_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $user_query = $this->utility_model->run_query($user_sql);
        if ($user_query->num_rows() == 0) {
            throw new PhotoNotFoundException();

        }

        if ($this->has_shared($photo_id)) {
            return;
        }

        $user_result = $user_query->row_array();
        if (!$this->user_model->are_friends($user_result['user_id'])) {
            throw new IllegalAccessException();
        }

        // Insert it into the shares table.
        $share_sql = sprintf("INSERT INTO shares (subject_id, user_id, subject_type) " .
                                "VALUES (%d, %d, 'photo')",
                                $photo_id, $_SESSION['user_id']);
        $this->utility_model->run_query($share_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'photo', 'share')",
                                $_SESSION['user_id'], $user_result['user_id'], $photo_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Get users who liked a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this photo.
     */
    public function get_likes($photo_id, $offset, $limit)
    {
        $likes_sql = sprintf("SELECT * FROM likes " .
                                "WHERE (source_type = 'photo' AND source_id = %d) " .
                                "LIMIT %d, %d",
                                $photo_id, $offset, $limit);
        $likes_query = $this->utility_model->run_query($likes_sql);

        $likes = $likes_query->result_array();
        foreach ($likes as &$like) {
            $like['profile_pic_path'] = $this->user_model->get_profile_pic_path($like['liker_id']);
            $like['liker'] = $this->user_model->get_profile_name($like['liker_id']);
            $like['timespan'] = timespan(mysql_to_unix($like['date_liked']), now(), 1);
        }
        unset($like);

        return $likes;
    }

    /**
     * Gets the comments made on a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the comments made on this photo.
     */
    public function get_comments($photo_id, $offset, $limit)
    {
        $comments_sql = sprintf("SELECT comment_id FROM comments " .
                                "WHERE (source_type = 'photo' AND source_id = %d AND parent_id = 0) " .
                                "LIMIT %d, %d",
                                $photo_id, $offset, $limit);
        $comments_query = $this->utility_model->run_query($comments_sql);
        $results = $comments_query->result_array();

        $comments = array();
        foreach ($results as $r) {
            // Get the detailed comment.
            $comment = $this->comment_model->get_comment($r['comment_id']);
            array_push($comments, $comment);
        }

        return $comments;
    }

    /**
     * Gets the users who shared a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who shared this photo.
     */
    public function get_shares($photo_id, $offset, $limit)
    {
        $shares_sql = sprintf("SELECT user_id AS sharer_id, date_shared FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = 'photo') " .
                                "LIMIT %d, %d",
                                $photo_id, $offset, $limit);
        $shares_query = $this->utility_model->run_query($shares_sql);

        $shares = $shares_query->result_array();
        foreach ($shares as &$share) {
            $share['profile_pic_path'] = $this->user_model->get_profile_pic_path($share['sharer_id']);
            $share['sharer'] = $this->user_model->get_profile_name($share['sharer_id']);
            $share['timespan'] = timespan(mysql_to_unix($share['date_shared']), now(), 1);
        }
        unset($share);

        return $shares;
    }
}
?>
