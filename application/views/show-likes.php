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
                                        print "<a href='" . base_url("user/post/{$post['post_id']}") . "' class='more'>view more</a>";
                                    }
                                    ?>
                                </p>
                                <footer>
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $post['timespan']; ?> ago</small>
                                    <?php
                                    if ($post['num_likes'] > 0) {
                                        print "<span> &middot; </span><a href='" . base_url("post/likes/{$post['post_id']}") . "'>{$post['num_likes']}";
                                        print ($post['num_likes'] == 1) ? " like" : " likes";
                                        print "</a>";
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
                            <a href="<?= base_url("post/likes/{$post['post_id']}/{$next_offset}"); ?>">View previous comments</a>
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
