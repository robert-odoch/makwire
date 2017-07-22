<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <?php
    if (count($messages) > 0) {
        if (isset($has_prev)) {
            print "<a href='" . base_url("user/send-message/{$suid}/{$prev_offset}") .
                    "'>View previous messages</a>";
        }
    ?>
        <div class='messages'>
            <?php foreach ($messages as $m) { ?>
            <article class='media message'>
                <div class='media-body'>
                    <header>
                        <h4 class='media-heading'>
                            <a href='<?= base_url("user/{$m['sender_id']}"); ?>'>
                                <strong><?= $m['sender']; ?></strong>
                            </a>
                        </h4>
                    </header>
                    <p><?= htmlspecialchars($m['message']); ?></p>
                    <footer>
                        <small class='time'>
                            <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                            <?= $m['timespan']; ?> ago
                        </small>
                    </footer>
                </div>
            </article>
            <?php } ?>
        </div>
    <?php }  // (count($messages) > 0) ?>


        <form action='<?= base_url("user/send-message/{$suid}"); ?>' method='post'
                accept-charset='utf-8' role='form' id='send-message'>
            <fieldset>
                <div class='form-group'>
                    <label for='message' class='sr-only'>New Message</label>
                    <input type='text' name='message' id='message' placeholder='Your message...'
                        class='fluid
                    <?php
                    if (isset($message_error)) {
                        print ' has-error';
                    }
                    ?>' required>
                    <?php
                    if (isset($message_error)) {
                        print "<span class='error'>{$message_error}</span>";
                    }
                    ?>
                </div>
            </fieldset>

            <input type='submit' value='Send' class='btn btn-sm'>
        </form>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box previous'>
        <a href='<?= base_url("user/send-message/{$suid}/{$next_offset}"); ?>'>
            View more messages
        </a>
    </div>
<?php } ?>
