<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='side-content'>
    <div>
        <div id='primary-user' class='media'>
            <div class='media-left media-middle'>
                <a href='<?= base_url("profile/change-profile-picture"); ?>' title='Change profile picture'>
                <img src='<?= $profile_pic_path; ?>' alt='<?= $primary_user; ?>'
                    class='profile-pic-xs'>
                </a>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$_SESSION['user_id']}"); ?>'>
                        <?= $primary_user; ?>
                    </a>
                </h4>
            </div>
        </div>
    </div>

    <nav class='user-nav' role='navigation'>
        <p>
            <a href='<?= base_url('settings/account'); ?>'>
                <span class='glyphicon glyphicon-cog btn btn-sm' aria-hidden='true'></span>
            </a>
            <b>Settings</b>
        </p>
        <ul>
            <li>
                <a href='<?= base_url('settings/account'); ?>'
                    <?php if (PAGE == 'account') print " class='active'"; ?>
                    >Account</a>
            </li>
            <li>
                <a href='<?= base_url('settings/emails'); ?>'
                    <?php if (PAGE == 'emails') print " class='active'"; ?>
                    >Emails</a>
            </li>
        </ul>
    </nav>
</div>
