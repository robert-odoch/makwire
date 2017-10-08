<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php require_once(__DIR__ . '/../common/user-page-start.php'); ?>
<?php else: ?>
    <div class='wrapper-md'>
        <div role='main' class='main'>
<?php endif; ?>

        <div class='box'>
            <h4>Menu</h4>
            <ul class='list-group'>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class='list-group-item'>
                        <a href='<?= base_url('user/chat'); ?>'>
                            <span class='fa fa-fw fa-address-book-o' aria-hidden='true'></span>
                            Chat
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('user/messages'); ?>'>
                            <span class='fa fa-fw fa-envelope-o' aria-hidden='true'></span>
                            Messages
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('user/notifications'); ?>'>
                            <span class='fa fa-fw fa-bell-o' aria-hidden='true'></span>
                            Notifications
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('user/friend-requests'); ?>'>
                            <span class='fa fa-fw fa-user-plus' aria-hidden='true'></span>
                            Friend requests
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('user/find-friends'); ?>'>
                            <span class='fa fa-fw fa-search-plus' aria-hidden='true'></span>
                            Find friends
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('user/profile'); ?>'>
                            <span class='fa fa-fw fa-pencil' aria-hidden='true'></span>
                            Edit profile
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('settings/account'); ?>'>
                            <span class='fa fa-fw fa-cog' aria-hidden='true'></span>
                            Settings
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url("user/{$_SESSION['user_id']}"); ?>'>
                            <span class='fa fa-fw fa-history' aria-hidden='true'></span>
                            Timeline
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('news-feed'); ?>'>
                            <span class='fa fa-fw fa-home' aria-hidden='true'></span>
                            Home
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class='list-group-item'>
                        <a href='<?= base_url('logout'); ?>'>
                            <span class='fa fa-fw fa-sign-out' aria-hidden='true'></span>
                            Log out
                        </a>
                    </li>
                <?php else: ?>
                    <li class='list-group-item'>
                        <a href='<?= base_url('register/step-one'); ?>'>
                            <span class='fa fa-edit' aria-hidden='true'></span>
                            Register
                        </a>
                    </li>
                    <li class='list-group-item'>
                        <a href='<?= base_url('logout'); ?>'>
                            <span class='fa fa-fw fa-sign-in' aria-hidden='true'></span>
                            Log in
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
