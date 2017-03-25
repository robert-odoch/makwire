<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('PAGE', 'photos');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');

if ($is_visitor) {
    require_once(dirname(__FILE__) . '/../common/secondary-user-nav.php');
}
?>

<div class="box">
    <?php if (!$is_visitor) { ?>
        <h4>Photos</h4>
    <?php } ?>

    <?php if (count($photos) == 0) { ?>
        <div class="alert alert-info">
            <span class="glyphicon glyphicon-info-sign"></span> No photos to show.
        </div>
    <?php } else { ?>

    <div class="container-fluid">
        <div class="row gallery">
            <?php foreach ($photos as $photo) { ?>
            <div class="col-xs-6 col-md-3">
                <a href="<?= base_url("user/photo/{$photo['photo_id']}"); ?>" title="view photo">
                    <img src="<?= $photo['web_path']; ?>" alt="<?= $photo['alt']; ?>"
                        class="img-responsive">
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class="box more">
        <a href="<?= base_url("user/photos/{$user_id}/{$next_offset}"); ?>">View more photos</a>
    </div>
<?php } ?>
