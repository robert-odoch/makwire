<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
?>
                        <div class="box">
                            <article id="reply">
                                <header>
                                    <h4><a href=""><?= $reply['replier']; ?></a></h4>
                                </header>
                                <p><?= htmlspecialchars($reply['comment']); ?></p>
                                <footer>
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $reply['timespan']; ?> ago</small>
                                    <?php
                                    if ($reply['num_likes'] > 0) {
                                        print "<span> &middot; </span><a href='" . base_url("reply/likes/{$reply['comment_id']}") . "'>{$reply['num_likes']}";
                                        print ($reply['num_likes'] == 1) ? ' like' : ' likes';
                                        print '</a>';
                                    }
                                    ?>
                                </footer>
                            </article>
                        </div><!-- box -->
                        <div class="box">
                            <h4>Likes</h4>
                            <?php if (count($likes) == 0): ?>
                            <div class="alert alert-info">
                                <p>No likes to show.</p>
                            </div>
                            <?php else: ?>
                            <ul class="likes">
                                <?php foreach($likes as $like): ?>
                                <li>
                                    <figure><img src="<?= base_url('images/kasumba.jpg'); ?>" alt="<?= $like['liker']; ?>"></figure>
                                    <span><a href="<?= base_url("user/index/{$like['liker_id']}"); ?>"><?= $like['liker']; ?></a></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box more">
                            <a href="<?= base_url("comment/likes/{$comment['comment_id']}/{$next_offset}"); ?>">View previous likes</a>
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
