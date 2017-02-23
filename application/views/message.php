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
    <?php
    if (count($messages) > 0) {
        if (isset($has_prev)) { ?>
            <i><a href="<?= base_url("user/send-message/{$suid}/{$prev_offset}"); ?>">
                View previous messages.
            </a></i>
    <?php
        } // $has_prev.
    ?>
        <ul class="messages bordered">
            <?php foreach ($messages as $m): ?>
            <li>
                <article class="message">
                    <header>
                        <a href="<?= base_url("user/index/{$m['sender_id']}"); ?>"><strong><?= $m['sender']; ?></strong></a>
                    </header>
                    <p><?= htmlspecialchars($m['message']); ?></p>
                    <footer>
                        <small class="time"><span class="glyphicon glyphicon-time"></span> <?= $m['timespan']; ?> ago</small>
                    </footer>
                </article>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php } // count($messages) > 0 ?>
</div><!-- box -->
<?php if ($has_next): ?>
<div class="box previous">
    <a href="<?= base_url("user/send-message/{$suid}/{$next_offset}"); ?>">View more messages</a>
</div>
<?php endif; ?>
