<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('is_ajax_request')) {
    function is_ajax_request() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }
}

if (!function_exists('ensure_user_is_logged_in')) {
    function ensure_user_is_logged_in()
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }
    }
}
?>
