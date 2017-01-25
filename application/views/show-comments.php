<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
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
                                <p>
                                    <?php
                                    print htmlspecialchars($post['post']);
                                    if ($post['has_more']) {
                                        print "<a href='" . base_url("user/post/{$post['author_id']}/{$post['post_id']}") . "' class='more'>view more</a>";
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
                                </footer>
                            </article>
                        </div><!-- box -->
                        <div class="box">
                            <h4>Comments</h4>
                            <?php if (count($comments) == 0): ?>
                            <div class="alert alert-info">
                                <p>No comments to show.</p>
                            </div>
                            <?php else: ?>
                            <ul class="comments">
                                <?php foreach($comments as $comment): ?>
                                <li>
                                    <article class="comment">
                                        <header>
                                            <a href="<?= base_url("user/index/{$comment['commenter_id']}"); ?>"><strong><?php print $comment['commenter']; ?></strong></a>
                                        </header>
                                        <p class="comment"><?= htmlspecialchars($comment['comment']); ?></p>
                                        <footer>
                                            <?php
                                            print "<small>&mdash; <span class='glyphicon glyphicon-time'></span> {$comment['timespan']} ago</small>";

                                            // Hide these two links from the commenter if she is the one currently
                                            // viewing this page.
                                            if (($comment['commenter_id'] != $_SESSION['user_id'])) {
                                                if (!$comment['liked']) {
                                                    print("<span> &middot; </span>" .
                                                          "<a href='" . base_url("comment/like/{$comment['comment_id']}/{$post['post_id']}") . "'>Like</a>");
                                                }

                                                print("<span> &middot; </span>" .
                                                      "<a href='" . base_url("comment/reply/{$comment['comment_id']}") . "'>Reply</a>");
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
                            <?php endif; ?>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box previous">
                            <a href="<?= base_url("post/comments/{$post['post_id']}/{$next_offset}"); ?>">View previous comments</a>
                        </div>
                        <?php endif; ?>
                    </div><!-- .main-content -->
                </div><!-- main -->

                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>
            <div class="col-small">
                <?php require_once("common/active-users.php"); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
