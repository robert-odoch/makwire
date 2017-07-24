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
    case 'video':
        require_once(dirname(__FILE__) . '/../common/video.php');
        break;
    case 'link':
        require_once(dirname(__FILE__) . '/../common/link.php');
        break;
    default:
        # Do nothing.
        break;
}
?>

<div class='box'>
    <h4>Shares</h4>
    <?php if (count($shares) == 0) { ?>
    <div class='alert alert-info' role='alert'>
        <p>
            <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span>
            No shares to show.
        </p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            $url = "{$object}/shares/{$$object[$object . '_id']}";
            if ($prev_offset != 0) {
                $url .= "/{$prev_offset}";
            }

            print "<a href='" . base_url($url) . "' class='previous'>Show previous shares</a>";
        }
    ?>
    <div class='shares'>
        <?php foreach($shares as $share) { ?>
        <div class='media'>
            <div class='media-left'>
                <img class='media-object' src='<?= $share['profile_pic_path']; ?>'
                        alt="<?= $share['sharer']; ?>">
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$share['sharer_id']}"); ?>'>
                        <strong><?= $share['sharer']; ?></strong>
                    </a>
                </h4>
                <small class='time'>
                    <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                    <?= $share['timespan']; ?> ago
                </small>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } // (count($shares) == 0) ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("{$object}/shares/{$$object[$object . '_id']}/{$next_offset}"); ?>'>
            Show more shares
        </a>
    </div>
<?php } ?>
