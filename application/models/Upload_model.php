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
        if ( ! $query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }
    /*** End Utility ***/

    public function set_profile_picture($data)
    {
        // Store image data in the images table.
        $q = sprintf("INSERT INTO user_images " .
             "(user_id, original_name, image_size, image_type, image_width, image_height, full_path) " .
             "VALUES (%d, %s, %d, %s, %d, %d, %s)",
             $_SESSION['user_id'], $this->db->escape($data['orig_name']), $data['file_size'],
             $this->db->escape($data['file_type']), $data['image_width'], $data['image_height'],
             $this->db->escape($data['full_path']));
        $this->run_query($q);
        $image_id = $this->db->insert_id();

        // Update profile_pic_id in the users table.
        $q = sprintf("UPDATE users SET profile_pic_id=%d WHERE (user_id=%d) LIMIT 1",
                     $image_id, $_SESSION['user_id']);
        $this->run_query($q);

        // Dispatch a notification.
        $q = sprintf("INSERT INTO activities " .
                     "(trigger_id, user_or_group_id, parent_id, source_id, source_type, activity, audience) " .
                     "VALUES (%d, %d, %d, %d, '%s', '%s', '%s')",
                     $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'], $image_id, 'photo', 'profile_pic_change', 'timeline'
            );
        $this->run_query($q);
    }
}
?>
