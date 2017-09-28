<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class='wrapper-lg'>
    <div class='col-large'>
        <div role='main' class='main user-home'>
            <?php
            define('PAGE', 'account');
            require_once(__DIR__ . '/../../common/account-settings-nav.php');
            ?>

            <div class='main-content'>
                <div class='box'>
                    <h4>Change password</h4>
                    <form action='<?= base_url('account/change-password'); ?>'
                            method='post' accept-charset='utf-8' role='form'>
                        <fieldset>
                            <div class='form-group'>
                                <label for='oldpasswd'>Old password:</label>
                                <input type='password' name='oldpasswd' id='oldpasswd'
                                    <?php
                                    if (isset($error_messages) && isset($error_messages['oldpasswd']))
                                        print " class='has-error'";
                                    ?> required>
                                <?php
                                if (isset($error_messages) && isset($error_messages['oldpasswd']))
                                    print "<span class='error'>{$error_messages['oldpasswd']}</span>";
                                ?>
                            </div>
                            <div class='form-group'>
                                <label for='passwd1'>New password:</label>
                                <span class='help-block'>
                                    Must be atleast 6 characters long.<br>
                                    Include atleast one uppercase letter, a lowercase letter, and a number.
                                </span>
                                <input type='password' name='passwd1' id='passwd1'
                                <?php
                                if (isset($error_messages) && isset($error_messages['passwd1'])) {
                                    print " class='has-error'";
                                }
                                ?> required>
                                <?php
                                if (isset($error_messages) && isset($error_messages['passwd1'])) {
                                    print "<span class='error'>{$error_messages['passwd1']}</span>";
                                }
                                ?>
                            </div>
                            <div class='form-group'>
                                <label for='passwd2'>Confirm new password:</label>
                                <input type='password' name='passwd2' id='passwd2'
                                <?php
                                if (isset($error_messages) && isset($error_messages['passwd2'])) {
                                    print " class='has-error'";
                                }
                                ?> required>
                                <?php
                                if (isset($error_messages) && isset($error_messages['passwd2'])) {
                                    print "<span class='error'>{$error_messages['passwd2']}</span>";
                                }
                                ?>
                            </div>
                        </fieldset>

                        <input type='submit' name='submit' value='Save' class='btn btn-sm'>
                    </form>
                </div>
