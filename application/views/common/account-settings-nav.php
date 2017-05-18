<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="side-content">
    <nav class="user-nav" role="navigation">
        <p><span class="glyphicon glyphicon-cog btn btn-sm"></span> <b>Settings</b></p>
        <ul>
            <li>
                <a href="<?= base_url('settings/account'); ?>"
                    <?php if (PAGE == 'account') print ' class="active"'; ?>
                    >Account</a>
            </li>
            <li>
                <a href="<?= base_url('settings/emails'); ?>"
                    <?php if (PAGE == 'emails') print ' class="active"'; ?>
                    >Emails</a>
            </li>
            <li>
                <a href="<?= base_url('settings/notifications'); ?>"
                    <?php if (PAGE == 'notifications') print ' class="active"'; ?>
                    >Notifications</a>
            </li>
            <li>
                <a href="<?= base_url('settings/blocked-users'); ?>"
                    <?php if (PAGE == 'blocked-users') print ' class="active"'; ?>
                    >Blocked users</a>
            </li>
        </ul>
    </nav>
</div>
