<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>

    <?php
    define('STATUS', 'link');
    require_once(dirname(__FILE__) . '/common/status-nav.php');
    ?>

    <div class='panel panel-default'>
        <div class='panel-body'>
            <form action='<?= base_url('video/new'); ?>' method='post' role='form'>
                <div class='form-group'>
                    <label for='link-url'>URL for link:</label>
                    <input type='url' name='link_url' id='link-url' class='fluid
                            <?php if (isset($error_message)) print ' has-error'; ?>' required>
                    <?php
                    if (isset($error_message)) {
                        print "<span class='error'>{$error_message}</span>";
                    }?>
                </div>
                <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
            </form>
        </div>
    </div>
</div>
