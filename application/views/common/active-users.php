<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (count($chat_users) > 0) { ?>
    <div class='active-users'>
        <?php foreach($chat_users as $user) { ?>
            <div class='media'>
                <div class='media-left media-middle'>
                    <img class='media-object' src='<?= $user['profile_pic_path']; ?>'
                        alt="<?= $user['profile_name']?>">
                </div>
                <div class='media-body'>
                    <h4 class='media-heading'>
                        <a href='<?= base_url("user/send-message/{$user['user_id']}"); ?>' class='send-message'>
                            <?= $user['profile_name']; ?>
                        </a>
                    </h4>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
