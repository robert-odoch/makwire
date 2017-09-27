<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php require_once(__DIR__ . '/../common/user-page-start.php'); ?>
<?php else: ?>
    <div class='wrapper-md'>
        <div role='main' class='main'>
<?php endif; ?>

        <div class='box'>
            <h4>Menu</h4>
            <div class='list-group'>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href='<?= base_url('user/chat'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-address-book-o'></span>&nbsp;
                        Chat
                    </a>
                    <a href='<?= base_url('settings/account'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-cog'></span>&nbsp;
                        Settings
                    </a>
                    <a href='<?= base_url('user/profile'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-pencil'></span>&nbsp;
                        Edit profile
                    </a>
                    <a href='<?= base_url('user/find-friends'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-search-plus'></span>&nbsp;
                        Find friends
                    </a>
                    <a href='<?= base_url('news-feed'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-feed'></span>&nbsp;
                        News feed
                    </a>
                    <a href='<?= base_url('user/notifications'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-bell'></span>&nbsp;
                        Notifications
                    </a>
                    <a href='<?= base_url('user/messages'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-envelope-o'></span>&nbsp;
                        Messages
                    </a>
                    <a href='<?= base_url('user/friend-requests'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-user'></span>&nbsp;
                        Friend requests
                    </a>
                <?php endif; ?>

                <a href='<?= base_url('help'); ?>' class='list-group-item'>
                    <span class='fa fa-fw fa-question-circle'></span>&nbsp;
                    Help
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href='<?= base_url('logout'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-sign-out'></span>&nbsp;
                        Log out
                    </a>
                <?php else: ?>
                    <a href='<?= base_url('logout'); ?>' class='list-group-item'>
                        <span class='fa fa-fw fa-sign-in'></span>&nbsp;
                        Log in
                    </a>
                <?php endif; ?>
            </div>
        </div>
