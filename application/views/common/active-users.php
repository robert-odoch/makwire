<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($chat_users) > 0):
?>
    <h4>Active</h4>
    <ul class="active-users">
        <?php foreach($chat_users as $chat_user): ?>
        <li>
            <figure><img src="<?= $chat_user['profile_pic_path']; ?>" alt="<?= $chat_user['profile_name']?>" class="user"></figure>
            <span><a href="<?= base_url("user/send-message/{$chat_user['friend_id']}"); ?>"><?= $chat_user['profile_name']; ?></a></span>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
