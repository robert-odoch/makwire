<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Messages</h4>
                            <?php if (count($messages) == 0): ?>
                            <div class="alert alert-info">
                                <p>No messages to show.</p>
                            </div>
                            <?php else: ?>
                            <ul class="messages">
                                <?php foreach ($messages as $m): ?>
                                <li>
                                    <article class="message">
                                        <header>
                                            <a href="<?= base_url("user/send_message/{$m['sender_id']}"); ?>" title="Reply"><?= $m['sender']; ?></a>
                                        </header>
                                        <p><?= htmlspecialchars($m['message']); ?></p>
                                        <footer>
                                            <small>&mdash; <span class="glyphicon glyphicon-time"></span> <?= $m['timespan']; ?> ago</small>
                                        </footer>
                                    </article>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box more previous">
                            <a href="<?= base_url("user/messages/{$next_offset}"); ?>">View previous messages</a>
                        </div>
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
