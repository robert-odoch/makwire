<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Photo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->model("comment_model");
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
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function get_photo($photo_id)
    {
        // Does this user have the proper permissions to view this file?
        if (!$this->user_can_access_photo($photo_id)) {
            return FALSE;
        }

        // Get the photo.
        $q = sprintf("SELECT user_id AS author_id, user_photos.* FROM user_photos WHERE (photo_id=%d)",
                     $photo_id);
        $query = $this->run_query($q);
        $image = $query->row_array();

        // Get the full path of the profile picture.
        $q = sprintf("SELECT full_path FROM user_photos WHERE photo_id=%d",
                     $image['photo_id']);
        $query = $this->run_query($q);
        $web_path = $query->row_array()['full_path'];
        $image['web_path'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $web_path);

        // Get the name of the author.
        $image['author'] = $this->user_model->get_name($image['user_id']);

        // Get the number of likes.
        $image['num_likes'] = $this->get_num_likes($photo_id);

        // Get the number of comments.
        $image['num_comments'] = $this->get_num_comments($photo_id);

        // Get the number of shares.
        $image['num_shares'] = $this->get_num_shares($photo_id);

        // Get the timespan.
        $unix_timestamp = mysql_to_unix($image['date_entered']);
        $image['timespan'] = timespan($unix_timestamp, now(), 1);

        return $image;
    }

    private function has_liked($photo_id)
    {
        // Check whether this photo belongs to the current user.
        $q = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                     $photo_id);
        $query = $this->run_query($q);
        if ($query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already liked the photo.
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='photo' AND liker_id=%d) " .
                     "LIMIT 1",
                     $photo_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    private function has_shared($photo_id)
    {
        // Check whether this photo belongs to the current user.
        $q = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d LIMIT 1",
                     $photo_id);
        $query = $this->run_query($q);
        if ($query->row_array()['user_id'] == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already shared the photo.
        $q = sprintf("SELECT share_id FROM shares " .
                     "WHERE (subject_id = %d AND user_id = %d AND subject_type='photo') LIMIT 1",
                     $photo_id, $_SESSION['user_id']);
        return ($this->run_query($q)->num_rows() == 1);
    }

    // Checks whether a user has the proper permision to access a given photo
    private function user_can_access_photo($photo_id)
    {
        // Get the id of the user who posted this photo.
        $q = sprintf("SELECT user_id FROM user_photos WHERE photo_id=%d",
                     $photo_id);
        $query = $this->run_query($q);
        if ($query->num_rows() == 0) {
            // No user (even admin) has permission to access a file that doesn't exist.
            return FALSE;
        }

        $author_id = $query->row()->user_id;
        return $this->user_model->are_friends($author_id);
    }

    public function get_num_likes($photo_id)
    {
        $q = sprintf("SELECT like_id FROM likes " .
                     "WHERE (source_id=%d AND source_type='photo')",
                     $photo_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_num_comments($photo_id)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type='photo' AND source_id=%d AND parent_id=0)",
                     $photo_id);
        return $this->run_query($q)->num_rows();
    }

    public function get_num_shares($photo_id)
    {
        $q = sprintf("SELECT share_id FROM shares WHERE (subject_id = %d AND subject_type = 'photo')",
                     $photo_id);
        return $this->run_query($q)->num_rows();
    }

    public function post($data)
    {
    }

    public function like($photo_id)
    {
        if (!$this->user_can_access_photo($photo_id)) {
            return FALSE;
        }
        if ($this->has_liked($photo_id)) {
            return TRUE;
        }

        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, 'photo')",
                     $_SESSION['user_id'], $photo_id);
        $this->run_query($q);

        // Get the id of the user who posted.
        $q = sprintf("SELECT user_id FROM user_photos WHERE photo_id=%d LIMIT 1",
                     $photo_id);
        $query = $this->run_query($q);

        $subject_id = $query->row()->user_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'photo', 'like')",
                     $_SESSION['user_id'], $subject_id, $photo_id);
        $this->run_query($q);

        return TRUE;
    }

    public function comment($photo_id, $comment)
    {
        if (!$this->user_can_access_photo($photo_id)) {
            return FALSE;
        }

        // Record the comment.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, 'photo', %s)",
                     $_SESSION['user_id'], 0, $photo_id, $this->db->escape($comment));
        $this->run_query($q);

        // Get the parent_id.
        $q = sprintf("SELECT user_id FROM user_photos WHERE photo_id=%d LIMIT 1",
                     $photo_id);
        $query = $this->run_query($q);

        $subject_id = $query->row()->user_id;

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'photo', 'comment')",
                     $_SESSION['user_id'], $subject_id, $photo_id);
        $this->run_query($q);

        return TRUE;
    }

    public function share($photo_id)
    {
        if (!$this->user_can_access_photo($photo_id)) {
            return FALSE;
        }
        if ($this->has_shared($photo_id)) {
            return TRUE;
        }

        $photo_q = sprintf("SELECT user_id, audience FROM user_photos WHERE photo_id=%d",
                            $photo_id);
        $photo_result = $this->run_query($photo_q)->row_array();
        if ($photo_result['audience'] == 'group') {
            return FALSE;  // Group photos can't be shared outside the group.
        }

        // Insert it into the shares table.
        $q = sprintf("INSERT INTO shares (subject_id, user_id, subject_type) " .
                     "VALUES (%d, %d, 'photo')",
                     $photo_id, $_SESSION['user_id']);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'photo', 'share')",
                     $_SESSION['user_id'], $photo_result['user_id'], $photo_id);
        $this->run_query($q);

        return TRUE;
    }

    public function get_likes($photo_id, $offset, $limit)
    {
        $q = sprintf("SELECT * FROM likes " .
                     "WHERE (source_type='photo' AND source_id=%d) " .
                     "LIMIT %d, %d",
                     $photo_id, $offset, $limit);
        $query = $this->run_query($q);

        $likes = $query->result_array();
        foreach ($likes as &$like) {
            // Get the name of the user who liked.
            $like['liker'] = $this->user_model->get_name($like['liker_id']);
            $like['profile_pic_path'] = $this->user_model->get_profile_picture($like['liker_id']);
        }
        unset($like);

        return $likes;
    }

    public function get_comments($photo_id, $offset, $limit)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type='photo' AND source_id=%d AND parent_id=0) " .
                     "LIMIT %d, %d",
                     $photo_id, $offset, $limit);
        $query = $this->run_query($q);
        $results = $query->result_array();

        $comments = array();
        foreach ($results as $r) {
            // Get the detailed comment.
            $comment = $this->comment_model->get_comment($r['comment_id']);
            array_push($comments, $comment);
        }

        return $comments;
    }

    public function get_shares($photo_id, $offset, $limit)
    {
        $q = sprintf("SELECT user_id AS sharer_id FROM shares " .
                     "WHERE (subject_id = %d AND subject_type = 'photo') " .
                     "LIMIT %d, %d",
                     $photo_id, $offset, $limit);
        $query = $this->run_query($q);

        $shares = $query->result_array();
        foreach ($shares as &$share) {
            // Get the name of the user who shared.
            $share['sharer'] = $this->user_model->get_name($share['sharer_id']);
            $share['profile_pic_path'] = $this->user_model->get_profile_picture($share['sharer_id']);
        }
        unset($share);

        return $shares;
    }
}
?>
