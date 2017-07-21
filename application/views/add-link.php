<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <div class='panel panel-success'>
        <div class='panel-heading'>Add link to a resource (news, post, etc) from another website</div>
        <div class='panel-body'>
            <?php if (isset($error)) { ?>
            <div class='alert alert-danger' role='alert'>
                <?= $error; ?>
            </div>
            <?php } ?>
            <form action='<?= base_url('video/new'); ?>' method='post' role='form'>
                <div class='form-group'>
                    <label for='link-url'>URL for link:</label>
                    <input type='url' name='link_url' id='link-url' class='fluid' required>
                </div>
                <input type='submit' name='submit' value='Submit' class='btn btn-sm'>
            </form>
        </div>
    </div>
</div>
