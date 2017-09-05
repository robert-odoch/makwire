<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='wrapper-md'>
    <div role='main' class='main'>
        <div class='box'>
            <h4>Recover Your Password</h4>
            <?php
            if (!empty($success_message)) {
                show_message($success_message, 'success');
            }
            elseif (!empty($info_message)) {
                show_message($info_message, 'info');
            }
            else {
                if (!empty($error_message)) {
                    show_message($error_message, 'danger');
                }
            ?>

                <form action='<?php echo base_url('account/forgot-password'); ?>' method='post'
                        accept-charset='utf-8' role='form'>
                    <fieldset>
                        <div class='form-group'>
                            <label for='email'>Email Address</label>
                            <span class='help-block'>
                                Any of your <strong>makwire verified</strong> email addresses.
                                Instructions for resetting your password will be sent to this email address.
                            </span>
                            <input type='email' name='email' size='30'
                                    <?php if (!empty($email)) echo " value='{$email}'" ?> required>
                        </div>
                    </fieldset>
                    <input type='submit' value='Recover' class='btn btn-sm'>
                </form>
            <?php } ?>
        </div><!-- box -->
