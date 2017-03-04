<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Friends Requests</h4>
    <?php if (count($friend_requests) == 0): ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No friend requests to show.</p>
    </div>
    <?php else: ?>
    <div class="friends">
        <?php foreach($friend_requests as $fr): ?>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $fr['profile_pic_path'] ?>"
                alt="<?= $fr['name']; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/index/{$fr['user_id']}"); ?>"><?= $fr['name']; ?></a>
                </h4>
                <a href="<?= base_url("user/accept-friend/{$fr['user_id']}"); ?>"
                    class="btn btn-xs">Confirm</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div><!-- .box -->
<?php if ($has_next): ?>
<div class="box more">
    <a href="<?= base_url("user/friend-requests/{$next_offset}") ?>">View more requests</a>
</div>
<?php endif; ?>
