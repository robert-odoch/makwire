<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Chat</h4>
                            <?php if (count($chat_users) == 0): ?>
                            <div class="alert alert-info">
                                <p>None of your friends are on chat at this moment.</p>
                            </div>
                            <?php else: ?>
                            <ul class="chat-friends">
                                <?php foreach ($chat_users as $cu): ?>
                                <li>
                                    <figure>
                                        <img src="<?= base_url("images/kasumba.jpg"); ?>" alt="<?= $cu['display_name']; ?>">
                                    </figure>
                                    <span><a href="<?= base_url("user/send-message/{$cu['friend_id']}"); ?>"><?= $cu['display_name']; ?></a> <span class="logged-in"></span></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div><!-- .box -->
                        <?php if ($has_next): ?>
                        <div class="box more">
                            <a href="<?= base_url("user/chat/{$next_offset}"); ?>">View more friends</a>
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
