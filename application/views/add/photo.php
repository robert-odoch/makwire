<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>

    <?php
    define('STATUS', 'photo');
    require_once(__DIR__ . '/../common/status-nav.php');
    ?>

    <h4 class='sr-only'>Add a photo</h4>

    <?php if (isset($error)) { ?>
    <div class='alert alert-danger' role='alert'>
        <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
        <span class='sr-only'>Error: </span>
        <p><?= $error; ?></p>
    </div>
    <?php } ?>

    <?php require_once(__DIR__ . '/../forms/new-photo.php'); ?>
</div>
