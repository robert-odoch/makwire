<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="side-content">
        <p><span class="glyphicon glyphicon-cog btn btn-sm"></span> <b>Settings</b></p>
    <nav class="user-nav" role="navigation">
        <ul>
            <li>
                <a href="<?= base_url('settings/account'); ?>">Account</a>
            </li>
            <li>
                <a href="<?= base_url('settings/emails'); ?>">Emails</a>
            </li>
            <li>
                <a href="<?= base_url('settings/notifications'); ?>">Notifications</a>
            </li>
            <li>
                <a href="<?= base_url('settings/blocked-users'); ?>">Blocked users</a>
            </li>
        </ul>
    </nav>
</div>
