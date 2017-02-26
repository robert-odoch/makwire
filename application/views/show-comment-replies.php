<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
require_once("common/comment-or-reply.php");
?>

<div class="box">
    <h4>Replies</h4>
    <?php if (count($replies) == 0) { ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No replies to show.</p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            print("<a href='" . base_url("comment/replies/{$comment['comment_id']}/{$prev_offset}") . "'>" .
                  "View previous replies.</a>");
        }
    ?>
    <ul class="replies">
        <?php
        $i = $num_prev;
        foreach($replies as $reply):
        ?>
        <li>
            <article class="reply">
                <header>
                    <a href="<?= base_url("user/index/{$reply['commenter_id']}"); ?>"><strong><?= $reply['commenter']; ?></strong></a>
                </header>
                <p class="reply"><?= htmlspecialchars($reply['comment']); ?></p>
                <footer>
                    <small class="time"><span class="glyphicon glyphicon-time"></span> <?= $reply['timespan']; ?> ago</small>
                    <?php
                    if ($reply['viewer_is_friend_to_owner'] && !$reply['liked']) {
                        print '<span> &middot; </span>' .
                                '<a href="' . base_url("reply/like/{$reply['comment_id']}/{$comment['comment_id']}/{$i}") . '">Like</a>';
                    }
                    if ($reply['num_likes'] > 0) {
                        print "<span> &middot; </span>" .
                              "<a href='" . base_url("reply/likes/{$reply['comment_id']}") . "'>{$reply['num_likes']}";
                        print ($reply['num_likes'] == 1) ? " like" : " likes";
                        print "</a>";
                    }
                    ?>
                </footer>
            </article>
        </li>
        <?php
        ++$i;
        endforeach;
        ?>
    </ul>
    <?php } // (count($replies) == 0) ?>
</div><!-- box -->
<?php if ($has_next) { ?>
<div class="box more">
    <a href="<?= base_url("comment/replies/{$comment['comment_id']}/{$next_offset}"); ?>">View more replies</a>
</div>
<?php } ?>
