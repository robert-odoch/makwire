<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div id='update-status' class='box'>

    <?php
    define('STATUS', 'video');
    require_once(__DIR__ . '/../common/status-nav.php');
    ?>

    <h4 class='sr-only'>Add YouTube video</h4>

    <?php if (isset($error_message)) { ?>
    <div class='alert alert-danger' role='alert'>
        <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
        <span class='sr-only'>Error: </span>
        <p><?= $error_message; ?></p>
    </div>
    <?php }?>

    <?php require_once(__DIR__ . '/../forms/new-video.php'); ?>
</div>
