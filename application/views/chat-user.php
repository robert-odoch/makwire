<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class='chat'>
    <div class='media receiver'>
        <div class='media-left media-middle'>
            <img src='<?= $receiver['profile_pic_path']; ?>' class='media-object'>
        </div>
        <div class='media-body'>
            <a href='<?= base_url("user/{$receiver['user_id']}"); ?>'>
                <?= $receiver['profile_name']; ?>
            </a>
        </div>
    </div>
    <div class='chat-content'>
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
        <div class='new-message'>
            <form action='<?= base_url("user/send-message/{$receiver['user_id']}"); ?>' method='post'>
                <table style='width: 100%'>
                    <tbody>
                        <tr>
                            <td>
                                <input type='text' name='message' class='fluid' placeholder='new message...'>
                            </td>
                            <td><input type='submit' value='Send' class='btn btn-sm'></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<a href='<?= base_url('user/chat'); ?>' class='btn btn-default btn-block back-btn'>&laquo; Active Users</a>
