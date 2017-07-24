<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4>Request admin to add country</h4>

    <?php if (isset($error_message)) { ?>
    <div class='alert alert-danger'>
        <p>
            <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
            <span class='sr-only'>Error: </span>
            <?= "{$error_message}"; ?>
        </p>
    </div>
    <?php } else { ?>
        <p>
            Please fill and submit this form.
        </p>
    <?php } ?>

    <form action='<?= base_url("request-admin/add-country"); ?>' method='post'
            accept-charset='utf-8' role='form'>
        <fieldset>
            <div class='form-group'>
                <label for='country'>Country</label>
                <input type='text' name='country' id='country' size='30' required>
            </div>
        </fieldset>
        <input type='submit' value='Submit' class='btn btn-sm'>
    </form>
</div><!-- box -->
