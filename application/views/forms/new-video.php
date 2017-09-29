<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form action='<?= base_url('video/new'); ?>' method='post' role='form'>
    <div class='form-group'>
        <label for='video-url'>YouTube video URL:</label>
        <input type='url' name='video_url' id='video-url' class='fluid
                <?php if ( ! empty($error_message)) print " has-error"; ?>'
                <?php if (!empty($video_url)) print " value='{$video_url}'"; ?> required>
        <?php if ( ! empty($error_message)) print "<span class='error'>{$error_message}</span>"; ?>
    </div>
    <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
</form>
