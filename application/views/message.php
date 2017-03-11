<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <form action="<?= base_url("user/send-message/{$suid}"); ?>" method="post"
        accept-charset="utf-8" role="form" id="send-message">
        <fieldset>
            <div class="form-group">
                <label for="message" class="sr-only">New Message</label>
                <input type="text" name="message" id="message" placeholder="Your message..."
                    class="fluid
                <?php
                if (isset($message_error)) {
                    print ' has-error';
                }
                ?>">
                <?php
                if (isset($message_error)) {
                    print "<span class='error'>{$message_error}</span>";
                }
                ?>
            </div>
        </fieldset>
        <input type="submit" value="Send" class="btn btn-sm">
    </form>
    <?php
    if (count($messages) > 0) {
        if (isset($has_prev)) {
            print '<a href="' . base_url("user/send-message/{$suid}/{$prev_offset}") . '">' .
                'View previous messages</a>';
        }
    ?>
        <ul class="messages bordered">
            <?php foreach ($messages as $m): ?>
            <li>
                <article class="message">
                    <header>
                        <a href="<?= base_url("user/{$m['sender_id']}"); ?>">
                            <strong><?= $m['sender']; ?></strong>
                        </a>
                    </header>
                    <p><?= htmlspecialchars($m['message']); ?></p>
                    <footer>
                        <small class="time">
                            <span class="glyphicon glyphicon-time"></span> <?= $m['timespan']; ?> ago
                        </small>
                    </footer>
                </article>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php }  // (count($messages) > 0) ?>
</div><!-- box -->
<?php
if ($has_next) {
    print '<div class="box previous">' .
        '<a href="' . base_url("user/send-message/{$suid}/{$next_offset}") . '">View more messages</a>' .
        '</div>';
}
?>
