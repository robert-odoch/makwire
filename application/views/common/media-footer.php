<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<footer class='footer media-footer'>
    <?php
    if ($$object['num_likes'] > 0) {
        print "<a href='" . base_url("$object/likes/{$$object[$object . '_id']}") .
                "' class='likes'>{$$object['num_likes']}";
        print ($$object['num_likes'] == 1) ? " like" : " likes";
        print "</a>";
    }
    else {
        print "<a href='" . base_url("{$object}/likes/{$$object[$object . '_id']}") .
                "' class='likes hidden'></a>
                <span class='likes hidden'> &middot; </span>";
    }

    if ($$object['num_likes'] > 0 && $$object['num_comments'] > 0) {
        print("<span> &middot; </span>");
    }

    if ($$object['num_comments'] > 0) {
        print "<a href='" . base_url("$object/comments/{$$object[$object . '_id']}") .
                "'>{$$object['num_comments']}";
        print ($$object['num_comments'] == 1) ? " comment" : " comments";
        print "</a>";
    }

    if (($$object['num_likes'] > 0 && $$object['num_shares'] > 0) ||
            ($$object['num_comments'] > 0 && $$object['num_shares'] > 0)) {
        print("<span> &middot; </span>");
    }

    if ($$object['num_shares'] > 0) {
        print "<a href='" . base_url("$object/shares/{$$object[$object . '_id']}") .
                "'>{$$object['num_shares']}";
        print ($$object['num_shares'] == 1) ? " share" : " shares";
        print "</a>";
    }
    ?>
    <?php if ($$object['viewer_is_friend_to_owner']) { ?>
        <ul>
            <li>
                <a href='<?= base_url("$object/like/{$$object[$object . '_id']}"); ?>' class='like'
                    data-toggle='tooltip' data-placement='top' title='Like this <?= $object; ?>'>
                    <?php if ($$object['liked'] && $$object['user_id'] != $_SESSION['user_id']): ?>
                        <span class='fa fa-thumbs-up' aria-hidden='true'></span>
                    <?php else: ?>
                        <span class='fa fa-thumbs-o-up' aria-hidden='true'></span>
                    <?php endif; ?>
                    Like
                </a>
                <span> &middot; </span>
            </li>
            <li>
                <a href='<?= base_url("$object/comment/{$$object[$object . '_id']}"); ?>'
                    data-toggle='tooltip' data-placement='top' title='Comment on this <?= $object; ?>'>
                    <span class='fa fa-comment-o' aria-hidden='true'></span> Comment
                </a>
                <span> &middot; </span>
            </li>
            <li>
                <a href='<?= base_url("$object/share/{$$object[$object . '_id']}"); ?>'
                    data-toggle='tooltip' data-placement='top' title='Share this <?= $object; ?>'>
                    <span class='fa fa-share' aria-hidden='true'></span> Share
                </a>
            </li>

            <?php
            if (
                (!$$object['shared'] && $$object['user_id'] == $_SESSION['user_id']) ||
                ($$object['shared'] && ($$object['sharer_id'] == $_SESSION['user_id']))
            ): ?>
            <li class='dropdown pull-right'>
                <button class='btn btn-xs btn-default dropdown-toggle' type='button'
                    id='options-menu' data-toggle='dropdown' aria-haspopup='true'
                    aria-expanded='true' style='margin-bottom: 2px;'>
                    Options <span class='caret'></span>
                </button>
                <ul class='dropdown-menu' aria-labelledby='options-menu'>
                    <?php if (!$$object['shared']):  // Only the owners of an item can edit it. ?>
                        <li>
                            <a href='<?= base_url("{$object}/edit/{$$object[$object . '_id']}"); ?>'>
                                Edit
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href='<?= base_url("{$object}/delete/{$$object[$object . '_id']}"); ?>'>
                            Delete
                        </a>
                    </li>

                    <?php if ($object == 'photo' && $$object['user_id'] == $_SESSION['user_id']): ?>
                        <li>
                            <a href='<?= base_url("{$object}/options/{$$object[$object . '_id']}"); ?>'>
                                More
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
        <form action='<?= base_url("$object/comment/{$$object[$object . '_id']}"); ?>'
                method='post' accept-charset='utf-8' role='form'>
            <input type='text' name='comment' placeholder='Write a comment...' class='fluid
            <?php
            if (isset($comment_error)) {
                print(" has-error");
            }
            ?>' required>
            <?php
            if (isset($comment_error)) {
                print("<span class='error'>{$comment_error}</span>");
            }
            ?>
        </form>
    <?php } // ($$object['viewer_is_friend_to_owner']) ?>
</footer>
