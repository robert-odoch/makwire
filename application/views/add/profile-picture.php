<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
require_once(__DIR__ . '/../common/secondary-user-nav.php');
?>

<div class='box'>
    <div class='panel panel-default'>
        <div class='panel-heading'>Change profile picture</div>
        <div class='panel-body'>
            <?php if (isset($error)) { ?>
            <div class='alert alert-danger' role='alert'>
                <span class='fa fa-exclamation-circle' aria-hidden='true'></span>
                <span class='sr-only'>Error: </span>
                <?= $error;  // Errors for images come embedded in a paragraph. ?>
            </div>
            <?php } ?>
            <form action='<?= base_url('profile/change-profile-picture'); ?>' method='post'
                    enctype='multipart/form-data' role='form'>
                <div class='form-group'>
                    <label for='userfile'>Choose a photo:</label>
                    <input type='file' name='userfile' id='userfile' required>
                </div>
                <input type='submit' name='submit' value='Upload' class='btn btn-sm'>
            </form>
        </div>
    </div>
</div>
