<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form action='<?= base_url("$object/comment/{$$object[$object . '_id']}"); ?>'
        method='post' class='comment' accept-charset='utf-8'>
    <input type='text' name='comment' placeholder='Write a comment...' class='fluid
    <?php
    if (isset($comment_error)) {
        print(" has-error");
    }
    ?>' required>
    <?php
    if (isset($comment_error)) {
        print("<span class='error'>{$comment_error}</span>");
    }

    if ( ! empty($page) && $page == 'comment') {
        print "<br><br><input type='submit' value='Submit' class='btn btn-sm'>";
    }
    ?>
</form>
