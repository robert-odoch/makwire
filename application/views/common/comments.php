<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <h4>Comments</h4>
    <?php
    if (count($comments) == 0) {
        show_message('No comments to show.', 'info');

    } else {
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
                <div class='media separated'>
                    <div class='media-left'>
                        <img src='<?= $comment['profile_pic_path']; ?>'
                                alt='<?= $comment['commenter']; ?>' class='media-object profile-pic-sm'>
                    </div>
                    <div class='media-body'>
                        <h4 class='media-heading'>
                            <a href='<?= base_url("user/{$comment['commenter_id']}"); ?>'>
                                <strong><?= $comment['commenter']; ?></strong>
                            </a>
                        </h4>

                        <p class='comment'><?= htmlspecialchars($comment['comment']); ?></p>
                        <span class='footer'>
                            <small class='time'>
                                <span class='fa fa-clock-o' aria-hidden='true'></span>
                                <?= $comment['timespan']; ?> ago
                            </small>

                            <?php
                            if ($comment['viewer_is_friend_to_owner']) {
                                print "<span> &middot; </span>" .
                                        "<a href='" . base_url("comment/like/{$comment['comment_id']}") .
                                        "' class='like'>Like</a>";

                                print "<span> &middot; </span>
                                        <a href='" . base_url("comment/reply/{$comment['comment_id']}") .
                                        "'>Reply</a>";
                            }

                            if ($comment['num_likes'] > 0) {
                                print "<span> &middot; </span>" .
                                      "<a href='" . base_url("comment/likes/{$comment['comment_id']}") .
                                      "' class='likes'>{$comment['num_likes']}";
                                print ($comment['num_likes'] == 1) ? " like" : " likes";
                                print "</a>";
                            }
                            else {
                                print "<span class='likes hidden'> &middot; </span>" .
                                      "<a href='" . base_url("comment/likes/{$comment['comment_id']}") .
                                      "' class='likes hidden'></a>";
                            }

                            if ($object != 'comment' && $comment['num_replies'] > 0) {
                                print "<span> &middot; </span>" .
                                      "<a href='" . base_url("comment/replies/{$comment['comment_id']}") .
                                      "'>{$comment['num_replies']}";
                                print ($comment['num_replies'] == 1) ? " reply" : " replies";
                                print "</a>";
                            }

                            if ($comment['commenter_id'] == $_SESSION['user_id']) {
                                print "<span> &middot; </span>
                                        <a href='" . base_url("comment/options/{$comment['comment_id']}") . "'
                                                title='Edit or delete this comment'>More</a>";
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
