<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <form action='<?= base_url("post/edit/{$post['post_id']}"); ?>' method='post'
            accept-charset='utf-8' role='form'>
        <div class='form-group'>
            <label for='post' class='sr-only'>Edit post</label>
            <textarea name='post' class='fluid
                    <?php if (isset($error_message)) { print ' has-error'; } ?>'
                    required autofocus><?= trim($post['post']); ?></textarea>
            <?php
            if (isset($error_message)) {
                print "<span class='error'>{$error_message}</span>";
            }
            ?>
        </div>

        <input type='submit' value='Save' class='btn btn-sm'>
        <a href='<?= base_url("user/post/{$post['post_id']}"); ?>' class='btn btn-sm btn-default'>Cancel</a>
    </form>
</div>
