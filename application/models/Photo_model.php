<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimplePhoto.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/NotFoundException.php');

/**
 * Contains functions related to a photo.
 */
class Photo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'activity_model', 'user_model', 'comment_model'
        ]);
    }

    /**
     * Gets a photo plus other photo metadata.
     *
     * Throws NotFoundException if the photo cannot be found.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @return the photo with the given ID.
     */
    public function get_photo($photo_id)
    {
        $photo_sql = sprintf("SELECT p.*, u.profile_name AS author FROM user_photos p " .
                            "LEFT JOIN users u ON(p.user_id = u.user_id) " .
                            "WHERE photo_id = %d",
                            $photo_id);
        $photo_query = $this->utility_model->run_query($photo_sql);
        if ($photo_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $photo = $photo_query->row_array();

        // Add web path.
        $web_path = str_replace("{$_SERVER['DOCUMENT_ROOT']}makwire", '', $photo['full_path']);
        $photo['web_path'] = base_url($web_path);

        // Add profile picture of the user.
        $photo['profile_pic_path'] = $this->user_model->get_profile_pic_path($photo['user_id']);

        // Add timespan.
        $photo['timespan'] = timespan(mysql_to_unix($photo['date_entered']), now(), 1);

        // Add data used by views.
        $photo['has_description'] = strlen($photo['description']) != 0;
        $photo['shared'] = FALSE;
        $photo['alt'] = format_name($photo['author']) . ' photo';

        // Check whether the user currently viewing the page is a friend to the
        // owner of the photo. This will allow us to only show the like, comment
        // and share buttons to friends of the owner.
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

        $simplePhoto = new SimplePhoto($photo['photo_id'], 'photo', $photo['user_id']);

        // Get the number of likes.
        $photo['num_likes'] = $this->activity_model->getNumLikes($simplePhoto);

        // Get the number of comments.
        $photo['num_comments'] = $this->activity_model->getNumComments($simplePhoto);

        // Get the number of shares.
        $photo['num_shares'] = $this->activity_model->getNumShares($simplePhoto);

        return $photo;
    }

    /**
     * Allows a user to add a new photo to his status.
     *
     * @param $data an array containing details about the uploaded photo.
     * @param $audience the target audience for the photo. May be timeline or group.
     * @param $audience_id the ID of the target audience. Same as user ID if
     * audience is timeline.
     * @return $photo_id the ID of the photo in the user_photos table.
     */
    public function publish($data, $audience, $audience_id)
    {
        // Record photo data in the photos table.
        $photo_sql = sprintf("INSERT INTO user_photos " .
                            "(user_id, image_type, full_path) " .
                            "VALUES (%d, %s, %s)",
                            $_SESSION['user_id'],
                            $this->db->escape($data['file_type']),
                            $this->db->escape($data['full_path']));
        $this->utility_model->run_query($photo_sql);
        $photo_id = $this->db->insert_id();

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'photo', 'photo')",
                                $_SESSION['user_id'], $_SESSION['user_id'], $photo_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Adds a brief description about a photo.
     *
     * Assumes that the photo alreay exists in the user_photos table.
     *
     * @param $description the description entered by the user.
     * @param $photo_id the id of the photo in the user_photos table.
     */
    public function add_description($description, $photo_id)
    {
        $sql = sprintf("UPDATE user_photos SET description = %s " .
                        "WHERE (photo_id = %d)",
                        $this->db->escape($description), $photo_id);
        $this->utility_model->run_query($sql);
    }

    /**
     * Records a like of a photo.
     *
     * Throws NotFoundException exception if photo is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a photo published by a user who is not his friend.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     */
    public function like($photo_id)
    {
        // Get the id of the owner of this photo.
        $owner_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];

        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        $this->activity_model->like(new SimplePhoto($photo_id, 'photo', $owner_id));
    }

    /**
     * Shares a photo on a user's timeline.
     *
     * Throws NotFoundException if a photo is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a photo that was published by a user who is not his friend.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     */
    public function share($photo_id)
    {
        $owner_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();

        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];
        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        $this->activity_model->share(new SimplePhoto($photo_id, 'photo', $owner_id));
    }

    /**
     * Records a comment on a photo.
     *
     * @param $photo_id the ID of the photo in the user_photos table.
     * @param $comment the comment a user made.
     */
    public function comment($photo_id, $comment)
    {
        // Get the ID of the owner of this photo.
        $owner_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $owner_result = $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['user_id'];

        // Record the comment.
        $this->activity_model->comment(
            new SimplePhoto($photo_id, 'photo', $owner_id),
            $comment
        );
    }

    /**
     * Get users who liked a photo.
     *
     * @param $photo an array containing photo data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this photo.
     */
    public function get_likes(&$photo, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimplePhoto($photo['photo_id'], 'photo', $photo['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the users who shared a photo.
     *
     * @param $photo an array containing photo data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who shared this photo.
     */
    public function get_shares(&$photo, $offset, $limit)
    {
        return $this->activity_model->getShares(
            new SimplePhoto($photo['photo_id'], 'photo', $photo['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the comments made on a photo.
     *
     * @param $photo an array containing photo data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the comments made on this photo.
     */
    public function get_comments(&$photo, $offset, $limit)
    {
        return $this->activity_model->getComments(
            new SimplePhoto($photo['photo_id'], 'photo', $photo['user_id']),
            $offset,
            $limit
        );
    }

    public function delete_photo(&$photo)
    {
        $simplePhoto = new SimplePhoto($photo['photo_id'], 'photo', $photo['user_id']);
        $this->utility_model->delete_item($simplePhoto);
    }
}
?>
