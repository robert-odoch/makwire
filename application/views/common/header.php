<?php
defined('BASEPATH') OR exit('No direct script access allowed');
isset($page) OR $page = '';  // Default value for page.
?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title><?= $title; ?></title>

        <link rel='shortcut icon' href='<?= base_url('images/favicon.ico'); ?>' type='image/x-icon'>
        <link rel='icon' href='<?= base_url('images/favicon.ico'); ?>' type='image/x-icon'>

        <link rel='stylesheet' href='<?= base_url('styles/bootstrap.min.css'); ?>'>
        <link rel='stylesheet' href='<?= base_url('styles/font-awesome-4.7.0/css/font-awesome.min.css'); ?>'>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link rel='stylesheet' href='<?= base_url('styles/ie10-viewport-bug-workaround.css'); ?>'>

        <!-- Custom styles for this site -->
        <link href='https://fonts.googleapis.com/css?family=Ubuntu:400,400i,700,700i' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i' rel='stylesheet'>
        <link href='<?= base_url('styles/styles.css'); ?>' rel='stylesheet'>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <header>
            <nav role='navigation' class='mobile-nav visible-xs'>
                <div class='wrapper-lg'>
                    <div class='btn-group btn-group-justified'>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href='<?= base_url('news-feed'); ?>' class='btn
                                <?php if ($page == 'news-feed') print 'active'; ?>
                                '>
                                <span class='fa fa-home' aria-hidden='true'></span>
                                <span class='sr-only'>News feed</span>
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
                                <span class='fa fa-sign-in' aria-hidden='true'></span>&nbsp; Log In
                            </a>
                            <a href='<?= base_url('register/step-one'); ?>' class='btn
                                <?php if ($page == 'register') print ' active'; ?>
                                '>
                                <span class='fa fa-registered' aria-hidden='true'></span>&nbsp;Register
                            </a>
                        <?php endif; ?>
                        <a href='<?= base_url('menu'); ?>' class='btn
                            <?php if ($page == 'menu') print ' active'; ?>
                            '>
                            <span class='fa fa-navicon' aria-hidden='true'></span>
                            <span class='sr-only'>&nbsp; Menu</span>
                        </a>
                    </div>
                </div>
            </nav>
            <nav role='navigation' class='navbar navbar-inverse navbar-fixed-top'>
                <div class='wrapper-lg'>
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
                                    print "<span class='badge'>{$num_new_messages}</span>";
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
                                    print " <span class='badge'>{$num_new_notifs}</span>";
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
                                    print " <span class='badge'>{$num_friend_requests}</span>";
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
                            <a href='<?= base_url('login'); ?>'>
                                <span class='fa fa-sign-in' aria-hidden='true'></span>&nbsp; Log In
                            </a>
                        </li>
                        <?php } ?>

                        <li>
                            <a href='<?= base_url('help'); ?>'>
                                <span class='fa fa-question-circle' aria-hidden='true'></span>&nbsp; Help
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header><?= "\n"; ?>
