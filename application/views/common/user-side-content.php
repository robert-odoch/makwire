<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="side-content">
    <aside>
        <div id="primary-user">
            <a href="<?= base_url("upload/profile-picture"); ?>" title="Change Profile Picture">
            <img src="<?= $profile_pic_path; ?>" alt="<?= $primary_user; ?>'s photo" class="profile-pic">
            </a>
            <a href="<?= base_url("user/index/{$_SESSION['user_id']}"); ?>"><?= $primary_user; ?></a>
        </div>
        <nav role="navigation" class="user-nav">
            <ul>
                <li><a href="<?= base_url("user/profile"); ?>">Edit Profile</a></li>
                <li><a href="<?= base_url("user/friends"); ?>">Friends</a></li>
                <li><a href="<?= base_url("user/groups"); ?>">Groups</a></li>
                <li><a href="<?= base_url("user/photos"); ?>">Photos</a></li>
            </ul>
        </nav>
    </aside>
</div>
