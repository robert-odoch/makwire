<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');

if ($is_visitor) {
    define('PAGE', 'friends');
    require_once("common/secondary-user-nav.php");
}
?>

<div class="box">
    <?php
    if (!$is_visitor) {
        print("<h4>Friends</h4>");
    }
    ?>

    <?php if (count($friends) == 0): ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No friends to show.</p>
    </div>
    <?php else: ?>
    <div class="friends">
        <?php foreach($friends as $fr): ?>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $fr['profile_pic_path']; ?>"
                alt="<?= $fr['profile_name']; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$fr['friend_id']}"); ?>"><?= $fr['profile_name']; ?></a>
                </h4>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div><!-- .box -->
<?php if ($has_next): ?>
<div class="box more">
    <?php
    if ($is_visitor) {
        print '<a href="' . base_url("user/friends/{$suid}") . '">View more friends</a>';
    }
    else {
        print '<a href="' . base_url("user/friends/{$_SESSION['user_id']}") . '">View more friends</a>';
    }
    ?>
</div>
<?php endif; ?>
