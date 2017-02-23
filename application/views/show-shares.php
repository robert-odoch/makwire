<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
require_once("common/post-or-photo.php");
?>

<div class="box">
    <h4>Shares</h4>
    <?php if (count($shares) == 0) { ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No shares to show.</p>
    </div>
    <?php } else {
        if (isset($has_prev)) {
            print("<a href='" . base_url("{$object}/shares/{$$object[$object . '_id']}/{$prev_offset}") . "'>" .
                  "View previous shares.</a>");
        }
    ?>
    <ul class="likes">
        <?php foreach($shares as $share): ?>
        <li>
            <figure><img src="<?= $share['profile_pic_path']; ?>" alt="<?= $share['sharer']; ?>"></figure>
            <span><a href="<?= base_url("user/index/{$share['sharer_id']}"); ?>"><?= $share['sharer']; ?></a></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php } // (count($shares) == 0) ?>
</div><!-- box -->
<?php if ($has_next) { ?>
<div class="box more">
    <a href="<?= base_url("{$object}/shares/{$$object[$object . '_id']}/{$next_offset}"); ?>">View more shares</a>
</div>
<?php } ?>
