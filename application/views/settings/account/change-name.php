<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php
            define('PAGE', 'account');
            require_once(dirname(__FILE__) . '/../../common/account-settings-nav.php');
            ?>

            <div class="main-content">
                <div class="box">
                    <h4>Change name</h4>
                    <form action="<?= base_url('account/change-name'); ?>" method="post" accept-charset="utf-8" role="form">
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

                        <input type="submit" name="submit" value="Update" class="btn btn-sm">
                    </form>
                </div>
