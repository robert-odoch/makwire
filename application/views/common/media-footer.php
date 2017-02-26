<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<footer>
    <?php
    if ($$object['num_likes'] > 0) {
        print "<a href='" . base_url("$object/likes/{$$object[$object . '_id']}") . "'>{$$object['num_likes']}";
        print ($$object['num_likes'] == 1) ? " like" : " likes";
        print "</a>";
    }

    if ($$object['num_likes'] > 0 && $$object['num_comments'] > 0) {
        print("<span> &middot; </span>");
    }

    if ($$object['num_comments'] > 0) {
        print "<a href='" . base_url("$object/comments/{$$object[$object . '_id']}") . "'>{$$object['num_comments']}";
        print ($$object['num_comments'] == 1) ? " comment" : " comments";
        print "</a>";
    }
    if (($$object['num_likes'] > 0 && $$object['num_shares'] > 0) ||
        ($$object['num_comments'] > 0 && $$object['num_shares'] > 0)) {
            print("<span> &middot; </span>");
        }

    if ($$object['num_shares'] > 0) {
        print "<a href='" . base_url("$object/shares/{$$object[$object . '_id']}") . "'>{$$object['num_shares']}";
        print ($$object['num_shares'] == 1) ? " share" : " shares";
        print "</a>";
    }
    ?>
    <?php if ($$object['viewer_is_friend_to_owner']) { ?>
    <ul>
        <li>
            <a href="<?= base_url("$object/like/{$$object[$object . '_id']}"); ?>" title="Like this <?= $object; ?>"><span class="glyphicon glyphicon-thumbs-up"></span> Like</a>
            <span> &middot; </span>
        </li>
        <li>
            <a href="<?= base_url("$object/comment/{$$object[$object . '_id']}"); ?>" title="Comment on this <?= $object; ?>"><span class="glyphicon glyphicon-comment"></span> Comment</a>
            <span> &middot; </span>
        </li>
        <li>
            <a href="<?= base_url("$object/share/{$$object[$object . '_id']}"); ?>" title="Share this <?= $object; ?>">
                <span class="glyphicon glyphicon-share"></span> Share
            </a>
        </li>
    </ul>
    <form action="<?= base_url("$object/comment/{$$object[$object . '_id']}"); ?>" method="post" accept-charset="utf-8" role="form">
        <input type="text" name="comment" placeholder="Write a comment..." class="fluid
        <?php
        if (isset($comment_error)) {
            print(" has-error");
        }
        ?>">
        <?php
        if (isset($comment_error)) {
            print("<span class='error'>{$comment_error}</span>");
        }
        ?>
    </form>
    <?php } // ($$object['viewer_is_friend_to_owner']) ?>
</footer>
