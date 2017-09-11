<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
if ($has_prev) {
    echo "<a href='" . base_url("user/send-message/{$receiver['user_id']}/{$prev_offset}") .
            "' class='previous'>Show previous messages</a>";
}
?>

<?php foreach ($messages as $m): ?>
    <?php if ($m['sender_id'] == $_SESSION['user_id']): ?>
        <div class='media message sent'>
            <div class='media-left'>
                <img src='<?= $sender['profile_pic_path']; ?>' class='media-object' title='<?= $m['sender']; ?>'>
            </div>
            <div class='media-body'>
                <p><?= $m['message']; ?></p>
                <small><?= (new DateTime($m['date_sent']))->format('g:i a'); ?></small>
            </div>
        </div>
    <?php else: ?>
        <div class='media message received'>
            <div class='media-right pull-right'>
                <img src='<?= $receiver['profile_pic_path']; ?>' class='media-object' title='<?= $m['sender']; ?>'>
            </div>
            <div class='media-body'>
                <p><?= $m['message']; ?></p>
                <small><?= (new DateTime($m['date_sent']))->format('g:i a'); ?></small>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
