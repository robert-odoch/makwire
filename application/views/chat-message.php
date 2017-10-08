<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if ($message['sender_id'] == $_SESSION['user_id']): ?>
    <div class='media message sent'>
        <div class='media-left'>
            <img src='<?= $sender['profile_pic_path']; ?>' class='media-object profile-pic-xs' title='<?= $message['sender']; ?>'>
        </div>
        <div class='media-body'>
            <p><?= $message['message']; ?></p>
            <small><?= (new DateTime($message['date_sent']))->format('g:i a'); ?></small>
        </div>
    </div>
<?php else: ?>
    <div class='media message received'>
        <div class='media-right pull-right'>
            <img src='<?= $receiver['profile_pic_path']; ?>' class='media-object profile-pic-xs' title='<?= $message['sender']; ?>'>
        </div>
        <div class='media-body'>
            <p class='text'><?= $message['message']; ?></p>
            <small><?= (new DateTime($message['date_sent']))->format('g:i a'); ?></small>
        </div>
    </div>
<?php endif; ?>
