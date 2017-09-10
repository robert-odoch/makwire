<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('is_ajax_request')) {
    function is_ajax_request() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }
}
?>
