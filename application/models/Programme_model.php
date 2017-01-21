<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Programme_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_programmes()
    {
        $q = sprintf("SELECT programme_id, name FROM programmes");
        $query = $this->db->query($q);
        if ( ! $query) {
            $error = $this->db->error();
            print $error;
            exit(1);
        }
        
        return $query->result_array();
    }
}
