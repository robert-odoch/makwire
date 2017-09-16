<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4>Messages</h4>
    <?php if (count($messages) == 0) { ?>
    <div class='alert alert-info' role='alert'>
        <p>
            <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span>
            No messages to show.
        </p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            $url = 'user/messages';
            $url .= ($prev_offset == 0) ? '' : "/{$prev_offset}";
            print "<a href='" . base_url($url) . "' class='previous'>Show previous messages</a>";
        }
    ?>
    <div class='messages'>
        <?php foreach ($messages as $m) { ?>
        <article class='media message'>
            <div class='media-body'>
                <p>
                    <strong class='object'>
                    <a href='<?= base_url("user/send-message/{$m['sender_id']}"); ?>'
                        title='<?= $m['sender']; ?>' class='send-message'>
                        <?= $m['sender']; ?>
                    </a>
                    </strong>
                    <?= htmlspecialchars($m['message']); ?>
                </p>
                <small class='time'>
                    <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                    <?= $m['timespan']; ?> ago
                </small>
            </div>
        </article>
        <?php } ?>
    </div>
    <?php } ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/messages/{$next_offset}"); ?>'>
            Show <?php if (isset($older)) { print 'older'; } else { print 'more'; } ?> messages
        </a>
    </div>
<?php } ?>
