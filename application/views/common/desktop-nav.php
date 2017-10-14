<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav role='navigation' class='navbar navbar-inverse navbar-fixed-top'>
    <div class='wrapper-lg nav'>
        <a class='navbar-brand' href='<?= base_url('news-feed'); ?>'>Makwire</a>
        <ul class='nav navbar-nav navbar-right'>
            <?php
            if (isset($_SESSION['user_id'])) { ?>
            <li>
                <a href='<?= base_url('user/messages'); ?>'
                        data-toggle='tooltip' data-placement='bottom' title='Messages'>
                    <span class='fa fa-envelope' aria-hidden='true'></span>
                    <span class='sr-only'>Messages</span>
                    <?php
                    if ($num_new_messages > 0) {
                        print " <span class='badge messages'>{$num_new_messages}</span>";
                    }
                    else {
                        print " <span class='badge messages hidden'></span>";
                    }
                    ?>
                </a>
            </li>
            <li>
                <a href='<?= base_url('user/notifications'); ?>'
                        data-toggle='tooltip' data-placement='bottom' title='Notifications'>
                    <span class='fa fa-bell' aria-hidden='true'></span>
                    <span class='sr-only'>Notifications</span>
                    <?php
                    if ($num_new_notifs > 0) {
                        print " <span class='badge notifications'>{$num_new_notifs}</span>";
                    }
                    else {
                        print " <span class='badge notifications hidden'></span>";
                    }
                    ?>
                </a>
            </li>
            <li>
                <a href='<?= base_url('user/friend-requests'); ?>'
                        data-toggle='tooltip' data-placement='bottom' title='Friend requests'>
                    <span class='fa fa-user-plus' aria-hidden='true'></span>
                    <span class='sr-only'>Friends Requests</span>
                    <?php
                    if ($num_friend_requests > 0) {
                        print " <span class='badge friends'>{$num_friend_requests}</span>";
                    }
                    else {
                        print " <span class='badge friends hidden'></span>";
                    }
                    ?>
                </a>
            </li>
            <li>
                <a href='<?= base_url('logout'); ?>'>
                    <span class='fa fa-sign-out' aria-hidden='true'></span>&nbsp; Log out
                </a>
            </li>
            <?php } else { ?>
            <li>
                <a href='<?= base_url('register/step-one'); ?>'>
                    <span class='fa fa-edit' aria-hidden='true'></span>&nbsp; Register
                </a>
            </li>
            <li>
                <a href='<?= base_url('login'); ?>'>
                    <span class='fa fa-sign-in' aria-hidden='true'></span>&nbsp; Log In
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
</nav>
