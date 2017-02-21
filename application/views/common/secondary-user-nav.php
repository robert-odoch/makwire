<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="secondary-user" class="box">
    <figure>
        <img src="<?= $su_profile_pic_path; ?>" alt="<?= $secondary_user; ?>'s photo" class="profile-pic">
    </figure>
    <div>
        <a href="<?= base_url("user/index/{$suid}"); ?>"><?= $secondary_user; ?></a>
        <?php
        if ($friendship_status['are_friends']) {
            // Do nothing.
        }
        elseif ($friendship_status['fr_sent'] && $friendship_status['target_id']==$_SESSION['user_id']) {
            print("<a href='" . base_url("user/accept_friend/{$suid}") . "' class='btn'>Confirm Friend</a>");
        }
        elseif ($friendship_status['fr_sent'] && $friendship_status['user_id']==$_SESSION['user_id']) {
            print("<a href='' class='btn'>Friend Request Sent</a>");
        }
        else {
            print("<a href='" . base_url("user/add_friend/{$suid}") . "' class='btn'>Add Friend</a>");
        }
        ?>
    </div>
<?php if ($are_friends) { ?>
    <ul>
        <?php
        switch (PAGE) {
            case 'timeline':
                print('<li><a href="' . base_url("user/index/{$suid}") . '" class="active">Timeline</a></li>' .
                '<li><a href="' . base_url("user/profile/{$suid}") . '">About</a></li>' .
                '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>' .
                '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>');
                break;
            case 'about':
                print('<li><a href="' . base_url("user/index/{$suid}") . '">Timeline</a></li>' .
                '<li><a href="' . base_url("user/profile/{$suid}") . '" class="active">About</a></li>' .
                '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>' .
                '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>');
                break;
            case 'friends':
                print('<li><a href="' . base_url("user/index/{$suid}") . '">Timeline</a></li>' .
                '<li><a href="' . base_url("user/profile/{$suid}") . '">About</a></li>' .
                '<li><a href="' . base_url("user/friends/{$suid}") . '" class="active">Friends</a></li>' .
                '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>' .
                '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>');
                break;
            case 'groups':
                print('<li><a href="' . base_url("user/index/{$suid}") . '">Timeline</a></li>' .
                '<li><a href="' . base_url("user/profile/{$suid}") . '">About</a></li>' .
                '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                '<li><a href="' . base_url("user/groups/{$suid}") . '" class="active">Groups</a></li>' .
                '<li><a href="' . base_url("user/photos/{$suid}") . '">Photos</a></li>');
                break;
            case 'photos':
                print('<li><a href="' . base_url("user/index/{$suid}") . '">Timeline</a></li>' .
                '<li><a href="' . base_url("user/profile/{$suid}") . '">About</a></li>' .
                '<li><a href="' . base_url("user/friends/{$suid}") . '">Friends</a></li>' .
                '<li><a href="' . base_url("user/groups/{$suid}") . '">Groups</a></li>' .
                '<li><a href="' . base_url("user/photos/{$suid}") . '" class="active">Photos</a></li>');
                break;
            default:
                # do nothing.
                break;
        }
        ?>
    </ul>
<?php }  // ($are_friends) ?>
    <span class="clearfix"></span>
</div>
