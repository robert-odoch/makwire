<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class="box">
    <div class="panel panel-success">
        <div class="panel-heading"><?= $heading; ?></div>
        <div class="panel-body">
            <?php if (isset($error)) { ?>
            <div class="alert alert-danger" role="alert">
                <?= $error; ?>
            </div>
            <?php } ?>
            <form action="<?= $form_action; ?>" method="post"
                enctype="multipart/form-data" role="form">
                <div class="form-group">
                    <label for="userfile">Choose a photo:</label>
                    <input type="file" name="userfile" id="userfile">
                </div>
                <input type="submit" name="submit" value="Upload" class="btn btn-sm">
            </form>
        </div>
    </div>
</div>
