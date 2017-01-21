<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
?>
                        <div class="box">
                            <article class="comment">
                                <header>
                                    <h4><a href=""><?= $comment['commenter']; ?></a></h4>
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $comment['timespan']; ?> ago</small>
                                </header>
                                <p>
                                    <?= $comment['comment']; ?>
                                </p>
                                <?php
                                if ($comment['num_likes'] > 0) {
                                    print "<a href='". base_url("comment/likes/{$comment['comment_id']}") . "'>{$comment['num_likes']}";
                                    print ($comment['num_likes'] == 1) ? " like" : " likes";
                                    print "</a>";
                                }
                                
                                if (($comment['num_likes'] > 0) && ($comment['num_replies'] > 0)) {
                                    print "<span> &middot; </span>";
                                }
                                
                                if ($comment['num_replies'] > 0) {
                                    print "<a href='". base_url("comment/replies/{$comment['comment_id']}") . "'>{$comment['num_replies']}";
                                    print ($comment['num_replies'] == 1) ? " reply" : " replies";
                                    print "</a>";
                                }
                                ?>
                                <footer>
                                    <form action="<?= base_url("comment/reply/{$comment['comment_id']}"); ?>" method="post" accept-charset="utf-8" role="form">
                                        <input type="text" name="reply" placeholder="Write a reply..." class="fluid
                                        <?php
                                        if (isset($reply_errors['reply'])) {
                                            print " has-error";
                                        }
                                        ?>">
                                        <?php
                                        if (isset($reply_errors['reply'])) {
                                            print "<span class='error'>{$reply_errors['reply']}</span>";
                                        }
                                        ?>
                                    </form>
                                </footer>
                            </article>
                        </div><!-- box -->
                        <?php if (count($replies) > 0): ?>
                        <div class="box">
                            <h4>Replies</h4>
                            <ul class="comments">
                                <?php foreach ($replies as $reply): ?>
                                <li>
                                    <a href=""><strong><?= $reply['replier']; ?></strong></a>
                                    <p><?= $reply['comment']; ?></p>
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $reply['timespan']; ?> ago</small>
                                    <?php
                                    // Hide this link from the replier if she is the one currently
                                    // viewing this page.
                                    if ($reply['commenter_id'] != $_SESSION['user_id']) {
                                        print "<span> &middot; </span>" .
                                              "<a href='" . base_url("reply/like/{$reply['comment_id']}") . "'>Like</a>";
                                    }
                                    
                                    if ($reply['num_likes'] > 0) {
                                        print "<span> &middot; </span>" .
                                              "<a href='" . base_url("reply/likes/{$reply['comment_id']}") . "'>{$reply['num_likes']}";
                                        print ($reply['num_likes'] == 1) ? " like" : " likes";
                                        print "</a>";
                                    }
                                    ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box previous">
                            <a>View previous replies</a>
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
                <?php require_once("common/active-users.php"); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
