<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <div id='birthday-message' class='media'>
        <div class='media-left'>
            <img class='media-object' src='<?= $message['profile_pic_path']; ?>'
                alt="<?= $message['sender']; ?>">
        </div>
        <div class='media-body'>
            <h4 class='media-heading'>
                <strong>
                    <a href='<?= base_url("user/{$message['sender_id']}"); ?>'>
                        <?= $message['sender']; ?>
                    </a>
                </strong>
            </h4>

            <p class='message'><?= htmlspecialchars($message['message']); ?></p>

            <span>
                <small class='time'>
                    <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
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
                if ($message['num_replies'] > 0) {
                    print "<span> &middot; </span>" .
                            "<a href='" . base_url("birthday-message/replies/{$message['id']}") .
                            "'>{$message['num_replies']}";
                    print ($message['num_replies'] == 1) ? ' reply' : ' replies';
                    print "</a>";
                }
                ?>
            </span>
        </div>
    </div>

    <?php if ($message['user_can_reply']) { ?>
        <form action='<?= base_url("birthday-message/reply/{$message['id']}"); ?>'
              method='post' accept-charset='utf-8' role='form'>
            <label for='reply' class='sr-only'>Reply</label>
            <input type='text' name='reply' id='reply' class='fluid' placeholder='Leave a reply...' required>
        </form>
    <?php } ?>
</div><!-- box -->
