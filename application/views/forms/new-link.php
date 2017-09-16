<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form action='<?= base_url('link/new'); ?>' method='post' role='form'>
    <div class='form-group'>
        <label for='link-url'>URL for link:</label>
        <input type='url' name='link_url' id='link-url' class='fluid'
                <?php if (!empty($link_url)) print " value='{$link_url}'"; ?> required>
    </div>
    <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
</form>
