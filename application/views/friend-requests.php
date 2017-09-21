<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <h4>Friends Requests</h4>
    <?php if (count($friend_requests) == 0) { ?>
        <div class='alert alert-info' role='alert'>
            <p>
                <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span>
                No friend requests to show.
            </p>
        </div>
    <?php } else { ?>
        <div class='friends'>
            <?php foreach($friend_requests as $fr) { ?>
            <div class='media'>
                <div class='media-left'>
                    <img src='<?= $fr['profile_pic_path'] ?>'
                            alt='<?= $fr['profile_name']; ?>' class='media-object profile-pic-md'>
                </div>
                <div class='media-body'>
                    <h4 class='media-heading'>
                        <a href='<?= base_url("user/{$fr['user_id']}"); ?>'><?= $fr['profile_name']; ?></a>
                    </h4>
                    <a href='<?= base_url("user/accept-friend/{$fr['user_id']}"); ?>'
                            class='btn btn-xs'>Confirm</a>
                    <a href='<?= base_url("user/delete-friend-request/{$fr['user_id']}"); ?>'
                        class='btn btn-xs btn-default'>
                        <span class='glyphicon glyphicon-trash'></span> Delete
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
</div><!-- .box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/friend-requests/{$next_offset}") ?>'>Show more friend requests</a>
    </div>
<?php } ?>
