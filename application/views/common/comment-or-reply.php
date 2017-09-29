<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <div id='<?= $object; ?>' class='media'>
        <div class='media-left'>
            <img src='<?= $$object['profile_pic_path']; ?>'
                alt='<?= $$object['commenter']; ?>' class='media-object profile-pic-md'>
        </div>
        <div class='media-body'>
            <h4 class='media-heading'>
                <strong>
                    <a href='<?= base_url("user/{$$object['commenter_id']}"); ?>'>
                        <?= $$object['commenter']; ?>
                    </a>
                </strong>
            </h4>

            <p class='comment'><?= htmlspecialchars($$object['comment']); ?></p>

            <span class='footer'>
                <small class='time'>
                    <span class='fa fa-clock-o' aria-hidden='true'></span>
                    <?= $$object['timespan']; ?> ago
                </small>

                <?php
                if ($$object['num_likes'] > 0) {
                    print "<span> &middot; </span>" .
                            "<a href='" . base_url("{$object}/likes/{$$object['comment_id']}") .
                            "' class='likes'>{$$object['num_likes']}";
                    print ($$object['num_likes'] == 1) ? " like" : " likes";
                    print "</a>";
                }
                else {
                    print "<span class='likes hidden'> &middot; </span>" .
                            "<a href='" . base_url("{$object}/likes/{$$object['comment_id']}") .
                            "' class='likes hidden'></a>";
                }

                if ($object == 'comment') {
                    if ($comment['num_replies'] > 0) {
                        print "<span> &middot; </span>" .
                                "<a href='". base_url("comment/replies/{$comment['comment_id']}") .
                                "'>{$comment['num_replies']}";
                        print ($comment['num_replies'] == 1) ? " reply" : " replies";
                        print "</a>";
                    }
                }

                if ($$object['commenter_id'] == $_SESSION['user_id']) {
                    print "<span> &middot; </span>
                            <a href='" . base_url("{$object}/options/{$$object['comment_id']}") . "'
                                    title='Edit or delete this {$object}'>More</a>";
                }
                ?>
            </span>
        </div>
    </div>

    <?php if ($object == 'comment') { ?>
        <form action='<?= base_url("comment/reply/{$comment['comment_id']}"); ?>'
            method='post' accept-charset='utf-8' role='form'>
            <input type='text' name='reply' placeholder='Write a reply...' class='fluid
            <?php
            if (isset($reply_error)) {
                print " has-error";
            }
            ?>' required>
            <?php
            if (isset($reply_error)) {
                print "<span class='error'>{$reply_error}</span>";
            }
            ?>
        </form>
    <?php } ?>
</div><!-- box -->
