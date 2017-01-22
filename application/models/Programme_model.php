<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Programme_model extends CI_Model
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

    public function get_programmes()
    {
        $q = sprintf("SELECT programme_id, name FROM programmes");
        $query = $this->run_query($q);

        return $query->result_array();
    }
}
