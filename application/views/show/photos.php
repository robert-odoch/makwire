<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'photos');
require_once(__DIR__ . '/../common/user-page-start.php');
require_once(__DIR__ . '/../common/secondary-user-nav.php');
?>

<?php if (count($photos) == 0): ?>
    <div class='box'>
        <div class='alert alert-info' role='alert'>
            <span class='fa fa-info-circle' aria-hidden='true'></span>
            <p>No photos to show.</p>
        </div>
    </div>
<?php else:
    foreach ($photos as $photo) {
        require(__DIR__ . '/../common/photo.php');
    }
endif;
?>

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/photos/{$user_id}/{$next_offset}"); ?>'>View more photos</a>
    </div>
<?php } ?>
