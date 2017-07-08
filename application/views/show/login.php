<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrap-single">
    <div role="main" class="main">
        <div class="box">
            <?php if (isset($message)) { ?>
            <h4>Log in to continue</h4>
            <?php } else { ?>
            <h4>Log In</h4>
            <?php } ?>

            <?php
            if (isset($login_errors) && array_key_exists('login', $login_errors)) {
                print "<div class='alert alert-danger'>" .
                        "<span class='glyphicon glyphicon-exclamation-sign'></span> " .
                        "{$login_errors['login']}</div>";
            }
            elseif (isset($message)) {
                print "<div class='alert alert-info'>" .
                        "<span class='glyphicon glyphicon-info-sign'></span> " .
                        "{$message}</div>";
                unset($message);
            }
            ?>
            <form action="<?= base_url('login'); ?>" method="post" accept-charset="utf-8" role="form">
                <fieldset>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="fluid
                        <?php
                        if (isset($login_errors) && array_key_exists('username', $login_errors)) {
                            print " has-error";
                        }
                        ?>"

                        <?php
                        if (isset($username)) {
                            print " value='{$username}'";
                        }
                        ?>>
                        <?php
                        if (isset($login_errors) && array_key_exists('username', $login_errors)) {
                            print "<span class='error'>{$login_errors['username']}</span>\n";
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="fluid
                        <?php
                        if (isset($login_errors) &&
                            array_key_exists('password', $login_errors)) {
                            print " has-error";
                        }
                        ?>">
                        <?php
                        if (isset($login_errors) && array_key_exists('password', $login_errors)) {
                            print "<span class='error'>{$login_errors['password']}</span>\n";
                        }
                        ?>
                        <span class="help-block">
                            <a href="<?= base_url('support/forgot-password'); ?>" title="Recover password">Forgot password?</a>
                        </span>
                    </div>
                </fieldset>
                <input type="submit" name="submit" value="Log In" class="btn btn-sm">
            </form>
            <p style="margin: 5px 0;">
                Don't have an account?
                <a href="<?= base_url('register/step-one'); ?>" title="Create an account">create one</a>
            </p>
        </div><!-- box -->
