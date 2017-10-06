<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div id='update-status' class='box'>

    <?php
    define('STATUS', 'photo');
    require_once(__DIR__ . '/../common/status-nav.php');
    ?>

    <h4 class='sr-only'>Add a photo</h4>

    <?php if ( ! empty($error)) { show_message($error, 'danger', FALSE); } ?>

    <?php require_once(__DIR__ . '/../forms/new-photo.php'); ?>
</div>
