<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <?php
    if (count($messages) > 0) {
        if (isset($has_prev)) {
            $url = "user/send-message/{$suid}";
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
                            <a href='<?= base_url("user/{$m['sender_id']}"); ?>'>
                                <strong><?= $m['sender']; ?></strong>
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
    <?php }  // (count($messages) > 0) ?>


        <form action='<?= base_url("user/send-message/{$suid}"); ?>' method='post'
                accept-charset='utf-8' role='form' id='send-message'>
            <fieldset>
                <div class='form-group'>
                    <label for='message' class='sr-only'>New Message</label>
                    <input type='text' name='message' id='message' placeholder='Your message...'
                        class='fluid <?php if (isset($message_error)) { print 'has-error'; } ?>' required>
                    <?php if (isset($message_error)) { print "<span class='error'>{$message_error}</span>"; } ?>
                </div>
            </fieldset>

            <input type='submit' value='Send' class='btn btn-sm'>
        </form>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/send-message/{$suid}/{$next_offset}"); ?>'>
            Show more messages
        </a>
    </div>
<?php } ?>
