<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div id="add-description" class="box">
    <h4>Say something about this photo</h4>
    <img src="<?= $photo['web_path']; ?>">
    <form action="<?= base_url("photo/add-description/{$photo['photo_id']}"); ?>"
        method="post" accept-charset="utf-8" role="form">
        <textarea name="description" placeholder="Your say..."id="description" class="fluid
            <?php if (isset($error_message)) print ' has-error'; ?>"></textarea>
        <?php if (isset($error_message)) { ?>
            <span class='error'><?= $error_message; ?></span>
        <?php } ?>
        <input type="submit" name="submit" value="Submit" class="btn btn-sm">
    </form>
</div>
