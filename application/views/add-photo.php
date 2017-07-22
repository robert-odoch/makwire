<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>

    <?php
    define('STATUS', 'photo');
    require_once(dirname(__FILE__) . '/common/status-nav.php');
    ?>

    <div class='panel panel-default'>
        <div class='panel-heading sr-only'>Add a photo</div>
        <div class='panel-body'>
            <?php if (isset($error)) { ?>
            <div class='alert alert-danger' role='alert'>
                <?= $error; ?>
            </div>
            <?php } ?>
            <form action='<?= base_url('photo/new'); ?>' method='post'
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
