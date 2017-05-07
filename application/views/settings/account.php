<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php require_once(dirname(__FILE__) . '/../common/account-settings-nav.php'); ?>

            <div class="main-content">
                <div class="box">
                    <h4>Prefered profile name</h4>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <fieldset>
                            <div class="radio">
                                <label for="name1">
                                    <input type="radio" name="name" value="Odoch Robert" id="name1">
                                    Odoch Robert
                                </label>
                            </div>
                            <div class="radio">
                                <label for="name2">
                                    <input type="radio" name="name" value="Robert Odoch" id="name2">
                                    Robert Odoch
                                </label>
                            </div>
                        </fieldset>

                        <input type="submit" name="submit" value="Update profile name" class="btn btn-sm">
                    </form>
                </div>

                <div class="box">
                    <h4>Change password</h4>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <fieldset>
                            <div class="form-group">
                                <label for="oldpasswd">Old password:</label>
                                <input type="password" name="oldpasswd" id="oldpasswd">
                            </div>
                            <div class="form-group">
                                <label for="newpasswd1">New password:</label>
                                <input type="password" name="newpasswd1" id="newpasswd1">
                            </div>
                            <div class="form-group">
                                <label for="newpasswd2">Confirm new password:</label>
                                <input type="password" name="newpasswd2" id="newpasswd2">
                            </div>
                        </fieldset>

                        <input type="submit" name="submit" value="Update password" class="btn btn-sm">
                        <a href="<?= base_url('account/forgot-password'); ?>">I forgot my password</a>
                    </form>
                </div>

                <div class="box">
                    <h4>Change name</h4>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <fieldset>
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" name="fname" id="first-name" size="30"
                                <?php
                                if (isset($fname)) {
                                    print " value='{$fname}'";
                                }
                                if (isset($error_messages) && isset($error_messages['fname'])) {
                                    print ' class="has-error"';
                                }
                                ?>>
                                <?php
                                if (isset($error_messages) && isset($error_messages['fname'])) {
                                    print "<span class='error'>{$error_messages['fname']}</span>";
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" name="lname" id="last-name" size="30"
                                <?php
                                if (isset($lname)) {
                                    print " value='{$lname}'";
                                }
                                if (isset($error_messages) && isset($error_messages['lname'])) {
                                    print ' class="has-error"';
                                }
                                ?>>
                                <?php
                                if (isset($error_messages) && isset($error_messages['lname'])) {
                                    print "<span class='error'>{$error_messages['lname']}</span>";
                                }
                                ?>
                            </div>
                        </fieldset>

                        <input type="submit" name="submit" value="Update name" class="btn btn-sm">
                    </form>
                </div>

                <div class="box">
                    <h4>Delete account</h4>
                    <div class="media">
                        <div class="media-left">
                            <span class="glyphicon glyphicon-info-sign"></span>
                        </div>
                        <div class="media-body">
                            <p class="media-heading">
                                Please be absolutely sure that you really want to delete your account
                                because
                                <span style="color: red">
                                    there is no going back once this action is performed.
                                </span>
                            </p>
                        </div>
                    </div>

                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <input type="submit" name="submit" value="Delete my account" class="btn btn-sm">
                    </form>
                </div>
