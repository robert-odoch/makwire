<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
        <?php require_once('common/user-page-start.php'); ?>
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
                                    <?php
                                    print htmlspecialchars($post['post']);
                                    if ($post['has_more']) {
                                        print "<a href='" . base_url("user/post/{$user_id}/{$post['post_id']}") . "' class='more'>view more</a>";
                                    }
                                    ?>
                                </p>
                                <footer>
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $post['timespan']; ?> ago</small>
                                    <?php
                                    if ($post['num_comments'] > 0) {
                                        print "<span> &middot; </span><a href='" . base_url("post/comments/{$post['post_id']}") . "'>{$post['num_comments']}";
                                        print ($post['num_comments'] == 1) ? " comment" : " comments";
                                        print "</a>";
                                    }
                                    ?>
                                    <form action="<?= base_url("post/comment/{$post['post_id']}/{$user_id}"); ?>" method="post" accept-charset="utf-8" role="form">
                                        <input type="text" name="comment" placeholder="Write a comment..." class="fluid
                                        <?php
                                        if (array_key_exists('comment', $comment_errors)) {
                                            print ' has-error';
                                        }
                                        ?>">
                                        <?php
                                        if (array_key_exists('comment', $comment_errors)) {
                                            print "<span class='error'>{$comment_errors['comment']}</span>\n";
                                        }
                                        ?>
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
                                            <a href="<?= base_url("user/index/{$comment['commenter_id']}"); ?>">
                                                <strong><?= $comment['commenter']; ?></strong>
                                            </a>
                                        </header>
                                        <p class="comment"><?php print $comment['comment']; ?></p>
                                        <footer>
                                        <?php
                                        print "<small>&mdash; " .
                                              "<span class='glyphicon glyphicon-time'></span> {$comment['timespan']} ago" .
                                              "</small>";
                                        
                                        // Hide these two links from the commenter if she is the one currently
                                        // viewing this page.
                                        if ($comment['commenter_id'] != $_SESSION['user_id']) {
                                            print "<span> &middot; </span>" .
                                                  "<a href='" . base_url("comment/like/{$comment['comment_id']}") . "'>Like</a>" .
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
                </div><!-- main -->
                
                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>
            <div class="col-small">
                <?php require_once('common/active-users.php'); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
