<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="box">
    <h4>Comments</h4>
    <?php if (count($comments) == 0) { ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No comments to show.</p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            print("<a href='" . base_url("{$object}/comments/{$$object[$object . '_id']}/{$prev_offset}") . "'>" .
                    "View previous comments.</a>");
        }
    ?>
    <div class="comments">
        <?php
        foreach($comments as $comment):
        ?>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $comment['profile_pic_path']; ?>"
                alt="<?= $comment['commenter']; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$comment['commenter_id']}"); ?>"><strong><?= $comment['commenter']; ?></strong></a>
                </h4>
                <p class="comment"><?= htmlspecialchars($comment['comment']); ?></p>
                <?php
                if ($comment['viewer_is_friend_to_owner']) {
                    if (!$comment['liked']) {
                        print '<a href="' . base_url("comment/like/{$comment['comment_id']}") . '">Like</a>';
                        print '<span> &middot; </span>';
                    }

                    print '<a href="' . base_url("comment/reply/{$comment['comment_id']}") . '">Reply</a>';
                }

                if ($comment['num_likes'] > 0) {
                    print "<span> &middot; </span>" .
                          "<a href='" . base_url("comment/likes/{$comment['comment_id']}") . "'>{$comment['num_likes']}";
                    print ($comment['num_likes'] == 1) ? " like" : " likes";
                    print "</a>";
                }
                if ($object != 'comment' && $comment['num_replies'] > 0) {
                    print "<span> &middot; </span>" .
                          "<a href='" . base_url("comment/replies/{$comment['comment_id']}") . "'>{$comment['num_replies']}";
                    print ($comment['num_replies'] == 1) ? " reply" : " replies";
                    print "</a>";
                }
                ?>
                <small class="time"><span class="glyphicon glyphicon-time"></span> <?= $comment['timespan']; ?> ago</small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php } // (count($comments) == 0) ?>
</div><!-- box -->
<?php if ($has_next) { ?>
<div class="box more">
    <a href="<?= base_url("{$object}/comments/{$$object[$object . '_id']}/{$next_offset}"); ?>">View more comments</a>
</div>
<?php } ?>
