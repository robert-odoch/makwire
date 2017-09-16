<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <h4>Chat</h4>
    <?php if (count($chat_users) == 0) { ?>
        <div class='alert alert-info' role='alert'>
            <p>
                <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span>
                None of your friends are on chat at this moment.
            </p>
        </div>
    <?php } else { ?>
        <div class='chat-friends'>
            <?php foreach ($chat_users as $cu) { ?>
                <div class='media'>
                    <div class='media-left'>
                        <img class='media-object' src='<?= $cu['profile_pic_path']; ?>'
                                alt="<?= $cu['profile_name']; ?>">
                    </div>
                    <div class='media-body'>
                        <a href='<?= base_url("user/send-message/{$cu['friend_id']}"); ?>' class='send-message'>
                            <?= $cu['profile_name']; ?>
                        </a>
                        <span class='logged-in'></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div><!-- .box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/chat/{$next_offset}"); ?>'>Show more friends</a>
    </div>
<?php } ?>
