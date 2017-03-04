<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($chat_users) > 0):
?>
    <ul class="active-users">
        <?php foreach($chat_users as $chat_user): ?>
        <li>
            <div class="media">
                <div class="media-left media-middle">
                    <img class="media-object" src="<?= $chat_user['profile_pic_path']; ?>"
                    alt="<?= $chat_user['profile_name']?>">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        <a href="<?= base_url("user/send-message/{$chat_user['friend_id']}"); ?>">
                            <?= $chat_user['profile_name']; ?>
                        </a>
                    </h4>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
