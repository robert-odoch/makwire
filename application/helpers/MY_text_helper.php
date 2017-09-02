<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_name')) {

    /**
     * Format Name
     *
     * Adds an s at the end of the name.
     *
     * @param name the name to be formatted.
     * @param append the string to append before ' or 's
     * @return formatted name
     */
    function format_name($name, $append='')
    {
        if ((strtolower($name)[strlen($name)-1] == 's')) {
            $result = "{$name}{$append}'";
        }
        else {
            $result = "{$name}{$append}'s";
        }

        return $result;
    }
}

if (!function_exists('show_message')) {
    function show_message($message, $type) {
        echo "<div class='alert alert-{$type}'><p>{$message['header']}</p></div>";
        if (!empty($message['body'])) {
            echo "<p>{$message['body']}</p>";
        }
    }
}
?>
