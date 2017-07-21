<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <div class='panel panel-default'>
        <div class='panel-heading'>Add YouTube video</div>
        <div class='panel-body'>
            <form action='<?= base_url('video/new'); ?>' method='post' role='form'>
                <div class='form-group'>
                    <label for='video-url'>YouTube video URL:</label>
                    <input type='url' name='video_url' id='video-url' class='fluid
                            <?php if (isset($error_message)) print ' has-error'; ?>'
                            <?php if (!empty($video_url)) print " value='{$video_url}'"; ?> required>
                    <?php if (isset($error_message)) { ?>
                        <span class='error'><?= $error_message; ?></span>
                    <?php } ?>
                </div>
                <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
            </form>
        </div>
    </div>
</div>
