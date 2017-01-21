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
                                    <small><span class="glyphicon glyphicon-time"></span> <?= $post['timespan']; ?> ago</small>
                                </header>
                                <p>
                                    <?php
                                    print $post['post'];
                                    if ($post['has_more']) {
                                        print "<a href='" . base_url("user/post/{$post['post_id']}") . "' class='more'>view more</a>";
                                    }
                                    ?>
                                </p>
                                <footer>
                                    <?php
                                    if ($post['num_shares'] > 0) {
                                        print "<a href='" . base_url("post/shares/{$post['post_id']}") . "'>{$post['num_shares']}";
                                        print ($post['num_shares'] == 1) ? " share" : " shares";
                                        print "</a>";
                                    }
                                    ?>
                                </footer>
                            </article>
                        </div><!-- box -->
                        <div class="box">
                            <h4>Shares</h4>
                            <?php if (count($shares) == 0): ?>
                            <div class="alert alert-info">
                                <p>No shares to show.</p>
                            </div>
                            <?php else: ?>
                            <ul class="likes">
                                <?php foreach($shares as $share): ?>
                                <li>
                                    <figure><img src="<?= base_url('images/kasumba.jpg'); ?>" alt="<?= $share['sharer']; ?>"></figure>
                                    <span><a href="<?= base_url("user/index/{$share['sharer_id']}"); ?>"><?= $share['sharer']; ?></a></span>
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
