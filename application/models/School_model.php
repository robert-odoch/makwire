<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class School_model extends CI_Model
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

    public function get_schools()
    {
        $q = sprintf("SELECT school_id, college_id, name FROM mak_schools");
        $query = $this->run_query($q);

        return $query->result_array();
    }

    public function add_school($school_id)
    {
        $q = sprintf("UPDATE user_profile SET school_id=%d WHERE user_id=%d LIMIT 1",
                     $school_id, $_SESSION['user_id']);
        $this->run_query($q);
    }
}
?>
