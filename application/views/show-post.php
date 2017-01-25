<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function can_share_post($user_id, $post)
{
    if (($post['author_id'] === $_SESSION['user_id']) ||
        ($post['shared'] && $post['source_id']===$_SESSION['user_id'])) {
        return FALSE;
    }

    return TRUE;
}

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <article class="post">
                                <header>
                                    <h4>
                                    <?php
                                    if ($post['shared']) {
                                        print "<a href='" . base_url("user/index/{$post['author_id']}") . "'>{$post['author']}</a> shared <a href='" . base_url("user/index/{$post['source_id']}") . "'>{$post['source']}</a>'s post";
                                    }
                                    else {
                                        print "<a href='" . base_url("user/index/{$post['author_id']}") . "'>{$post['author']}</a>";
                                    }
                                    ?>
                                    </h4>
                                </header>
                                <p class="post">
                                    <?php print(htmlspecialchars($post['post'])); ?>
                                </p>
                                <footer>
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $post['timespan']; ?> ago</small>
                                    <?php
                                    if ($post['num_likes'] > 0) {
                                        print "<span> &middot; </span><a href='" . base_url("post/likes/{$post['post_id']}") . "'>{$post['num_likes']}";
                                        print ($post['num_likes'] == 1) ? " like" : " likes";
                                        print "</a>";
                                    }

                                    if ($post['num_comments'] > 0) {
                                        print "<span> &middot; </span><a href='" . base_url("post/comments/{$post['post_id']}") . "'>{$post['num_comments']}";
                                        print ($post['num_comments'] == 1) ? " comment" : " comments";
                                        print "</a>";
                                    }

                                    if ($post['num_shares'] > 0) {
                                        print "<span> &middot; </span>" .
                                              "<a href='" . base_url("post/shares/{$post['post_id']}") . "'>{$post['num_shares']}";
                                        print ($post['num_shares'] == 1) ? " share" : " shares";
                                        print "</a>";
                                    }
                                    ?>
                                    <ul>
                                        <li>
                                        <?php if($post['liked']) {
                                            // Show nothing for now.
                                        }
                                        else {
                                            print '<a href="' . base_url("post/like/{$post['post_id']}") . '" title="Like this post"><span class="glyphicon glyphicon-thumbs-up"></span> Like</a>';
                                            print("<span> &middot; </span>");
                                        }
                                        ?>
                                        </li>

                                        <li>
                                            <a href="<?php echo base_url('post/comment/' . "{$post['post_id']}"); ?>" title="Comment on this post"><span class="glyphicon glyphicon-comment"></span> Comment</a>
                                        </li>

                                        <?php if (can_share_post($_SESSION['user_id'], $post)){ ?>
                                        <li>
                                            <span> &middot; </span>
                                            <a href="<?= base_url("post/share/{$post['post_id']}"); ?>" role="button" title="Share this post">
                                                <span class="glyphicon glyphicon-share"></span> Share
                                            </a>
                                        </li>
                                        <?php } else { ?>
                                        <!-- Show nothing for now -->
                                        <?php } ?>
                                    </ul>
                                    <form action="<?= base_url("post/comment/{$post['post_id']}"); ?>" method="post" accept-charset="utf-8" role="form">
                                        <input type="text" name="comment" placeholder="Write a comment..." class="fluid">
                                    </form>
                                </footer>
                            </article>
                        </div><!-- box -->

                        <?php if (count($comments) > 0): ?>
                        <div class="box">
                            <h4>Comments</h4>
                            <ul class="comments">
                                <?php foreach($comments as $comment): ?>
                                <li>
                                    <article class="comment">
                                        <header>
                                            <a href="<?= base_url("user/index/{$comment['commenter_id']}"); ?>"><strong><?= $comment['commenter']; ?></strong></a>
                                        </header>
                                        <p class="comment"><?= htmlspecialchars($comment['comment']); ?></p>
                                        <footer>
                                            <?php
                                            print "<small>&mdash; <span class='glyphicon glyphicon-time'></span> {$comment['timespan']} ago</small>";

                                            // Hide these two links from the commenter if she is the one currently
                                            // viewing this page.
                                            if ($comment['commenter_id'] != $_SESSION['user_id']) {
                                                print "<span> &middot; </span>" .
                                                      "<a href='" . base_url("comment/like/{$post['post_id']}/{$comment['comment_id']}") . "'>Like</a>" .
                                                      "<span> &middot; </span>" .
                                                      "<a href='" . base_url("comment/reply/{$comment['comment_id']}") . "'>Reply</a>";
                                            }

                                            if ($comment['num_likes'] > 0) {
                                                print "<span> &middot; </span>" .
                                                      "<a href='" . base_url("comment/likes/{$comment['comment_id']}") . "'>{$comment['num_likes']}";
                                                print ($comment['num_likes'] == 1) ? " like" : " likes";
                                                print "</a>";
                                            }
                                            if ($comment['num_replies'] > 0) {
                                                print "<span> &middot; </span>" .
                                                      "<a href='" . base_url("comment/replies/{$comment['comment_id']}") . "'>{$comment['num_replies']}";
                                                print ($comment['num_replies'] == 1) ? " reply" : " replies";
                                                print "</a>";
                                            }
                                            ?>
                                        </footer>
                                    </article>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box previous">
                            <a href="<?= base_url("post/comments/{$post['post_id']}/{$next_offset}"); ?>">View previous comments</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div><!-- .main-content -->
                </div><!-- .main -->

                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div><!-- .col-large -->

            <div class="col-small">
                <?php require_once("common/active-users.php"); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
