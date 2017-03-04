<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Find friends</h4>
    <form action="" method="post" enctype="multipart/form-data" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="query">Name or email address</label>
                <input type="search" name="query" id="query" class="fluid" placeholder="Search for a friend">
            </div>
        </fieldset>
        <input type="submit" value="Search" class="btn btn-sm">
    </form>
</div><!-- .box -->
<?php if (count($suggested_users) > 0): ?>
<div class="box">
    <h4>People you may know</h4>
    <div class="suggested-users">
        <?php foreach ($suggested_users as $user): ?>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $user['profile_pic_path']; ?>"
                alt="<?= $user['profile_name']; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/index/{$user['user_id']}"); ?>"><?= $user['profile_name']; ?></a>
                </h4>
                <a href="<?= base_url("user/add-friend/{$user['user_id']}"); ?>"
                    class="btn btn-sm">Add friend</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div><!-- .box -->
<?php if ($has_next): ?>
<div class="box more">
    <a href="<?= base_url("user/find-friends/{$next_offset}"); ?>">View more suggestions</a>
</div>
<?php endif; ?>
<?php endif; ?>
