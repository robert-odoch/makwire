<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="wrap-single">
    <div role="main" class="main">
        <div class="box">
            <h4>Sign Up: step 3 of 3</h4>
            <form action="<?= base_url('register/step-three'); ?>" method="post"
                accept-charset="utf-8" role="form">
                <fieldset>
                    <div class="form-group">
                        <label for="user-name">Username:</label>
                        <span class="help-block">
                            Must be atleast 3 characters long<br>
                            Only letters and numbers are allowed.
                        </span>
                        <input type="text" name="uname" id="user-name" class="fluid
                        <?php
                        if (isset($error_messages) && isset($error_messages['uname'])) {
                            print ' has-error';
                        }
                        ?>"
                        <?php
                        if (isset($uname)) {
                            print " value='{$uname}'";
                        }
                        ?>>
                        <?php
                        if (isset($error_messages) && isset($error_messages['uname'])) {
                            print "<span class='error'>{$error_messages['uname']}</span>";
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="passwd1">Password:</label>
                        <span class="help-block">
                            Must be atleast 6 characters long.<br>
                            Include atleast one uppercase letter, a lowercase letter, and a number.
                        </span>
                        <input type="password" name="passwd1" id="passwd1" class="fluid
                        <?php
                        if (isset($error_messages) && isset($error_messages['passwd1'])) {
                            print ' has-error';
                        }
                        ?>">
                        <?php
                        if (isset($error_messages) && isset($error_messages['passwd1'])) {
                            print "<span class='error'>{$error_messages['passwd1']}</span>";
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="passwd2">Confirm Password:</label>
                        <input type="password" name="passwd2" id="passwd2" class="fluid
                        <?php
                        if (isset($error_messages) && isset($error_messages['passwd2'])) {
                            print ' has-error';
                        }
                        ?>">
                        <?php
                        if (isset($error_messages) && isset($error_messages['passwd2'])) {
                            print "<span class='error'>{$error_messages['passwd2']}</span>";
                        }
                        ?>
                    </div>
                </fieldset>
                <input type="submit" name="submit" value="Sign Up" class="btn btn-sm">
            </form>
        </div><!-- box -->
