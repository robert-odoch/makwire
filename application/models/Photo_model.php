<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

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
     * @param $photo_id the ID of the photo in the photos table.
     * @return the photo with the given ID.
     */
    public function get_photo($photo_id, $visitor_id)
    {
        $photo_sql = sprintf("SELECT p.*, u.profile_name AS author FROM photos p
                                LEFT JOIN users u ON(p.user_id = u.user_id)
                                WHERE photo_id = %d",
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
        $photo['viewer_is_friend_to_owner'] = $this->user_model->are_friends($visitor_id, $photo['user_id']);

        // Check if photo was used as a profile picture.
        $was_profile_pic_sql = sprintf("SELECT activity_id FROM activities
                                        WHERE (source_id = %d AND source_type = 'photo' AND activity = 'profile_pic_change')",
                                        $photo['photo_id']);
        $was_profile_pic_query = $this->utility_model->run_query($was_profile_pic_sql);
        $photo['was_profile_pic'] = ($was_profile_pic_query->num_rows() == 1);

        // Check if photo is the current profile picture.
        $photo['is_curr_profile_pic'] = FALSE;
        if ($photo['was_profile_pic']) {
            $is_curr_profile_pic_sql = sprintf("SELECT source_id FROM activities
                                                WHERE (actor_id = %d AND source_type = 'photo' AND activity = 'profile_pic_change')
                                                ORDER BY date_entered DESC LIMIT 1",
                                                $photo['user_id']);
            $is_curr_profile_pic_query = $this->db->query($is_curr_profile_pic_sql);
            if ($is_curr_profile_pic_query->num_rows() == 1) {
                $result = $is_curr_profile_pic_query->row_array();
                $photo['is_curr_profile_pic'] = ($result['source_id'] == $photo['photo_id']);
            }
        }

        if ($photo['was_profile_pic']) {
            // Get the gender of this user.
            $gender_sql = sprintf("SELECT gender FROM users WHERE (user_id = %d)",
                                   $photo['user_id']);
            $photo['user_gender'] = ($this->utility_model->run_query($gender_sql)->row_array()['gender'] == 'M')? 'his': 'her';
        }

        $simplePhoto = new SimplePhoto($photo['photo_id'], $photo['user_id']);

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
     */
    public function publish($data, $user_id)
    {
        $photo_id = $this->add_photo($data, $user_id);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities
                                (actor_id, subject_id, source_id, source_type, activity)
                                VALUES (%d, %d, %d, 'photo', 'photo')",
                                $user_id, $user_id, $photo_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Adds a brief description about a photo.
     *
     * Assumes that the photo alreay exists in the photos table.
     *
     * @param $description the description entered by the user.
     * @param $photo_id the id of the photo in the photos table.
     */
    public function add_description($description, $photo_id)
    {
        $sql = sprintf("UPDATE photos SET description = %s WHERE (photo_id = %d)",
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
     * @param $photo_id the ID of the photo in the photos table.
     */
    public function like($photo_id, $user_id)
    {
        // Get the id of the owner of this photo.
        $owner_sql = sprintf("SELECT user_id FROM photos WHERE photo_id = %d",
                            $photo_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];

        if (!$this->user_model->are_friends($user_id, $owner_id)) {
            throw new IllegalAccessException(
                "You don't have the proper permissions to like this photo."
            );
        }

        $this->activity_model->like(
            new SimplePhoto($photo_id, $owner_id),
            $user_id
        );
    }

    /**
     * Shares a photo on a user's timeline.
     *
     * Throws NotFoundException if a photo is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a photo that was published by a user who is not his friend.
     *
     * @param $photo_id the ID of the photo in the photos table.
     */
    public function share($photo_id, $user_id)
    {
        $owner_sql = sprintf("SELECT user_id FROM photos WHERE photo_id = %d",
                            $photo_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();

        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];
        if (!$this->user_model->are_friends($user_id, $owner_id)) {
            throw new IllegalAccessException(
                "You don't have the proper permissions to share this photo."
            );
        }

        $this->activity_model->share(
            new SimplePhoto($photo_id, $owner_id),
            $user_id
        );
    }

    /**
     * Records a comment on a photo.
     *
     * @param $photo_id the ID of the photo in the photos table.
     * @param $comment the comment a user made.
     */
    public function comment($photo_id, $comment, $user_id)
    {
        // Get the ID of the owner of this photo.
        $owner_sql = sprintf("SELECT user_id FROM photos WHERE photo_id = %d",
                            $photo_id);
        $owner_result = $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['user_id'];

        // Record the comment.
        $this->activity_model->comment(
            new SimplePhoto($photo_id, $owner_id),
            $comment,
            $user_id
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
            new SimplePhoto($photo['photo_id'], $photo['user_id']),
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
            new SimplePhoto($photo['photo_id'], $photo['user_id']),
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
    public function get_comments(&$photo, $offset, $limit, $visitor_id)
    {
        return $this->activity_model->getComments(
            new SimplePhoto($photo['photo_id'], $photo['user_id']),
            $offset,
            $limit,
            $visitor_id
        );
    }

    public function delete_photo(&$photo, $user_id)
    {
        $simplePhoto = new SimplePhoto($photo['photo_id'], $photo['user_id']);
        $this->utility_model->delete_item($simplePhoto, $user_id);
    }

    public function update_description($photo_id, $new_description)
    {
        $sql = sprintf('UPDATE photos SET description = %s WHERE photo_id = %d',
                        $this->db->escape($new_description), $photo_id);
        $this->db->query($sql);
    }

    public function add_photo($data, $user_id)
    {
        $photo_sql = sprintf("INSERT INTO photos (user_id, file_type, full_path) VALUES (%d, %s, %s)",
                            $user_id, $this->db->escape($data['file_type']),
                            $this->db->escape($data['full_path']));
        $this->utility_model->run_query($photo_sql);
        $photo_id = $this->db->insert_id();

        return $photo_id;
    }
}
?>
