<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimpleVideo.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/NotFoundException.php');

/**
 * Contains functions related to a video.
 */
class Video_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'activity_model',
            'user_model', 'comment_model'
        ]);
    }

    /**
     * Gets a video plus other video metadata.
     *
     * Throws NotFoundException if the video cannot be found.
     *
     * @param $video_id the ID of the video in the videos table.
     * @return the video with the given ID.
     */
    public function get_video($video_id)
    {
        $video_sql = sprintf("SELECT v.*, u.profile_name AS author FROM videos v " .
                            "LEFT JOIN users u ON(v.user_id = u.user_id) " .
                            "WHERE video_id = %d",
                            $video_id);
        $video_query = $this->utility_model->run_query($video_sql);
        if ($video_query->num_rows() == 0) {
            throw new NotFoundException();
        }
        $video = $video_query->row_array();
        $video['profile_pic_path'] = $this->user_model->get_profile_pic_path($video['user_id']);
        $video['timespan'] = timespan(mysql_to_unix($video['date_entered']), now(), 1);

        // Add data used by views.
        $video['has_description'] = strlen($video['description']) != 0;
        $video['shared'] = FALSE;
        $video['alt'] = format_name($video['author']) . ' video';

        // Check whether the user currently viewing the page is a friend to the
        // owner of the video. This will allow us to only show the like, comment
        // and share buttons to friends of the owner.
        $video['viewer_is_friend_to_owner'] = $this->user_model->are_friends($video['user_id']);

        // Get number of liks, comments and shares.
        $simpleVideo = new SimpleVideo($video['video_id'], 'video', $video['user_id']);
        $video['num_likes'] = $this->activity_model->getNumLikes($simpleVideo);
        $video['num_comments'] = $this->activity_model->getNumComments($simpleVideo);
        $video['num_shares'] = $this->activity_model->getNumShares($simpleVideo);

        return $video;
    }

    /**
     * Allows a user to add a new video to his status.
     *
     * @param $url.
     * @return $video_id the ID of the video in the user_videos table.
     */
    public function publish($url)
    {
        // Record video data in the videos table.
        $video_sql = sprintf('INSERT INTO videos (user_id, url) VALUES (%d, %s) ',
                            $_SESSION['user_id'], $this->db->escape($url));
        $this->utility_model->run_query($video_sql);
        $video_id = $this->db->insert_id();

        // Dispatch an activity.
        $activity_sql = sprintf('INSERT INTO activities ' .
                                '(actor_id, subject_id, source_id, source_type, activity) ' .
                                'VALUES (%d, %d, %d, "video", "video")',
                                $_SESSION['user_id'], $_SESSION['user_id'], $video_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Adds a brief description about a video.
     *
     * Assumes that the video alreay exists in the user_videos table.
     *
     * @param $description the description entered by the user.
     * @param $video_id the id of the video in the user_videos table.
     */
    public function add_description($description, $video_id)
    {
        $sql = sprintf('UPDATE videos SET description = %s WHERE (video_id = %d)',
                        $this->db->escape($description), $video_id);
        $this->utility_model->run_query($sql);
    }

    /**
     * Records a like of a video.
     *
     * Throws NotFoundException exception if video is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a video published by a user who is not his friend.
     *
     * @param $video_id the ID of the video in the user_videos table.
     */
    public function like($video_id)
    {
        // Get the id of the owner of this video.
        $owner_sql = sprintf('SELECT user_id FROM videos WHERE video_id = %d', $video_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];

        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        $this->activity_model->like(new SimpleVideo($video_id, 'video', $owner_id));
    }

    /**
     * Shares a video on a user's timeline.
     *
     * Throws NotFoundException if a video is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a video that was published by a user who is not his friend.
     *
     * @param $video_id the ID of the video in the user_videos table.
     */
    public function share($video_id)
    {
        $owner_sql = sprintf('SELECT user_id FROM videos WHERE video_id = %d', $video_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];
        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException();
        }

        $this->activity_model->share(new SimpleVideo($video_id, 'video', $owner_id));
    }

    /**
     * Records a comment on a video.
     *
     * @param $video_id the ID of the video in the user_videos table.
     * @param $comment the comment a user made.
     */
    public function comment($video_id, $comment)
    {
        // Get the ID of the owner of this video.
        $owner_sql = sprintf('SELECT user_id FROM videos WHERE video_id = %d', $video_id);
        $owner_result = $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['user_id'];

        // Record the comment.
        $this->activity_model->comment(
            new SimpleVideo($video_id, 'video', $owner_id),
            $comment
        );
    }

    /**
     * Get users who liked a video.
     *
     * @param $video an array containing video data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this video.
     */
    public function get_likes(&$video, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimpleVideo($video['video_id'], 'video', $video['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the users who shared a video.
     *
     * @param $video an array containing video data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who shared this video.
     */
    public function get_shares(&$video, $offset, $limit)
    {
        return $this->activity_model->getShares(
            new SimpleVideo($video['video_id'], 'video', $video['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the comments made on a video.
     *
     * @param $video an array containing video data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the comments made on this video.
     */
    public function get_comments(&$video, $offset, $limit)
    {
        return $this->activity_model->getComments(
            new SimpleVideo($video['video_id'], 'video', $video['user_id']),
            $offset,
            $limit
        );
    }
}
?>
