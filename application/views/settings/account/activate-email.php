<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class='wrapper-md'>
    <div role='main' class='main'>
        <div class='box'>
            <h4>Activate your email address</h4>
            <?php
            if ( ! empty($info_message)) {
                show_message($info_message, 'info');
            }
            else {
            ?>
                <p>Please remind me of your email address.</p>
                <form action='<?= $form_action; ?>' method='post'
                        accept-charset='utf-8' role='form'>
                    <fieldset>
                        <div class='form-group'>
                            <label for='email'>Email Address:</label>
                            <input type='email' name='email' id='email' class='fluid
                                    <?php
                                    if (isset($error_message)) {
                                        print ' has-error';
                                    }
                                    ?>'
                                    <?php
                                    if (isset($email)) {
                                        print " value = '{$email}' ";
                                    }
                                    ?> required>
                            <?php
                            if (isset($error_message)) {
                                print "<span class='error'>{$error_message}</span>";
                            }
                            ?>
                        </div>
                    </fieldset>
                    <input type='submit' value='Confirm' class='btn btn-sm'>
                </form>
            <?php } ?>
        </div><!-- box -->
