<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('PAGE') OR define('PAGE', '');
?>

<div class='side-content'>
    <div>
        <div id='primary-user' class='media'>
            <div class='media-left media-middle'>
                <a href='<?= base_url("profile/change-profile-picture"); ?>'
                        data-toggle='tooltip' data-placement='right' title='Change profile picture'>
                    <img src='<?= $profile_pic_path; ?>' alt='<?= $primary_user; ?>'
                        class='profile-pic-xs'>
                </a>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$_SESSION['user_id']}"); ?>'
                            data-toggle='tooltip' data-placement='bottom' title='Timeline'>
                        <?= $primary_user; ?>
                    </a>
                </h4>
            </div>
        </div>

        <!-- .user-nav goes here -->
    </div>

    <!-- #short-cuts goes here -->
</div>
