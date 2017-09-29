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
    <div class='likes hidden'>
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
        <?php
        foreach($likes as $like) {
            require(__DIR__ . '/../common/like.php');
        }
        ?>
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
