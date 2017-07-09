<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
if ($object == 'comment') {
    require_once(dirname(__FILE__) . '/../common/comment-or-reply.php');
}
else if ($object == 'birthday-message') {
    require_once(dirname(__FILE__) . '/../common/birthday-message.php');
}
?>

<div class="box">
    <h4>Replies</h4>
    <?php if (count($replies) == 0) { ?>
    <div class="alert alert-info" role="alert">
        <p>
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            No replies to show.
        </p>
    </div>
    <?php } else {
        $ID = ($object == 'birthday-message') ? $message['id'] : $comment['comment_id'];
        if (isset($has_prev)) {
            print "<a href='" . base_url("{$object}/replies/{$ID}/{$prev_offset}") . "'>" .
                    "View previous replies.</a>";
        }
    ?>
    <div class="replies">
        <?php foreach($replies as $reply) { ?>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $reply['profile_pic_path']; ?>"
                    alt="<?= format_name($reply['commenter']); ?> photo">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$reply['commenter_id']}"); ?>">
                        <strong><?= $reply['commenter']; ?></strong>
                    </a>
                </h4>

                <p class="reply"><?= htmlspecialchars($reply['comment']); ?></p>

                <span>
                    <small class="time">
                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                        <?= $reply['timespan']; ?> ago
                    </small>

                    <?php
                    if ($reply['viewer_is_friend_to_owner'] && !$reply['liked']) {
                        print '<span> &middot; </span>' .
                                '<a href="' . base_url("reply/like/{$reply['comment_id']}") . '">Like</a>';
                    }
                    if ($reply['num_likes'] > 0) {
                        if (!$reply['liked']) {
                            print "<span> &middot; </span>";
                        }

                        print "<a href='" . base_url("reply/likes/{$reply['comment_id']}") . "'>{$reply['num_likes']}";
                        print ($reply['num_likes'] == 1) ? " like" : " likes";
                        print "</a>";
                    }
                    ?>
                </span>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } // (count($replies) == 0) ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class="box more">
        <a href="<?= base_url("{$object}/replies/{$ID}/{$next_offset}"); ?>">
            View more replies
        </a>
    </div>
<?php } ?>
