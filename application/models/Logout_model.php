<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
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

    public function logout()
    {
        $q = sprintf("UPDATE users SET logged_in=%d WHERE user_id=%d",
                     0, $_SESSION['user_id']);

        $query = $this->run_query($q);

        $_SESSION = array();
        session_destroy();
        setcookie(session_name(), '', time()-300);

        redirect(base_url('login/'));
        exit(0);
    }
}
?>
