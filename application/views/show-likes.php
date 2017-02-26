<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");

switch ($object) {
    case 'post':
        require_once("common/post.php");
        break;
    case 'photo':
        require_once("common/photo.php");
        break;
    case 'comment':
    case 'reply':
        require_once("common/comment-or-reply.php");
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
            print("<a href='" . base_url("$object/likes/{$$object[$object . '_id']}/{$prev_offset}") . "'>" .
                  "View previous likes.</a>");
        }
    ?>
    <ul class="likes">
        <?php foreach($likes as $like): ?>
        <li>
            <figure><img src="<?= $like['profile_pic_path']; ?>" alt="<?= $like['liker']; ?>"></figure>
            <span><a href="<?= base_url("user/index/{$like['liker_id']}"); ?>"><?= $like['liker']; ?></a></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php } ?>
</div><!-- box -->
<?php if ($has_next) { ?>
<div class="box more">
    <a href="<?= base_url("{$object}/likes/{$$object[$object . '_id']}/{$next_offset}"); ?>">View more likes</a>
</div>
<?php } ?>
