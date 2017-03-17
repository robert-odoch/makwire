<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contains functions related to file uploads.
 */
class Upload_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('utility_model');
    }
}
?>
