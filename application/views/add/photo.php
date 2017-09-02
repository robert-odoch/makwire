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
