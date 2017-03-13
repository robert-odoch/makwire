<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="box">
    <div id="<?= $object; ?>" class="media">
        <div class="media-left">
            <img class="media-object" src="<?= $$object['profile_pic_path']; ?>"
                alt="<?= $$object['commenter']; ?>">
        </div>
        <div class="media-body">
            <h4 class="media-heading">
                <strong>
                    <a href="<?= base_url("user/{$$object['commenter_id']}"); ?>">
                        <?= $$object['commenter']; ?>
                    </a>
                </strong>
            </h4>
            <p class="comment"><?= htmlspecialchars($$object['comment']); ?></p>

            <small class="time"><span class="glyphicon glyphicon-time"></span> <?= $$object['timespan']; ?> ago</small>
            <?php
            if ($$object['num_likes'] > 0) {
                print "<span> &middot; </span><a href='" . base_url("{$object}/likes/{$$object['comment_id']}") . "'>{$$object['num_likes']}";
                print ($$object['num_likes'] == 1) ? " like" : " likes";
                print "</a>";
            }
            if ($object == 'comment') {
                if ($comment['num_replies'] > 0) {
                    print "<span> &middot; </span><a href='". base_url("comment/replies/{$comment['comment_id']}") . "'>{$comment['num_replies']}";
                    print ($comment['num_replies'] == 1) ? " reply" : " replies";
                    print "</a>";
                }
            }
            ?>
        </div>
    </div>
    <?php if ($object == 'comment') { ?>
        <form action="<?= base_url("comment/reply/{$comment['comment_id']}"); ?>" method="post" accept-charset="utf-8" role="form">
            <input type="text" name="reply" placeholder="Write a reply..." class="fluid
            <?php
            if (isset($reply_error)) {
                print " has-error";
            }
            ?>">
            <?php
            if (isset($reply_error)) {
                print "<span class='error'>{$reply_error}</span>";
            }
            ?>
        </form>
    <?php } ?>
</div><!-- box -->
