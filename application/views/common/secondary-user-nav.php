<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div id="secondary-user" class="box">
    <div class="media">
        <div class="media-left">
            <img class="media-object" src="<?= $su_profile_pic_path; ?>"
            alt="<?= $secondary_user; ?>">
        </div>
        <div class="media-body">
            <h4 class="media-heading">
                <a href="<?= base_url("user/{$suid}"); ?>"><?= $secondary_user; ?></a>
            </h4>
            <?php
            if ($friendship_status['are_friends']) {
                print "<button class='btn btn-sm'><span class='glyphicon glyphicon-ok'></span> " .
                        "Friends</button>";
            }
            elseif ($friendship_status['fr_sent'] &&
                    $friendship_status['target_id'] == $_SESSION['user_id']) {
                print "<a href='" . base_url("user/accept-friend/{$suid}") .
                        "' class='btn btn-sm'>Confirm Friend</a>";
            }
            elseif ($friendship_status['fr_sent'] &&
                    $friendship_status['user_id'] == $_SESSION['user_id']) {
                print "<button class='btn btn-sm'>Friend Request Sent</button>";
            }
            elseif ($suid != $_SESSION['user_id']) {
                print "<a href='" . base_url("user/add-friend/{$suid}") .
                        "' class='btn btn-sm'>Add Friend</a>";
            }
            ?>
        </div>
    </div>
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
    <span class="clearfix"></span>
</div>
