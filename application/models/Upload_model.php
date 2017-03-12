<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
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
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function set_profile_picture($data)
    {
        // Store image data in the images table.
        $photo_sql = sprintf("INSERT INTO user_photos " .
                                "(user_id, original_name, image_size, image_type, " .
                                "image_width, image_height, full_path) " .
                                "VALUES (%d, %s, %d, %s, %d, %d, %s)",
                                $_SESSION['user_id'], $this->db->escape($data['orig_name']),
                                $data['file_size'], $this->db->escape($data['file_type']),
                                $data['image_width'], $data['image_height'],
                                $this->db->escape($data['full_path']));
        $this->run_query($photo_sql);
        $photo_id = $this->db->insert_id();

        // Update profile_pic_path in the users table.
        $profile_pic_path = "{$data['file_path']}thumbnails/{$data['file_name']}";
        $update_sql = sprintf("UPDATE users " .
                                "SET profile_pic_path = %s " .
                                "WHERE (user_id = %d) LIMIT 1",
                                $this->db->escape($profile_pic_path),
                                $_SESSION['user_id']);
        $this->run_query($update_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, 'photo', 'profile_pic_change')",
                                $_SESSION['user_id'], $_SESSION['user_id'], $photo_id);
        $this->run_query($activity_sql);
    }
}
?>
