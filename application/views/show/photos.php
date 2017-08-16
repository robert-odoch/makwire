<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'photos');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
require_once(dirname(__FILE__) . '/../common/secondary-user-nav.php');
?>

<?php if (count($photos) == 0): ?>
    <div class='box'>
        <div class='alert alert-info'>
            <p>No photos to show.</p>
        </div>
    </div>
<?php else:
    foreach ($photos as $photo) {
        require(dirname(__FILE__) . '/../common/photo.php');
    }
endif;
?>

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/photos/{$user_id}/{$next_offset}"); ?>'>View more photos</a>
    </div>
<?php } ?>
