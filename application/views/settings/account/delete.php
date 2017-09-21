<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class='wrapper-lg'>
    <div class='col-large'>
        <div role='main' class='main user-home'>
            <?php
            define('PAGE', 'account');
            require_once(dirname(__FILE__) . '/../../common/account-settings-nav.php');
            ?>

            <div class='main-content'>
                <div class='box'>
                    <h4>Delete account</h4>
                    <p>
                        Please be absolutely sure that you really want to delete your account
                        because
                        <span style='color: red'>
                            there is no way we can help you recover your account once this action is performed.
                        </span>
                    </p>

                    <div class='alert alert-warning' role='alert'>
                        <span class='glyphicon glyphicon-warning-sign' aria-hidden='true'></span>
                        <p>Are you sure you want to delete your account?</p>
                        <form action='<?= base_url('account/delete'); ?>' method='post'
                                accept-charset='utf-8' role='form'>
                            <input type='submit' name='submit' value='Delete' class='btn btn-sm'>
                            <a href='<?php echo base_url("user/{$_SESSION['user_id']}"); ?>' class='btn btn-sm btn-default'>Cancel</a>
                        </form>
                    </div>
                </div>
