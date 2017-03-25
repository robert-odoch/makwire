<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../common/user-page-start.php');

switch ($object) {
    case 'post':
        require_once(dirname(__FILE__) . '/../common/post.php');
        break;
    case 'photo':
        require_once(dirname(__FILE__) . '/../common/photo.php');
        break;
    case 'comment':
    case 'reply':
        require_once(dirname(__FILE__) . '/../common/comment-or-reply.php');
        break;
    default:
        // Do nothing.
        break;
}
?>

<div class="box">
    <h4>Likes</h4>
    <?php if (count($likes) == 0) { ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No likes to show.</p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            print "<a href='" . base_url("$object/likes/{$$object[$object . '_id']}/{$prev_offset}") .
                    "'>View previous likes.</a>";
        }
    ?>
    <div class="likes">
        <?php foreach($likes as $like) { ?>
        <div class="media">
            <div class="media-left">
                <img class="media-object" src="<?= $like['profile_pic_path']; ?>"
                    alt="<?= $like['liker']; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$like['liker_id']}"); ?>">
                        <strong><?= $like['liker']; ?></strong>
                    </a>
                </h4>
                <small class="time">
                    <span class="glyphicon glyphicon-time"></span> <?= $like['timespan']; ?> ago
                </small>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class="box more">
        <a href="<?= base_url("{$object}/likes/{$$object[$object . '_id']}/{$next_offset}"); ?>">
            View more likes
        </a>
    </div>
<?php } ?>
