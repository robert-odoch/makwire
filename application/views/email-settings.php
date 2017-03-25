<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php require_once('common/account-settings-nav.php'); ?>

            <div class="main-content">
                <div class="box">
                    <h4>Email</h4>
                    <h5>Primary email address</h5>
                    <p class="help-block">
                        You primary makwire email address will be used for
                        notifications related to your account.
                    </p>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <select name="primary-email" class="form-control">
                                <option value="">rodoch@cis.mak.ac.ug</option>
                                <option value="">robertelvisodoch@gmail.com</option>
                            </select>
                        </div>

                        <input type="submit" name="submit" value="Save" class="btn btn-sm">
                    </form>

                    <h5>Backup email address</h5>
                    <p class="help-block">
                        Your backup makwire email address can be used to reset
                        your password if you nolonger have access to your primary
                        email address.
                    </p>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <select name="backup-email" class="form-control">
                                <option value="">Allow all verified emails</option>
                                <option value="">Allow only primary email</option>
                                <option value="">rodoch@cis.mak.ac.ug</option>
                            </select>
                        </div>

                        <input type="submit" name="submit" value="Save" class="btn btn-sm">
                    </form>
                </div>
