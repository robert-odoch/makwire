<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($suggested_users) > 0) {
?>
    <h4>People you may know</h4>
    <ul class="suggested-users">
        <?php foreach ($people_you_may_know as $p) { ?>
        <li>
            <figure>
                <img src="<?= $p['profile_pic_path']; ?>" alt="<?= $p['profile_name']; ?>">
            </figure>
            <span>
                <a href="<?= base_url("user/profile/{$p['user_id']}"); ?>"><?= $p['profile_name']; ?></a>&nbsp;
                <a href="<?= base_url("user/add-friend/{$p['user_id']}"); ?>" class="btn">Add friend</a>
            </span>
        </li>
        <?php } ?>
    </ul>
<?php
}
?>
