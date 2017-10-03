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

if (!function_exists('host_www_root')) {
    function host_www_root() {
        return '/opt/lampp/htdocs/makwire/';
    }
}

if (!function_exists('upload_dir')) {
    function upload_dir() {
        return host_www_root() . 'uploads/';
    }
}

if (!function_exists('profile_pics_directory')) {
    function profile_pics_directory() {
        return upload_dir() . 'profile_pics/';
    }
}

if (!function_exists('get_web_path')) {
    function get_image_web_path($full_path) {
        $web_path = str_replace(host_www_root(), '', $full_path);
        return base_url($web_path);
    }
}
?>
