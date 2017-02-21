<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Messages</h4>
    <?php if (count($messages) == 0): ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No messages to show.</p>
    </div>
    <?php else:
        if (isset($has_prev)) { ?>
            <li>
                <a href="<?= base_url("user/messages/{$prev_offset}"); ?>">
                    View previous messages.
                </a>
            </li>
        <?php } ?>
    <ul class="messages">
        <?php foreach ($messages as $m): ?>
        <li>
            <article class="message">
                <header>
                    <a href="<?= base_url("user/index/{$m['sender_id']}"); ?>" title="<?= $m['sender']; ?>"><?= $m['sender']; ?></a>
                </header>
                <p><?= htmlspecialchars($m['message']); ?></p>
                <footer>
                    <small><span class="glyphicon glyphicon-time"></span> <i><?= $m['timespan']; ?> ago</i></small>
                    <span> &middot; </span><a href="<?= base_url("user/send_message/{$m['sender_id']}"); ?>" title="Reply">Reply</a>
                </footer>
            </article>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div><!-- box -->
<?php if ($has_next): ?>
<div class="box more previous">
    <a href="<?= base_url("user/messages/{$next_offset}"); ?>">View more messages</a>
</div>
<?php endif; ?>
