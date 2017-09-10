<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='wrapper-md'>
    <div role='main' class='main'>
        <div class='box'>
            <h4>Log In</h4>

            <?php
            if (isset($login_errors) && array_key_exists('login', $login_errors)) {
                print "<div class='alert alert-danger' role='alert'>" .
                        "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> " .
                        "{$login_errors['login']}</div>";
            }
            elseif (isset($info_message)) {
                show_message($info_message, 'info');
                unset($info_message);
            }
            ?>
            <form action='<?= base_url('login'); ?>' method='post' accept-charset='utf-8' role='form'>
                <fieldset>
                    <div class='form-group'>
                        <label for='identifier'>Username or email</label>
                        <input type='text' name='identifier' id='identifier' class='fluid
                        <?php
                        if (isset($login_errors) && array_key_exists('identifier', $login_errors)) {
                            print " has-error";
                        }
                        ?>'

                        <?php
                        if (isset($identifier)) {
                            print " value='{$identifier}'";
                        }
                        ?> required>
                        <?php
                        if (isset($login_errors) && array_key_exists('identifier', $login_errors)) {
                            print "<span class='error'>{$login_errors['identifier']}</span>\n";
                        }
                        ?>
                    </div>
                    <div class='form-group'>
                        <label for='password'>Password</label>
                        <input type='password' name='password' id='password' class='fluid
                        <?php
                        if (isset($login_errors) &&
                            array_key_exists('password', $login_errors)) {
                            print " has-error";
                        }
                        ?>' required>
                        <?php
                        if (isset($login_errors) && array_key_exists('password', $login_errors)) {
                            print "<span class='error'>{$login_errors['password']}</span>\n";
                        }
                        ?>
                        <span class='help-block'>
                            <a href='<?= base_url('account/forgot-password'); ?>'
                                    title='Recover your password'>Forgot password?</a>
                        </span>
                    </div>
                </fieldset>
                <input type='submit' name='submit' value='Log In' class='btn btn-sm'>
            </form>
            <p style='margin: 5px 0;'>
                Don't have an account?
                <a href='<?= base_url('register/step-one'); ?>' title='Create an account'>create one</a>
            </p>
        </div><!-- box -->
