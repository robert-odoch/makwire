<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>

    <?php
    define('STATUS', 'video');
    require_once(__DIR__ . '/../common/status-nav.php');
    ?>

    <h4 class='sr-only'>Add YouTube video</h4>

    <?php if (isset($error_message)) { ?>
    <div class='alert alert-danger' role='alert'>
        <p><?= $error_message; ?></p>
    </div>
    <?php }?>

    <form action='<?= base_url('video/new'); ?>' method='post' role='form'>
        <div class='form-group'>
            <label for='video-url'>YouTube video URL:</label>
            <input type='url' name='video_url' id='video-url' class='fluid'
                    <?php if (!empty($video_url)) print " value='{$video_url}'"; ?> required>
        </div>
        <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
    </form>
</div>
