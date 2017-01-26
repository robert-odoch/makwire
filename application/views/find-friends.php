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
        <input type="submit" value="Search" class="btn">
    </form>
</div><!-- .box -->
<?php if (count($suggested_users) > 0): ?>
<div class="box">
    <h4>People you may know</h4>
    <ul class="suggested-users">
        <?php foreach ($suggested_users as $user): ?>
        <li>
            <figure><img src="<?= base_url('images/kasumba.jpg'); ?>" alt="<?= $user['display_name']; ?>"></figure>
            <span>
                <a href="<?= base_url("user/index/{$user['user_id']}"); ?>"><?= $user['display_name']; ?></a>
                <a href="<?= base_url("user/add_friend/{$user['user_id']}"); ?>" class="btn">Add friend</a>
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
</div><!-- .box -->
<?php if ($has_next): ?>
<div class="box more">
    <a href="<?= base_url("user/find-friends/{$next_offset}"); ?>">View more suggestions</a>
</div>
<?php endif; ?>
<?php endif; ?>
