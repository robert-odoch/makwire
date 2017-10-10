<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav role='navigation' class='mobile-nav'>
    <div class='wrapper-lg nav'>
        <div class='btn-group btn-group-justified'>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href='<?= base_url('news-feed'); ?>' class='btn
                    <?php if ($page == 'news-feed') print 'active'; ?>
                    '>
                    <span class='fa fa-home' aria-hidden='true'></span>
                    <span class='sr-only'>Home</span>
                </a>
                <a href='<?= base_url('user/messages'); ?>' class='btn
                    <?php if ($page == 'messages') print 'active'; ?>
                    '>
                    <span class='fa fa-envelope' aria-hidden='true'></span>
                    <span class='sr-only'>Messages</span>
                    <?php
                    if ($num_new_messages > 0) {
                        print "<span class='badge'>{$num_new_messages}</span>";
                    }
                    ?>
                </a>
                <a href='<?= base_url('user/notifications'); ?>' class='btn
                    <?php if ($page == 'notifications') print 'active'; ?>
                    '>
                    <span class='fa fa-bell' aria-hidden='true'></span>
                    <span class='sr-only'>Notifications</span>
                    <?php
                    if ($num_new_notifs > 0) {
                        print " <span class='badge'>{$num_new_notifs}</span>";
                    }
                    ?>
                </a>
                <a href='<?= base_url('user/friend-requests'); ?>' class='btn
                    <?php if ($page == 'friend-requests') print 'active'; ?>
                    '>
                    <span class='fa fa-user-plus' aria-hidden='true'></span>
                    <span class='sr-only'>Friend requests</span>
                    <?php
                    if ($num_friend_requests > 0) {
                        print " <span class='badge'>{$num_friend_requests}</span>";
                    }
                    ?>
                </a>
            <?php else: ?>
                <a href='<?= base_url('login'); ?>' class='btn
                    <?php if ($page == 'login') print ' active'; ?>
                    '>
                    <span class='fa fa-sign-in' aria-hidden='true'></span> Log In
                </a>
                <a href='<?= base_url('register/step-one'); ?>' class='btn
                    <?php if ($page == 'register') print ' active'; ?>
                    '>
                    <span class='fa fa-edit' aria-hidden='true'></span>Register
                </a>
            <?php endif; ?>
            <a href='<?= base_url('menu'); ?>' class='btn
                <?php if ($page == 'menu') print ' active'; ?>
                '>
                <span class='fa fa-navicon' aria-hidden='true'></span>
                <span class='sr-only'> Menu</span>
            </a>
        </div>
    </div>
</nav>
