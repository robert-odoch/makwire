<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php require_once(dirname(__FILE__) . '/../../common/account-settings-nav.php'); ?>

            <div class="main-content">
                <div class="box">
                    <h4>Account settings</h4>
                    <ul class="account-settings">
                        <li><a href="<?= base_url('settings/account/change-password'); ?>">Change password</a></li>
                        <li><a href="<?= base_url('settings/account/prefered-display-name'); ?>">Prefered display name</a></li>
                        <li><a href="<?= base_url('settings/account/change-name'); ?>">Change name</a></li>
                        <li><a href="<?= base_url('settings/account/delete'); ?>">Delete account</a></li>
                    </ul>
                </div>
