<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('PAGE') OR define('PAGE', '')
?>

<div class="side-content<?php
    if (in_array(PAGE, ['timeline', 'profile', 'photos', 'friends']) &&
        $suid == $_SESSION['user_id']) {
        print ' hidden-xs';
    }
?>">
    <div>
        <div id="primary-user" class="media">
            <div class="media-left media-middle">
                <a href="<?= base_url("profile/change-profile-picture"); ?>"
                    title="Change profile picture">
                <img src="<?= $profile_pic_path; ?>" alt="<?= $primary_user; ?>"
                    class="profile-pic">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$_SESSION['user_id']}"); ?>">
                        <?= $primary_user; ?>
                    </a>
                </h4>
            </div>
        </div>

        <nav role="navigation" class="user-nav hidden-xs">
            <ul>
                <li>
                    <a href="<?= base_url('user/profile'); ?>"
                        <?php
                        if (PAGE == 'profile' && $suid == $_SESSION['user_id'])
                            print ' class="active"';
                        ?>>Edit profile</a>
                </li>
                <li>
                    <a href="<?= base_url('settings/account'); ?>">Settings</a>
                </li>
            </ul>
        </nav>
    </div>

    <nav role="navigation" id="short-cuts" class="hidden-xs">
        <h5><span class="glyphicon glyphicon-console" aria-hidden="true"></span> QUICK ACCESS</h5>
        <ul>
            <li<?php if (PAGE == 'news-feed') print ' class="active"'; ?>>
                <a href="<?= base_url('user/news-feed'); ?>">News Feed</a>
            </li>
            <li<?php if (PAGE == 'find-friends') print ' class="active"'; ?>>
                <a href="<?= base_url('user/find-friends'); ?>"
                    >Find Friends</a>
            </li>
        </ul>
        <h5><span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span> SHORTCUTS</h5>
    </nav>
</div>
