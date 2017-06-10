<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php
            define('PAGE', 'emails');
            require_once(dirname(__FILE__) . '/../common/account-settings-nav.php');
            ?>

            <div class="main-content">
                <div class="box">
                    <h4>Emails</h4>
                    <h5>Primary email address</h5>
                    <form action="<?= base_url('settings/emails'); ?>" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <p class="help-block">
                                You primary makwire email address will be used for
                                communications related to your account.
                            </p>
                            <select name="primary-email" class="form-control">
                                <?php foreach ($emails as $e): ?>
                                    <option value="<?= $e['email']; ?>"><?= $e['email']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="submit" value="Save" class="btn btn-sm">
                    </form>

                    <?php if (count($emails) > 1): ?>
                    <h5>Backup email address</h5>
                    <form action="<?= base_url('settings/emails'); ?>" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <p class="help-block">
                                Your backup makwire email address can be used if you no
                                longer have access to your primary email address.
                            </p>
                            <select name="backup-email" class="form-control">
                                <?php
                                foreach ($emails as $e) {
                                    if ( ! $e['is_primary']) {
                                        print "<option value='{$e['email']}'>{$e['email']}</option>";
                                    }
                                }
                                ?>
                                <option value="all">Allow all verified emails</option>
                                <option value="none">Allow only primary email</option>
                            </select>
                        </div>

                        <input type="submit" value="Save" class="btn btn-sm">
                    </form>
                    <?php endif; ?>

                    <h5>Add new email address</h5>
                    <?php if (isset($info_message)): ?>
                    <div class="alert alert-info">
                        <p><?= $info_message; ?></p>
                    </div>

                    <?php elseif (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <p><?= $success_message; ?></p>
                    </div>

                    <?php else: ?>
                    <form action="<?= base_url('settings/emails'); ?>" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <label for="email" class="sr-only">Email address</label>
                            <p class="help-block">Other emails like Gmail, Yahoo!, Outlook, etc are allowed.</p>
                            <input type="email" name="email" id="email"
                            <?php if (isset($error_message)) print ' class="has-error"'; ?>>
                            <?php if (isset($error_message)) print "<span class='error'>{$error_message}"; ?>
                        </div>

                        <input type="submit" value="Submit" class="btn btn-sm">
                    </form>
                    <?php endif; ?>
                </div>
