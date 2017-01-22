<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <form action="<?= base_url("user/send-message/{$suid}"); ?>" method="post" accept-charset="utf-8" role="form" id="reply-message">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="message" class="sr-only">New Message</label>
                                        <input type="text" name="message" id="message" placeholder="Your message..." class="fluid
                                        <?php
                                        if (array_key_exists('message', $message_errors)) {
                                            print ' has-error';
                                        }
                                        ?>">
                                        <?php
                                        if (array_key_exists('message', $message_errors)) {
                                            echo "<span class='error'>{$message_errors['message']}</span>\n";
                                        }
                                        ?>
                                    </div>
                                </fieldset>
                                <input type="submit" value="Send" class="btn">
                            </form>
                            <ul class="messages bordered">
                                <?php foreach ($messages as $m): ?>
                                <li>
                                    <article class="message">
                                        <header>
                                            <a href="<?= base_url("user/index/{$m['sender_id']}"); ?>"><strong><?= $m['sender']; ?></strong></a>
                                        </header>
                                        <p><?= $m['message']; ?></p>
                                        <footer>
                                            <small>&mdash; <span class="glyphicon glyphicon-time"></span> <?= $m['timespan']; ?> ago</small>
                                        </footer>
                                    </article>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box more previous">
                            <a href="<?= base_url("user/send-message/{$suid}/{$next_offset}"); ?>">View previous messages</a>
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
