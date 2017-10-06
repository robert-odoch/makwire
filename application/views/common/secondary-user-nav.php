<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id='secondary-user' class='box'>
    <div>
        <div class='media'>
            <div class='media-left'>
                <?php if ($suid == $_SESSION['user_id']) { ?>
                <a href='<?= base_url('profile/change-profile-picture'); ?>' data-toggle='tooltip'
                        data-placement='right' title='Change profile picture'>
                    <img src='<?= $su_profile_pic_path; ?>' alt='<?= $secondary_user; ?>' class='profile-pic-lg'>
                </a>
                <?php } else { ?>
                <img src='<?= $su_profile_pic_path; ?>' alt='<?= $secondary_user; ?>' class='profile-pic-lg'>
                <?php } ?>
            </div>
            <div class='media-body'>
                <h4><a href='<?= base_url("user/{$suid}"); ?>'><?= $secondary_user; ?></a></h4>
                <div class='btn-group btn-group-xs'>
                    <?php
                    if ($suid != $_SESSION['user_id']) {
                        if ($friendship_status['are_friends']) {
                            print "<a href='" . base_url("user/send-message/{$suid}") .
                                    "' class='btn btn-xs btn-default send-message'>
                                    <span class='fa fa-envelope-o'></span> Message</a>";

                            if ($following) {
                                print "<a href='" . base_url("user/unfollow/{$suid}") .
                                        "' class='btn btn-xs btn-default' title='unfollow'>
                                        <span class='fa fa-eye-slash'></span> Unfollow</a>";
                            }
                            else {
                                print "<a href='" . base_url("user/follow/{$suid}") .
                                        "' class='btn btn-xs btn-default' title='follow'>
                                        <span class='fa fa-eye'></span> Follow</a>";
                            }

                            print "<a href='" . base_url("user/unfriend/{$suid}") .
                                    "' class='btn btn-xs btn-default' title='unfriend'>
                                    <span class='fa fa-user-times'></span> Unfriend</a>";
                        }
                        elseif ($friendship_status['fr_sent']) {
                            if ($friendship_status['target_id'] == $_SESSION['user_id']) {
                                print "<a href='" . base_url("user/accept-friend/{$suid}") .
                                        "' class='btn btn-xs'>
                                        <span class='fa fa-user-plus' aria-hidden='true'></span>
                                        Confirm Friend</a>";
                            }
                            if ($friendship_status['user_id'] == $_SESSION['user_id']) {
                                print "<span class='btn btn-xs btn-default'>
                                        <span class='fa fa-check-circle-o' aria-hidden='true'></span>
                                        Request Sent</span>";
                            }

                            print "<a href='" . base_url("user/delete-friend-request/{$suid}") . "'
                                        class='btn btn-xs btn-default'>
                                    <span class='fa fa-trash'></span> Delete Request
                                    </a>";
                        }
                        else {
                            print "<a href='" . base_url("user/add-friend/{$suid}") .
                                        "' class='btn btn-xs'>
                                        <span class='fa fa-user-plus' aria-hidden='true'></span>
                                        Add Friend
                                    </a>";
                        }
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
        </ul>
    </nav>
</div>
