<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <div id='birthday-message' class='media'>
        <div class='media-left'>
            <img src='<?= $message['profile_pic_path']; ?>'
                alt='<?= $message['sender']; ?>' class='media-object profile-pic-md'>
        </div>
        <div class='media-body'>
            <h4 class='media-heading'>
                <strong>
                    <a href='<?= base_url("user/{$message['sender_id']}"); ?>'>
                        <?= $message['sender']; ?>
                    </a>
                </strong>
            </h4>

            <p class='birthday-message'><?= htmlspecialchars($message['message']); ?></p>

            <span class='footer'>
                <small class='time'>
                    <span class='fa fa-clock-o' aria-hidden='true'></span>
                    <?= $message['timespan']; ?> ago
                </small>

                <?php
                if ($message['viewer_is_friend_to_owner']) {
                    print "<span> &middot; </span>" .
                            "<a href='" . base_url("birthday-message/like/{$message['id']}") .
                            "' class='like'>Like</a>";
                    print "<span> &middot; </span>" .
                            "<a href='" . base_url("birthday-message/reply/{$message['id']}") .
                            "'>Reply</a>";
                }

                if ($message['num_likes'] > 0) {
                    print "<span> &middot; </span>" .
                            "<a href='" . base_url("birthday-message/likes/{$message['id']}") .
                            "' class='likes'>{$message['num_likes']}";
                    print ($message['num_likes'] == 1) ? " like" : " likes";
                    print "</a>";
                }
                else {
                    print "<span class='likes hidden'> &middot; </span>" .
                            "<a href='" . base_url("birthday-message/likes/{$message['id']}") .
                            "' class='likes hidden'></a>";
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

    <?php if ($message['viewer_is_friend_to_owner']) { ?>
        <form action='<?= base_url("birthday-message/reply/{$message['id']}"); ?>'
              method='post' accept-charset='utf-8' role='form'>
            <label for='reply' class='sr-only'>Reply</label>
            <input type='text' name='reply' id='reply' class='fluid' placeholder='Leave a reply...' required>
        </form>
    <?php } ?>
</div><!-- box -->
