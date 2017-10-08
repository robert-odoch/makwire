<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav role='navigation' class='user-nav'>
    <ul>
        <li>
            <a href='<?= base_url('user/profile'); ?>'
                <?php
                if ($page == 'profile' && $suid == $_SESSION['user_id'])
                    print " class='active'";
                ?>>Edit profile</a>
        </li>
        <li>
            <a href='<?= base_url('settings/account'); ?>'>Settings</a>
        </li>
    </ul>
</nav>
