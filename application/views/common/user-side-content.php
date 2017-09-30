<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('PAGE') OR define('PAGE', '');
isset($page) OR $page = '';
?>

<div class='side-content<?php
    if (in_array(PAGE, ['timeline', 'profile', 'photos', 'friends']) &&
        $suid == $_SESSION['user_id']) {
        print ' hidden-xs';
    }
    if ($page == 'menu') {
        print ' menu';
    }
?>'>
    <div>
        <div id='primary-user' class='media'>
            <div class='media-left media-middle'>
                <a href='<?= base_url("profile/change-profile-picture"); ?>'
                        data-toggle='tooltip' data-placement='right' title='Change profile picture'>
                    <img src='<?= $profile_pic_path; ?>' alt='<?= $primary_user; ?>'
                        class='profile-pic-xs'>
                </a>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$_SESSION['user_id']}"); ?>'
                            data-toggle='tooltip' data-placement='bottom' title='Timeline'>
                        <?= $primary_user; ?>
                    </a>
                </h4>
            </div>
        </div>

        <nav role='navigation' class='user-nav hidden-xs'>
            <ul>
                <li>
                    <a href='<?= base_url('user/profile'); ?>'
                        <?php
                        if (PAGE == 'profile' && $suid == $_SESSION['user_id'])
                            print " class='active'";
                        ?>>Edit profile</a>
                </li>
                <li>
                    <a href='<?= base_url('settings/account'); ?>'>Settings</a>
                </li>
            </ul>
        </nav>
    </div>

    <nav role='navigation' id='short-cuts' class='hidden-xs'>
        <h5><span class='fa fa-gg' aria-hidden='true'></span> Quick Access</h5>
        <ul>
            <li<?php if (PAGE == 'chat') print " class='active'"; ?>>
                <a href='<?= base_url('user/chat'); ?>'
                    >Chat
                    <?php
                    if ($num_active_friends > 0) {
                        print "<span class='badge pull-right'>{$num_active_friends}</span>";
                    }
                    ?>
                </a>
            </li>
            <li<?php if (PAGE == 'news-feed') print " class='active'"; ?>>
                <a href='<?= base_url('news-feed'); ?>'>News Feed</a>
            </li>
            <li<?php if (PAGE == 'find-friends') print ' class="active"'; ?>>
                <a href='<?= base_url('user/find-friends'); ?>'
                    >Find Friends</a>
            </li>
        </ul>

        <?php if ( ! empty($shortcuts)): ?>
            <h5><span class='fa fa-bookmark' aria-hidden='true'></span> Shortcuts</h5>
        <?php endif; ?>
    </nav>
</div>
