<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id='secondary-user' class='box'>
    <div>
        <div class='media'>
            <div class='media-left'>
                <?php if ($suid == $_SESSION['user_id']) { ?>
                <a href='<?= base_url('profile/change-profile-picture'); ?>' title='Change profile picture'>
                    <img src='<?= $su_profile_pic_path; ?>' alt="<?= $secondary_user; ?>">
                </a>
                <?php } else { ?>
                <img src='<?= $su_profile_pic_path; ?>' alt="<?= $secondary_user; ?>">
                <?php } ?>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$suid}"); ?>'><?= $secondary_user; ?></a>
                </h4>
                <div class='btn-group btn-group-xs'>
                    <?php
                    if ($friendship_status['are_friends']) {
                        print "<a href='" . base_url("user/send-message/{$suid}") .
                                "' class='btn btn-xs btn-default'>
                                <span class='glyphicon glyphicon-envelope'></span> Message</a>

                                <a href='" . base_url("user/unfollow/{$suid}") .
                                "' class='btn btn-xs btn-default' title='unfollow'>Unfollow</a>

                                <a href='" . base_url("user/unfriend/{$suid}") .
                                "' class='btn btn-xs btn-default' title='unfriend'>Unfriend</a>";
                    }
                    elseif ($friendship_status['fr_sent'] &&
                            $friendship_status['target_id'] == $_SESSION['user_id']) {
                        print "<a href='" . base_url("user/accept-friend/{$suid}") .
                                "' class='btn btn-xs'>Confirm Friend</a>";
                    }
                    elseif ($friendship_status['fr_sent'] &&
                            $friendship_status['user_id'] == $_SESSION['user_id']) {
                        print "<span class='btn btn-xs btn-default'>
                                <span class='glyphicon glyphicon-ok' aria-hidden='true'></span>
                                Request Sent</span>";
                    }
                    elseif ($suid != $_SESSION['user_id']) {
                        print "<a href='" . base_url("user/add-friend/{$suid}") .
                                "' class='btn btn-xs'>
                                <span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span> Add Friend</a>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <nav>
        <ul>
            <li>
                <a href='<?= base_url("user/{$suid}"); ?>'
                    <?php if (PAGE == 'timeline') print " class='active'"; ?>>Timeline</a>
            </li>
            <li>
                <a href='<?= base_url("user/profile/{$suid}"); ?>'
                    <?php if (PAGE == 'profile') print " class='active'"; ?>>Profile</a>
            </li>
            <li>
                <a href='<?= base_url("user/photos/{$suid}"); ?>'
                    <?php if (PAGE == 'photos') print " class='active'"; ?>>Photos</a>
            </li>
            <li>
                <a href='<?= base_url("user/friends/{$suid}"); ?>'
                    <?php if (PAGE == 'friends') print " class='active'"; ?>>Friends</a>
            </li>
            <li>
                <a href='<?= base_url("user/groups/{$suid}"); ?>'
                    <?php if (PAGE == 'groups') print " class='active'"; ?>>Groups</a>
            </li>
        </ul>
    </nav>
</div>
