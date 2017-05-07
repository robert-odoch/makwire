<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="side-content">
    <div>
        <div id="primary-user" class="media">
            <div class="media-left media-middle">
                <a href="<?= base_url("upload/profile-picture"); ?>" title="Change profile picture">
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

        <nav role="navigation" class="user-nav">
            <ul>
                <li><a href="<?= base_url('user/profile'); ?>">Profile</a></li>
                <li><a href="<?= base_url('settings/account'); ?>">Settings</a></li>
            </ul>
        </nav>
    </div>

    <nav role="navigation" id="short-cuts">
        <h5><span class="glyphicon glyphicon-heart"></span> Favorites</h5>
        <ul>
            <li><a href="<?= base_url('user/news-feed'); ?>">News Feed</a></li>
        </ul>
        <h5><span class="glyphicon glyphicon-bookmark"></span> Quick Access</h5>
    </nav>
</div>
