<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Default_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query("SET time_zone='+0:00'");
    }
}

?>
