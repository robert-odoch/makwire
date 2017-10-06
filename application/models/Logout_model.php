<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contians functions for logging out a user.
 */
class Logout_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Logs out a user.
     */
    public function logout($user_id)
    {
        $_SESSION = array();
        session_destroy();
        setcookie(session_name(), '', time()-300);
    }
}
?>
