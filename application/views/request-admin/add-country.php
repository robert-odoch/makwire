<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4>Request admin to add country</h4>

    <?php
    if ( ! empty($error_message)) {
        show_message($error_message, 'danger');
    }
    else {
        print "<p>Please fill and submit this form.</p>";
    }
    ?>

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
