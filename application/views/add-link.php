<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>

    <?php
    define('STATUS', 'link');
    require_once(dirname(__FILE__) . '/common/status-nav.php');
    ?>

    <h4 class='sr-only'>Add link to a resource on another website.</h4>

    <?php if (isset($error_message)) { ?>
    <div class='alert alert-danger' role='alert'>
        <p><?= $error_message; ?></p>
    </div>
    <?php }?>

    <form action='<?= base_url('link/new'); ?>' method='post' role='form'>
        <div class='form-group'>
            <label for='link-url'>URL for link:</label>
            <input type='url' name='link_url' id='link-url' class='fluid'
                    <?php if (!empty($link_url)) print " value='{$link_url}'"; ?> required>
        </div>
        <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
    </form>
</div>
