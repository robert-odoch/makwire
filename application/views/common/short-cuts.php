<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav role='navigation' id='short-cuts'>
    <h5><span class='fa fa-gg' aria-hidden='true'></span> Quick Access</h5>
    <ul>
        <li<?php if (in_array('chat', $location)) print " class='active'"; ?>>
            <a href='<?= base_url('user/chat'); ?>'>
                Chat
                <?php
                if ($num_active_friends > 0) {
                    print "<span class='badge pull-right'>{$num_active_friends}</span>";
                }
                ?>
            </a>
        </li>
        <li<?php if (in_array('news-feed', $location)) print " class='active'"; ?>>
            <a href='<?= base_url('news-feed'); ?>'>News Feed</a>
        </li>
        <li<?php if (in_array('find-friends', $location)) print ' class="active"'; ?>>
            <a href='<?= base_url('user/find-friends'); ?>'>Find Friends</a>
        </li>
    </ul>

    <?php if ( ! empty($shortcuts)): ?>
        <h5><span class='fa fa-bookmark' aria-hidden='true'></span> Shortcuts</h5>
    <?php endif; ?>
</nav>
