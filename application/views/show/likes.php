<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');

switch ($object) {
    case 'post':
        require_once(__DIR__ . '/../common/post.php');
        break;
    case 'photo':
        require_once(__DIR__ . '/../common/photo.php');
        break;
    case 'video':
        require_once(__DIR__ . '/../common/video.php');
        break;
    case 'link':
        require_once(__DIR__ . '/../common/link.php');
        break;
    case 'comment':
    case 'reply':
        require_once(__DIR__ . '/../common/comment-or-reply.php');
        break;
    case 'message':
        require_once(__DIR__ . '/../common/birthday-message.php');
        break;
    default:
        // Do nothing.
        break;
}
?>

<div class='box'>
    <h4>Likes</h4>
    <?php if (count($likes) == 0) { ?>
    <div class='alert alert-info' role='alert'>
        <span class='fa fa-info-circle' aria-hidden='true'></span>
        <p>No likes to show.</p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            $url = "$object/likes/{$$object[$object . '_id']}";
            if ($prev_offset != 0) {
                $url .= "/{$prev_offset}";
            }

            print "<a href='" . base_url($url) . "' class='previous'>Show previous likes</a>";
        }
    ?>
    <div class='likes'>
        <?php foreach($likes as $like) { ?>
        <div class='media separated'>
            <div class='media-left'>
                <img src='<?= $like['profile_pic_path']; ?>'
                    alt='<?= $like['liker']; ?>' class='media-object profile-pic-sm'>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$like['liker_id']}"); ?>'>
                        <strong><?= $like['liker']; ?></strong>
                    </a>
                </h4>
                <small class='time'>
                    <span class='fa fa-clock-o' aria-hidden='true'></span>
                    <?= $like['timespan']; ?> ago
                </small>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("{$object}/likes/{$$object[$object . '_id']}/{$next_offset}"); ?>'>
            Show more likes
        </a>
    </div>
<?php } ?>
