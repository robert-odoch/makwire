<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Friends Requests</h4>
                            <?php if (count($friend_requests) == 0): ?>
                            <div class="alert alert-info">
                                <p>No friend requests to show.</p>
                            </div>
                            <?php else: ?>
                            <ul class="friends">
                                <?php foreach($friend_requests as $fr): ?>
                                <li>
                                    <figure><img src="<?= base_url('images/kasumba.jpg'); ?>" alt="<?= $fr['name']; ?>"></figure>
                                    <span><a href="<?= base_url("user/index/{$fr['user_id']}"); ?>"><?= $fr['name']; ?></a>
                                    <a href="<?= base_url("user/accept_friend/{$fr['user_id']}"); ?>" class="btn">Confirm</a></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div><!-- .box -->
                        <?php if ($has_next): ?>
                        <div class="box more">
                            <a href="<?= base_url("user/friend-requests/{$next_offset}") ?>">View more requests</a>
                        </div>
                        <?php endif; ?>
                    </div><!-- .main-content -->
                </div><!-- .main -->

                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>
            <div class="col-small">
                <?php require_once("common/active-users.php"); ?>
            </div>
        <span class="clearfix"></span>
        </div> <!-- #wrapper -->
