<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4><?= $heading; ?></h4>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger">
                <?= $error; ?>
            </div>
        <?php } ?>
        <form action="<?= base_url("upload/profile-picture"); ?>" method="post" enctype="multipart/form-data" role="form">
            <input type="file" name="userfile" size="20">
            <input type="submit" name="submit" value="Upload" class="btn btn-sm">
        </form>
</div>
