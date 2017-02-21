<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
?>

<div class="box">
    <article id="comment">
        <header>
            <h4>
            <?php
            print "<a href='" .base_url("user/index/{$comment['commenter_id']}") . "'>{$comment['commenter']}</a>"
            ?>
            </h4>
        </header>
        <p>
            <?= htmlspecialchars($comment['comment']); ?>
        </p>
        <footer>
            <small><span class="glyphicon glyphicon-time"></span> <?= $comment['timespan']; ?> ago</small>
            <?php
            if ($comment['num_replies'] > 0) {
                print "<span> &middot; </span><a href='" . base_url("comment/replies/{$comment['comment_id']}") . "'>{$comment['num_replies']}";
                print ($comment['num_replies'] == 1) ? " reply" : " replies";
                print "</a>";
            }
            ?>
        </footer>
    </article>
</div><!-- box -->
<?php if (count($replies) > 0) { ?>
<div class="box">
    <h4>Replies</h4>
    <?php if (isset($has_prev)) { ?>
        <li>
            <a href="<?= base_url("comment/replies/{$comment['comment_id']}/{$prev_offset}"); ?>">
                View previous replies.
            </a>
        </li>
    <?php } ?>
    <ul class="comments">
        <?php
        $i = $num_prev;
        foreach ($replies as $reply): ?>
        <li>
            <article class="comment">
                <header>
                    <a href=""><strong><?= $reply['replier']; ?></strong></a>
                </header>
                <p class="comment"><?= htmlspecialchars($reply['comment']); ?></p>
                <footer>
                <small><span class="glyphicon glyphicon-time"></span> <i><?= $reply['timespan']; ?> ago</i></small>
                <?php
                // Hide this link from the replier if she is the one currently
                // viewing this page.

                if ($reply['commenter_id'] != $_SESSION['user_id']) {
                    if (!$reply['liked']) {
                        print("<span> &middot; </span>" .
                              "<a href='" . base_url("reply/like/{$reply['comment_id']}/{$comment['comment_id']}/{$i}") . "'>Like</a>");
                    }
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
</div><!-- box -->
<?php if ($has_next) { ?>
<div class="box previous">
    <a href="<?= base_url("comment/replies/{$comment['comment_id']}/{$next_offset}"); ?>">View more replies</a>
</div>
<?php } ?>
<?php } else { ?>
    <div class="box">
        <div class="alert alert-info">
            <p><span class="glyphicon glyphicon-info-sign"></span> No replies to show.</p>
        </div>
    </div>
<?php } ?>
