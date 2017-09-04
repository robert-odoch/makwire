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
            if ($prev_offset != 0) {
                $url .= "/{$prev_offset}";
            }

            print "<a href='" . base_url($url) . "' class='previous'>Show previous messages</a>";
        }
    ?>
    <div class='messages'>
        <?php foreach ($messages as $m) { ?>
        <article class='media message'>
            <div class='media-body'>
                <header>
                    <h4 class='media-heading'>
                        <a href='<?= base_url("user/send-message/{$m['sender_id']}"); ?>'
                            title='<?= $m['sender']; ?>'>
                            <?= $m['sender']; ?>
                        </a>
                        <small class='time'>
                            <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                            <?= $m['timespan']; ?> ago
                        </small>
                    </h4>
                </header>
                <p><?= htmlspecialchars($m['message']); ?></p>
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
