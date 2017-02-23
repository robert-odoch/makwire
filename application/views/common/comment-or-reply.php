<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="box">
    <article id="<?= $object; ?>">
        <header>
            <a href="<?= base_url("user/index/{$$object['commenter_id']}"); ?>"><?= $$object['commenter']; ?></a>
        </header>
        <p><?= htmlspecialchars($$object['comment']); ?></p>
        <footer>
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
            ?>
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
        </footer>
    </article>
</div><!-- box -->
