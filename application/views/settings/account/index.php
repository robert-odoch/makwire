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
                    <ul class='list-group settings'>
                        <li class='list-group-item'>
                            <a href='<?= base_url('account/set-prefered-name'); ?>'>
                                Prefered profile name
                            </a>
                        </li>
                        <li class='list-group-item'>
                            <a href='<?= base_url('account/change-password'); ?>'>
                                Change password
                            </a>
                        </li>
                        <li class='list-group-item'>
                            <a href='<?= base_url('account/change-name'); ?>'>
                                Change name
                            </a>
                        </li>
                        <li class='list-group-item'>
                            <a href='<?= base_url('account/delete'); ?>'>
                                Delete account
                            </a>
                        </li>
                    </ul>
                </div>
