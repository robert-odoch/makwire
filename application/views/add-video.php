<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <div class='panel panel-success'>
        <div class='panel-heading'>Add YouTube video</div>
        <div class='panel-body'>
            <?php if (isset($error)) { ?>
            <div class='alert alert-danger' role='alert'>
                <?= $error; ?>
            </div>
            <?php } ?>
            <form action='<?= base_url('video/new'); ?>' method='post' role='form'>
                <div class='form-group'>
                    <label for='video-url'>YouTube video URL:</label>
                    <input type='url' name='video_url' id='video-url' class='fluid' required>
                </div>
                <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
            </form>
        </div>
    </div>
</div>
