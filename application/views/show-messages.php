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
            <a href="<?= base_url("user/messages/{$prev_offset}"); ?>">
                View previous messages.
            </a>
        <?php } ?>
    <div class="messages">
        <?php foreach ($messages as $m): ?>
        <article class="media message">
            <div class="media-body">
                <header>
                    <h4 class="media-heading">
                        <a href="<?= base_url("user/{$m['sender_id']}"); ?>"
                            title="<?= $m['sender']; ?>">
                            <?= $m['sender']; ?>
                        </a>
                    </h4>
                </header>
                <p><?= htmlspecialchars($m['message']); ?></p>
                <footer>
                    <small class="time"><span class="glyphicon glyphicon-time"></span> <?= $m['timespan']; ?> ago</small>
                    <span> &middot; </span><a href="<?= base_url("user/send-message/{$m['sender_id']}"); ?>" title="Reply">Reply</a>
                </footer>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div><!-- box -->
<?php
if ($has_next) {
    print '<div class="box more previous">' .
            '<a href="' . base_url("user/messages/{$next_offset}") . '">View ';
    if (isset($older)) {
        print 'older ';
    }
    else {
        print 'more ';
    }
    print 'messages</a> ' .
            '</div>';
}
?>
