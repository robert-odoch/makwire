<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div class="box">
    <?php if (!empty($friend_request)): ?>
        <h4>Friend request</h4>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $friend_request['profile_pic_path'] ?>"
                    alt="<?= $friend_request['profile_name']; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$friend_request['user_id']}"); ?>">
                        <?= $friend_request['profile_name']; ?>
                    </a>
                </h4>
                <span class='btn btn-xs btn-default'>
                    <span class="glyphicon glyphicon-ok-circle"></span> Confirmed
                </span>
            </div>
        </div>
    <?php else: ?>
        <h4>Friends Requests</h4>
        <?php if (count($friend_requests) == 0) { ?>
            <div class="alert alert-info" role="alert">
                <p>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    No friend requests to show.
                </p>
            </div>
        <?php } else { ?>
            <div class="friends">
                <?php foreach($friend_requests as $fr) { ?>
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="<?= $fr['profile_pic_path'] ?>"
                            alt="<?= $fr['profile_name']; ?>">
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <a href="<?= base_url("user/{$fr['user_id']}"); ?>"><?= $fr['profile_name']; ?></a>
                        </h4>
                        <a href="<?= base_url("user/accept-friend/{$fr['user_id']}"); ?>"
                            class="btn btn-xs">Confirm</a>
                        <a href='<?= base_url("user/delete-friend-request/{$fr['user_id']}"); ?>'
                            class='btn btn-xs btn-default'>
                            <span class='glyphicon glyphicon-trash'></span> Delete request
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php endif; ?>
</div><!-- .box -->

<?php if ($has_next) { ?>
    <div class="box more">
        <a href="<?= base_url("user/friend-requests/{$next_offset}") ?>">View more requests</a>
    </div>
<?php } ?>
