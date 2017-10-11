<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='col-small'>
    <?php if (count($chat_users) > 0) { ?>
        <div class='active-users'>
            <!-- For refreshing chat users using AJAX. -->
            <a href='<?= base_url('user/chat'); ?>' class='show-active-users'></a>

            <?php foreach($chat_users as $user) { ?>
                <div class='media'>
                    <div class='media-left media-middle'>
                        <img src='<?= $user['profile_pic_path']; ?>'
                            alt='<?= $user['profile_name']?>' class='media-object profile-pic-sm'>
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
</div>
