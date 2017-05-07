<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <nav id="user-nav-mobile">
                <ul>
                    <li>
                        <a href="<?= base_url('user/chat/'); ?>">Chat
                            <?php
                            if($num_active_friends > 0) {
                                print " ({$num_active_friends})";
                            }
                            ?>
                        </a>
                    </li>
                    <span> &middot; </span>

                    <li>
                        <a href="<?= base_url('user/messages/'); ?>">Messages
                            <?php
                            if ($num_new_messages > 0) {
                                print " ({$num_new_messages})";
                            }
                            ?>
                        </a>
                    </li>
                    <span> &middot; </span>

                    <li>
                        <a href="<?= base_url('user/notifications/'); ?>">Notifications
                            <?php
                            if ($num_new_notifs > 0) {
                                print " ({$num_new_notifs})";
                            }
                            ?>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php require_once('user-side-content.php'); ?>
            <div class="main-content">
