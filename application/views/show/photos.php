<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'photos');
require_once(__DIR__ . '/../common/user-page-start.php');
require_once(__DIR__ . '/../common/secondary-user-nav.php');
?>

<?php
if (count($photos) == 0) {
    print "<div class='box'>";
    show_message('No photos to show.', 'info');
    print "</div>";
}
else {
    foreach ($photos as $photo) {
        require(__DIR__ . '/../common/photo.php');
    }
}
?>

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/photos/{$user_id}/{$next_offset}"); ?>'>View more photos</a>
    </div>
<?php } ?>
