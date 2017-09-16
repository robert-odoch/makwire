<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>

    <?php
    define('STATUS', 'link');
    require_once(__DIR__ . '/../common/status-nav.php');
    ?>

    <h4 class='sr-only'>Add link to a resource on another website.</h4>

    <?php if (isset($error_message)) { ?>
    <div class='alert alert-danger' role='alert'>
        <p><?= $error_message; ?></p>
    </div>
    <?php }?>

    <?php require_once(__DIR__ . '/../forms/new-link.php'); ?>
</div>
