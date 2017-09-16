<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<form action='<?= base_url('post/new'); ?>' method='post'
        accept-charset='utf-8' role='form'>
    <div class='form-group'>
        <label for='post' class='sr-only'>New Post</label>
        <textarea name='post' placeholder='Write something...' class='fluid
        <?php
        if (isset($post_error)) {
            print ' has-error';
        }
        ?>' required></textarea>
        <?php
        if (isset($post_error)) {
            print "<span class='error'>{$post_error}</span>";
        }
        ?>
    </div>

    <input type='submit' value='Post' class='btn btn-sm'>
</form>
