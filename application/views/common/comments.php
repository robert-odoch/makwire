<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <h4>Comments</h4>
    <?php if (count($comments) == 0) { ?>
        <div class='alert alert-info' role='alert'>
            <p>
                <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> No comments to show.
            </p>
        </div>
    <?php } else { ?>
        <?php
        if (isset($has_prev)) {
            $url = "{$object}/comments/{$$object[$object . '_id']}";
            if ($prev_offset != 0) {
                $url .= "/{$prev_offset}";
            }

            print "<a href='" . base_url($url) . "' class='previous'>Show previous comments</a>";
        }
        ?>

        <div class='comments'>
            <?php foreach($comments as $comment) { ?>
                <div class='media'>
                    <div class='media-left'>
                        <img class='media-object' src='<?= $comment['profile_pic_path']; ?>'
                                alt="<?= $comment['commenter']; ?>">
                    </div>
                    <div class='media-body'>
                        <h4 class='media-heading'>
                            <a href='<?= base_url("user/{$comment['commenter_id']}"); ?>'>
                                <strong><?= $comment['commenter']; ?></strong>
                            </a>
                        </h4>

                        <p class='comment'><?= htmlspecialchars($comment['comment']); ?></p>
                        <span>
                            <small class='time'>
                                <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                                <?= $comment['timespan']; ?> ago
                            </small>

                            <?php
                            if ($comment['viewer_is_friend_to_owner']) {
                                print '<span> &middot; </span>' .
                                        '<a href="' . base_url("comment/like/{$comment['comment_id']}") .
                                        '">Like</a>' .
                                        '<span> &middot; </span>';

                                // Only allow a user to reply to his comment if there is atleast one reply.
                                if ($comment['commenter_id'] != $_SESSION['user_id'] ||
                                    $comment['num_replies'] > 0) {
                                    print '<a href="' . base_url("comment/reply/{$comment['comment_id']}") .
                                            '">Reply</a>';
                                }
                            }

                            if ($comment['num_likes'] > 0) {
                                print "<span> &middot; </span>" .
                                      "<a href='" . base_url("comment/likes/{$comment['comment_id']}") .
                                      "'>{$comment['num_likes']}";
                                print ($comment['num_likes'] == 1) ? " like" : " likes";
                                print "</a>";
                            }
                            if ($object != 'comment' && $comment['num_replies'] > 0) {
                                print "<span> &middot; </span>" .
                                      "<a href='" . base_url("comment/replies/{$comment['comment_id']}") .
                                      "'>{$comment['num_replies']}";
                                print ($comment['num_replies'] == 1) ? " reply" : " replies";
                                print "</a>";
                            }
                            ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } // (count($comments) == 0) ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("{$object}/comments/{$$object[$object . '_id']}/{$next_offset}"); ?>'>
            Show more comments
        </a>
    </div>
<?php } ?>
