<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="secondary-user" class="box">
    <div>
        <?php if ($suid == $_SESSION['user_id']) { ?>
        <a href="<?= base_url('profile/change-profile-picture'); ?>" title="Change profile picture">
            <img src="<?= $su_profile_pic_path; ?>" alt="<?= $secondary_user; ?>">
        </a>
        <?php } else { ?>
        <img src="<?= $su_profile_pic_path; ?>" alt="<?= $secondary_user; ?>">
        <?php } ?>
        <h4>
            <a href="<?= base_url("user/{$suid}"); ?>"><?= $secondary_user; ?></a>
        </h4>

        <?php
        if ($friendship_status['are_friends']) {
            print "<button class='btn btn-xs'>" .
                    "<span class='glyphicon glyphicon-ok'></span> Friends" .
                    "</button>" .
                    "<a href='" . base_url("user/unfriend/{$suid}") .
                    "' class='btn btn-default btn-xs'>Unfriend</a>";
        }
        elseif ($friendship_status['fr_sent'] &&
                $friendship_status['target_id'] == $_SESSION['user_id']) {
            print "<a href='" . base_url("user/accept-friend/{$suid}") .
                    "' class='btn btn-xs'>Confirm Friend</a>";
        }
        elseif ($friendship_status['fr_sent'] &&
                $friendship_status['user_id'] == $_SESSION['user_id']) {
            print "<span class='btn btn-default'>" .
                    "<span class='glyphicon glyphicon-ok-circle'></span> " .
                    "Friend Request Sent</span>";
        }
        elseif ($suid != $_SESSION['user_id']) {
            print "<a href='" . base_url("user/add-friend/{$suid}") .
                    "' class='btn btn-xs'>Add Friend</a>";
        }
        ?>
        <span class="clearfix"></span>
    </div>
    <nav>
        <ul>
            <?php
            switch (PAGE) {
                case 'timeline':
                    print '<li><a href="' . base_url("user/{$suid}") . '" class="active">Timeline</a></li>' .
                            '<li><a href="' . base_url("user/profile/{$suid}") . '">Profile</a></li>' .
                            '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>';
                    break;
                case 'profile':
                    print '<li><a href="' . base_url("user/{$suid}") . '">Timeline</a></li>' .
                            '<li><a href="' . base_url("user/profile/{$suid}") . '" class="active">Profile</a></li>' .
                            '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>';
                    break;
                case 'photos':
                    print '<li><a href="' . base_url("user/{$suid}") . '">Timeline</a></li>' .
                            '<li><a href="' . base_url("user/profile/{$suid}") . '">Profile</a></li>' .
                            '<li><a href="' . base_url("user/photos/{$suid}") . '" class="active">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>';
                    break;
                case 'friends':
                    print '<li><a href="' . base_url("user/{$suid}") . '">Timeline</a></li>' .
                            '<li><a href="' . base_url("user/profile/{$suid}") . '">Profile</a></li>' .
                            '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends/{$suid}") . '" class="active">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>';
                    break;
                case 'groups':
                    print '<li><a href="' . base_url("user/{$suid}") . '">Timeline</a></li>' .
                            '<li><a href="' . base_url("user/profile/{$suid}") . '">Profile</a></li>' .
                            '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups/{$suid}") . '" class="active">Groups</a></li>';
                    break;
                default:
                    # do nothing.
                    break;
            }
            ?>
        </ul>
    </nav>
    <span class="clearfix"></span>
</div>
