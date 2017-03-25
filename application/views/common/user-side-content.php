<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="side-content">
    <aside>
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
                <?php
                if (!defined('PAGE') || PAGE === 'timeline' || $is_visitor) {
                    print '<li><a href="' . base_url("user/profile") . '">Edit Profile</a></li>' .
                            '<li><a href="' . base_url("user/news-feed") . '">News feed</a></li>' .
                            '<li><a href="' . base_url("user/photos") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups") . '">Groups</a></li>' .
                            '<li><a href="' . base_url('settings/account') . '">Settings</a></li>';
                }
                else {
                    switch (PAGE) {
                    case 'profile':
                    print '<li><a href="' . base_url("user/profile") . '" class="active">Edit Profile</a></li>' .
                            '<li><a href="' . base_url("user/news-feed") . '">News feed</a></li>' .
                            '<li><a href="' . base_url("user/photos") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups") . '">Groups</a></li>' .
                            '<li><a href="' . base_url('settings/account') . '">Settings</a></li>';
                        break;
                    case 'news-feed':
                    print '<li><a href="' . base_url("user/profile") . '">Edit Profile</a></li>' .
                            '<li><a href="' . base_url("user/news-feed") . '" class="active">News feed</a></li>' .
                            '<li><a href="' . base_url("user/photos") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups") . '">Groups</a></li>' .
                            '<li><a href="' . base_url('settings/account') . '">Settings</a></li>';
                        break;
                    case 'photos':
                    print '<li><a href="' . base_url("user/profile") . '">Edit Profile</a></li>' .
                            '<li><a href="' . base_url("user/news-feed") . '">News feed</a></li>' .
                            '<li><a href="' . base_url("user/photos") . '" class="active">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups") . '">Groups</a></li>' .
                            '<li><a href="' . base_url('settings/account') . '">Settings</a></li>';
                        break;
                    case 'friends':
                    print '<li><a href="' . base_url("user/profile") . '">Edit Profile</a></li>' .
                            '<li><a href="' . base_url("user/news-feed") . '">News feed</a></li>' .
                            '<li><a href="' . base_url("user/photos") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends") . '" class="active">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups") . '">Groups</a></li>' .
                            '<li><a href="' . base_url('settings/account') . '">Settings</a></li>';
                        break;
                    case 'groups':
                    print '<li><a href="' . base_url("user/profile") . '">Edit Profile</a></li>' .
                            '<li><a href="' . base_url("user/news-feed") . '">News feed</a></li>' .
                            '<li><a href="' . base_url("user/photos") . '">Photos</a></li>' .
                            '<li><a href="' . base_url("user/friends") . '">Friends</a></li>' .
                            '<li><a href="' . base_url("user/groups") . '" class="active">Groups</a></li>' .
                            '<li><a href="' . base_url('settings/account') . '">Settings</a></li>';
                        break;
                    default:
                        # do noting...
                        break;
                    }
                }
                ?>
            </ul>
        </nav>
    </aside>
</div>
