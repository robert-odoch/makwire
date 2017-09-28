<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
if ($object == 'comment') {
    require_once(__DIR__ . '/../common/comment-or-reply.php');
}
else if ($object == 'birthday-message') {
    require_once(__DIR__ . '/../common/birthday-message.php');
}
?>

<div class='box'>
    <h4>Replies</h4>
    <?php if (count($replies) == 0) { ?>
    <div class='alert alert-info' role='alert'>
        <span class='fa fa-info-circle' aria-hidden='true'></span>
        <p>No replies to show.</p>
    </div>
    <?php } else {
        $ID = ($object == 'birthday-message') ? $message['id'] : $comment['comment_id'];
        if (isset($has_prev)) {
            $url = "{$object}/replies/{$ID}";
            if ($prev_offset != 0) {
                $url .= "/{$prev_offset}";
            }

            print "<a href='" . base_url($url) . "' class='previous'>Show previous replies</a>";
        }
    ?>
    <div class='replies'>
        <?php foreach($replies as $reply) { ?>
        <div class='media separated'>
            <div class='media-left'>
                <img src='<?= $reply['profile_pic_path']; ?>'
                        alt='<?= $reply['commenter']; ?>' class='media-object profile-pic-sm'>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$reply['commenter_id']}"); ?>'>
                        <strong><?= $reply['commenter']; ?></strong>
                    </a>
                </h4>

                <p class='reply'><?= htmlspecialchars($reply['comment']); ?></p>

                <span>
                    <small class='time'>
                        <span class='fa fa-clock-o' aria-hidden='true'></span>
                        <?= $reply['timespan']; ?> ago
                    </small>

                    <?php
                    if ($reply['viewer_is_friend_to_owner']) {
                        print '<span> &middot; </span>' .
                                '<a href="' . base_url("reply/like/{$reply['comment_id']}") . '">Like</a>';
                    }
                    if ($reply['num_likes'] > 0) {
                        print "<span> &middot; </span>";
                        print "<a href='" . base_url("reply/likes/{$reply['comment_id']}") . "'>{$reply['num_likes']}";
                        print ($reply['num_likes'] == 1) ? " like" : " likes";
                        print "</a>";
                    }

                    if ($reply['commenter_id'] == $_SESSION['user_id']) {
                        print "<span> &middot; </span>
                                <a href='" . base_url("reply/options/{$reply['comment_id']}") . "'
                                    title='Edit or delete this reply'>More</a>";
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
    <div class='box more'>
        <a href='<?= base_url("{$object}/replies/{$ID}/{$next_offset}"); ?>'>
            Show more replies
        </a>
    </div>
<?php } ?>
