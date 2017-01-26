<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="secondary-user" class="box">
    <figure>
        <img src="<?= base_url("images/kasumba.jpg"); ?>" alt="<?= $secondary_user; ?>'s photo" class="profile-pic">
    </figure>
    <div>
        <a href="<?= base_url("user/index/{$suid}"); ?>"><?= $secondary_user; ?></a>
        <?php
        if ($friendship_status['friends']) {
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
    <ul>
        <li><a href="<?= base_url("user/index/{$suid}"); ?>">Timeline</a></li>
        <li><a href="<?= base_url("user/profile/{$suid}"); ?>" class="active">About</a></li>
        <li><a href="<?= base_url("user/friends/{$suid}"); ?>">Friends</a></li>
        <li><a href="">Groups</a></li>
        <li><a href="">Photos</a></li>
    </ul>
    <span class="clearfix"></span>
</div>
