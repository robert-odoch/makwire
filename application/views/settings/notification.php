<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php
            define('PAGE', 'notifications');
            require_once(dirname(__FILE__) . '/../common/account-settings-nav.php');
            ?>

            <div class="main-content">
                <div class="box">
                    <h4>Notifications</h4>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <div class="form-group">
                            <label for="participating">
                                <input type="checkbox" name="participating" id="participating" checked>
                                Participating
                            </label>
                            <span class="help-block">
                                You will be notified when your friends reply to a comment you also
                                replied to, or comment on posts of your friends that you commented
                                on or shared.
                            </span>
                        </div>

                        <input type="submit" name="submit" value="Save" class="btn btn-sm">
                    </form>
                </div>
