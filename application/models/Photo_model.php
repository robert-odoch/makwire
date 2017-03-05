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
        // Get the photo.
        $photo_sql = sprintf("SELECT * FROM user_photos WHERE photo_id = %d",
                     $photo_id);
        $photo_query = $this->run_query($photo_sql);
        if ($photo_query->num_rows() == 0) {
            return FALSE;
        }

        $photo = $photo_query->row_array();
        $web_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $photo['full_path']);
        $photo['web_path'] = base_url("/{$web_path}");

        // Get the name of the author.
        $photo['author'] = $this->user_model->get_profile_name($photo['user_id']);

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

        if ($this->user_model->name_ends_with('s', $photo['author'])) {
            $photo['author_name_ends_with_s'] = TRUE;
            $photo['alt'] = "{$photo['author']}' photo";
        }
        else {
            $photo['author_name_ends_with_s'] = FALSE;
            $photo['alt'] = "{$photo['author']}'s photo";
        }

        // Check whether the user currently viewing the page is a friend to the
        // owner of the photo. This will allow us to only show the
        // like, comment and share buttons to friends of the owner.
        $photo['viewer_is_friend_to_owner'] = $this->user_model->are_friends($photo['user_id']);

        // Check if photo was used as a profile picture.
        $is_profile_pic_sql = sprintf("SELECT activity_id FROM activities " .
                                        "WHERE (source_id = %d AND source_type = 'photo' AND activity = 'profile_pic_change')",
                                        $photo['photo_id']);
        $photo['is_profile_pic'] = ($this->run_query($is_profile_pic_sql)->num_rows() == 1);
        if ($photo['is_profile_pic']) {
            // Get the gender of this user.
            $gender_sql = sprintf("SELECT gender FROM users WHERE (user_id = %d)",
                                   $photo['user_id']);
            $photo['user_gender'] = ($this->run_query($gender_sql)->row_array()['gender'] == 'M')? 'his': 'her';
        }

        return $photo;
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
        // Get the id of the owner of this photo.
        $photo_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $photo_query = $this->run_query($photo_sql);
        if ($photo_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_liked($photo_id)) {
            return TRUE;
        }

        $photo_result = $photo_query->row_array();
        if (!$this->user_model->are_friends($photo_result['user_id'])) {
            return FALSE;
        }

        $q = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                     "VALUES (%d, %d, 'photo')",
                     $_SESSION['user_id'], $photo_id);
        $this->run_query($q);

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'photo', 'like')",
                     $_SESSION['user_id'], $photo_result['user_id'], $photo_id);
        $this->run_query($q);

        return TRUE;
    }

    public function comment($photo_id, $comment)
    {
        // Record the comment.
        $q = sprintf("INSERT INTO comments (commenter_id, parent_id, source_id, source_type, comment) " .
                     "VALUES (%d, %d, %d, 'photo', %s)",
                     $_SESSION['user_id'], 0, $photo_id, $this->db->escape($comment));
        $this->run_query($q);

        // Get the ID of the owner of this photo.
        $photo_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $photo_result = $this->run_query($photo_sql)->row_array();

        // Dispatch an activity.
        $q = sprintf("INSERT INTO activities (actor_id, subject_id, source_id, source_type, activity) " .
                     "VALUES (%d, %d, %d, 'photo', 'comment')",
                     $_SESSION['user_id'], $photo_result['user_id'], $photo_id);
        $this->run_query($q);
    }

    public function share($photo_id)
    {
        $photo_sql = sprintf("SELECT user_id FROM user_photos WHERE photo_id = %d",
                            $photo_id);
        $photo_query = $this->run_query($photo_sql);
        if ($photo_query->num_rows() == 0) {
            return FALSE;
        }

        if ($this->has_shared($photo_id)) {
            return TRUE;
        }

        $photo_result = $photo_query->row_array();
        if (!$this->user_model->are_friends($photo_result['user_id'])) {
            return FALSE;
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
            $like['liker'] = $this->user_model->get_profile_name($like['liker_id']);
            $like['profile_pic_path'] = $this->user_model->get_profile_pic_path($like['liker_id']);
        }
        unset($like);

        return $likes;
    }

    public function get_comments($photo_id, $offset, $limit)
    {
        $q = sprintf("SELECT comment_id FROM comments " .
                     "WHERE (source_type = 'photo' AND source_id = %d AND parent_id = 0) " .
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
            $share['sharer'] = $this->user_model->get_profile_name($share['sharer_id']);
            $share['profile_pic_path'] = $this->user_model->get_profile_pic_path($share['sharer_id']);
        }
        unset($share);

        return $shares;
    }
}
?>
