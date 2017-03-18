<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <form action="<?= base_url("photo/add-description/{$photo['photo_id']}"); ?>"
        id="add-description" method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="description">
                    Say something about this photo.
                </label>
                <img src="<?= $photo['web_path']; ?>" alt="">
                <input type="text" name="description" placeholder="comment..."
                    id="description" class="fluid">
            </div>
        </fieldset>

        <input type="submit" name="submit" value="Submit" class="btn btn-sm">
    </form>
</div>
