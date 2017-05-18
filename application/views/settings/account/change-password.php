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
                    <h4>Change password</h4>
                    <form action="<?= base_url('account/change-password'); ?>" method="post" accept-charset="utf-8" role="form">
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

                        <input type="submit" name="submit" value="Update" class="btn btn-sm">
                    </form>
                </div>
