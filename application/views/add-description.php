<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Say something about this photo</h4>
    <form action="<?= base_url("photo/add-description/{$photo['photo_id']}"); ?>"
        id="add-description" method="post" accept-charset="utf-8" role="form">

        <textarea name="description" placeholder="Your say..."id="description" class="fluid"></textarea>
        <img src="<?= $photo['web_path']; ?>">
        <input type="submit" name="submit" value="Submit" class="btn btn-sm">
    </form>
</div>
