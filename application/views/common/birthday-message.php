<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <div id="birthday-message" class="media">
        <div class="media-left">
            <img class="media-object" src="<?= $message['profile_pic_path']; ?>"
                alt="<?= $message['sender']; ?>">
        </div>
        <div class="media-body">
            <h4 class="media-heading">
                <strong>
                    <a href="<?= base_url("user/{$message['sender_id']}"); ?>">
                        <?= $message['sender']; ?>
                    </a>
                </strong>
            </h4>

            <p class="message"><?= htmlspecialchars($message['message']); ?></p>
            <small class="time">
                <span class="glyphicon glyphicon-time"></span>
                <?= $message['timespan']; ?> ago
            </small>

            <?php
            if ($message['num_likes'] > 0) {
                print "<span> &middot; </span>" .
                        "<a href='" . base_url("birthday-message/likes/{$message['id']}") .
                        "'>{$message['num_likes']}";
                print ($message['num_likes'] == 1) ? " like" : " likes";
                print "</a>";
            }
            ?>
        </div>
    </div>
</div><!-- box -->
