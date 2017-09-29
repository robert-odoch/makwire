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
    function show_message($message, $type, $user_paragraph = TRUE) {
        print "<div class='alert alert-{$type}' role='alert'>";

        switch ($type) {
            case 'info': print "<span class='fa fa-info-circle' aria-hidden='true'></span>";
                break;
            case 'danger':
                print "<span class='fa fa-exclamation-circle' aria-hidden='true'></span>";
                break;
            case 'success': print "<span class='fa fa-check-circle' aria-hidden='true'></span>";
                break;
            case 'warning': print "<span class='fa fa-warning' aria-hidden='true'></span>";
                break;
            default:
                // do nothing...
                break;
        }

        print ($user_paragraph) ? "<p class='text'>{$message}</p>" : $message;
        print "</div>";
    }
}
?>
