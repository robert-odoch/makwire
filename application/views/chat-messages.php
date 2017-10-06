<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
if ($has_prev) {
    echo "<a href='" . base_url("user/send-message/{$receiver['user_id']}/{$prev_offset}") .
            "' class='previous'>Show previous messages</a>";
}
?>

<?php
foreach ($messages as $message) {
    require(__DIR__ . '/chat-message.php');
}
?>
