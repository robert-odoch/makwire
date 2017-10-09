<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add_user_invite($email, $college_id)
    {
        // If an invitation already exits, just update is_used.
        $sql = sprintf('UPDATE admin_invite SET is_used = 0, date_entered = CURRENT_TIMESTAMP() WHERE email = %s',
                        $this->db->escape($email));
        $this->db->query($sql);

        // Otherwise add a new record.
        if ($this->db->affected_rows() == 0) {
            $sql = sprintf('INSERT INTO admin_invite (email, college_id) VALUES (%s, %d)',
                            $this->db->escape($email), $college_id);
            $this->db->query($sql);
        }
    }

}
?>
