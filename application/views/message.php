<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <?php require_once(__DIR__ . '/chat-user.php'); ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/send-message/{$suid}/{$next_offset}"); ?>'>
            Show more messages
        </a>
    </div>
<?php } ?>
