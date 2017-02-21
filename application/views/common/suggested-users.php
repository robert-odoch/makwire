<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($suggested_users) > 0): ?>
    <h4>People you may know</h4>
    <ul class="suggested-users">
        <?php foreach ($suggested_users as $su): ?>
        <li>
            <figure><img src="<?= $su['profile_pic_path']; ?>" alt="<?= $su['display_name']; ?>"></figure>
            <span><a href="<?= base_url("user/profile/{$su['user_id']}"); ?>"><?= $su['display_name']; ?></a> <a href="<?= base_url("user/add-friend/{$su['user_id']}"); ?>" class="btn">Add friend</a></span>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
