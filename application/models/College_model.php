<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class College_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_colleges()
    {
        $q = sprintf("SELECT college_id, name FROM colleges");
        $query = $this->db->query($q);
        if ( ! $query) {
            $error = $this->db->error();
            print $error;
            exit(1);
        }
        
        return $query->result_array();
    }
    
    public function college_and_school_exists($college_id, $school_id)
    {
        $q = sprintf("SELECT * FROM mak_schools WHERE (school_id=%d AND college_id=%d) LIMIT 1",
                     $school_id, $college_id);
        $query = $this->db->query($q);
        if ( ! $query) {
            $error = $this->db->error();
            print $error;
            exit(1);
        }
        if ($query->num_rows() == 1) {
            return TRUE;
        }
        
        return FALSE;
    }
}
?>
