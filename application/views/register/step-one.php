<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='wrapper-md'>
    <div role='main' class='main'>
        <div class='box'>
            <h4>Sign Up: step 1 of 3</h4>
            <?php
            if ( ! empty($info_message)) {
                show_message($info_message, 'info');
            }
            elseif ( ! empty($success_message)) {
                show_message($success_message, 'success');
            }
            else {
            ?>
                <form action='<?= base_url('register/step-one'); ?>' method='post'
                        accept-charset='utf-8' role='form'>
                    <fieldset>
                        <div class='form-group'>
                            <label for='email'>Email Address:</label>
                            <span class='help-block'>
                                Your Makerere University email address
                            </span>
                            <input type='email' name='email' id='email'
                                    placeholder='you@college.mak.ac.ug' class='fluid
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
                    <input type='submit' value='Next &raquo;' class='btn btn-sm'>
                </form>
            <?php } ?>
        </div><!-- box -->
