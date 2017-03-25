<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="side-content">
    <h5>
        <span class="glyphicon glyphicon-cog"></span> Settings
    </h5>
    <nav class="user-nav" role="navigation">
        <ul>
            <li>
                <a href="<?= base_url('settings/account'); ?>">
                    Account
                </a>
            </li>
            <li>
                <a href="<?= base_url('settings/emails'); ?>">
                    Emails
                </a>
            </li>
            <li>
                <a href="<?= base_url('settings/notifications'); ?>">
                    Notifications
                </a>
            </li>
            <li>
                <a href="<?= base_url('settings/blocked-users'); ?>">
                    Blocked users
                </a>
            </li>
        </ul>
    </nav>
</div>
