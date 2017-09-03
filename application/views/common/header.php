<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link rel='stylesheet' href='<?= base_url('styles/ie10-viewport-bug-workaround.css'); ?>'>

        <!-- Custom styles for this site -->
        <link href='https://fonts.googleapis.com/css?family=Ubuntu:400,400i,700,700i' rel='stylesheet'>
        <link href='<?= base_url('styles/styles.css'); ?>' rel='stylesheet'>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <header>
            <nav role='navigation' class='navbar navbar-inverse navbar-fixed-top'>
                <div class='wrapper-lg'>
                    <div class='navbar-header'>
                        <button type='button' class='navbar-toggle collapsed' data-toggle='collapse'
                            data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
                            <span class='sr-only'>Toggle navigation</span>
                            <span class='icon-bar'></span>
                            <span class='icon-bar'></span>
                            <span class='icon-bar'></span>
                        </button>
                        <a class='navbar-brand' href='<?= base_url(); ?>' title='Go to Makwire home'>Makwire</a>
                    </div>
                    <div id='navbar' class='navbar-collapse collapse'>
                        <ul class='nav navbar-nav navbar-right'>
                            <?php
                            if (isset($_SESSION['user_id'])) { ?>
                            <li class='hidden-lg'>
                                <a href='<?= base_url('user/chat'); ?>'>
                                    <span class='glyphicon glyphicon-signal' aria-hidden='true'></span>
                                    Chat
                                    <?php
                                    if ($num_active_friends > 0) {
                                        print "<span class='badge'>{$num_active_friends}</span>";
                                    }
                                    ?>
                                </a>
                            </li>
                            <li>
                                <a href='<?= base_url('user/messages'); ?>'>
                                    <span class='glyphicon glyphicon-envelope' aria-hidden='true'></span>
                                    Messages
                                    <?php
                                    if ($num_new_messages > 0) {
                                        print "<span class='badge'>{$num_new_messages}</span>";
                                    }
                                    ?>
                                </a>
                            </li>
                            <li>
                                <a href='<?= base_url('user/notifications'); ?>'>
                                    <span class='glyphicon glyphicon-bell' aria-hidden='true'></span>
                                    Notifications
                                    <?php
                                    if ($num_new_notifs > 0) {
                                        print " <span class='badge'>{$num_new_notifs}</span>";
                                    }
                                    ?>
                                </a>
                            </li>
                            <li>
                                <a href='<?= base_url('user/friend-requests'); ?>'>
                                    <span class='glyphicon glyphicon-user' aria-hidden='true'></span>
                                    Friends Requests
                                    <?php
                                    if ($num_friend_requests > 0) {
                                        print " <span class='badge'>{$num_friend_requests}</span>";
                                    }
                                    ?>
                                </a>
                            </li>
                            <li class='hidden-sm hidden-md hidden-lg'>
                                <a href='<?= base_url('user/find-friends'); ?>'>
                                    <span class='glyphicon glyphicon-user' aria-hidden='true'></span>
                                    Find Friends
                                </a>
                            </li>
                            <li class='hidden-sm hidden-md hidden-lg'>
                                <a href='<?= base_url('user/profile'); ?>'>
                                    <span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>
                                    Edit profile
                                </a>
                            </li>
                            <li class='hidden-sm hidden-md hidden-lg'>
                                <a href='<?= base_url('settings/account'); ?>'>
                                    <span class='glyphicon glyphicon-cog' aria-hidden='true'></span>
                                    Settings
                                </a>
                            </li>
                            <li class='hidden-sm hidden-md hidden-lg'>
                                <a href='<?= base_url('user/news-feed'); ?>'>
                                    <span class='glyphicon glyphicon-equalizer' aria-hidden='true'></span>
                                    News Feed
                                </a>
                            </li>
                            <li>
                                <a href='<?= base_url('logout'); ?>'>
                                    <span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>
                                    Log out
                                </a>
                            </li>
                            <?php } else { ?>
                            <li>
                                <a href='<?= base_url('login'); ?>'>
                                    <span class='glyphicon glyphicon-log-in' aria-hidden='true'></span>
                                    Log In
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header><?= "\n"; ?>
