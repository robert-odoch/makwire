<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='wrapper-lg'>
    <div class='col-large'>
        <div role='main' class='main user-home'>
            <?php
            define('PAGE', 'account');
            require_once(__DIR__ . '/../../common/account-settings-nav.php');
            ?>

            <div class='main-content'>
                <div class='box'>
                    <h4>Account settings</h4>
                    <div class='list-group'>
                        <a href='<?= base_url('account/set-prefered-name'); ?>' class='list-group-item'>
                            Prefered profile name
                        </a>
                        <a href='<?= base_url('account/change-password'); ?>' class='list-group-item'>
                            Change password
                        </a>
                        <a href='<?= base_url('account/change-name'); ?>' class='list-group-item'>
                            Change name
                        </a>
                        <a href='<?= base_url('account/delete'); ?>' class='list-group-item'>
                            Delete account
                        </a>
                    </ul>
                </div>
