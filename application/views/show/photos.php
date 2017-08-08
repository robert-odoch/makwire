<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'photos');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
require_once(dirname(__FILE__) . '/../common/secondary-user-nav.php');
?>

<?php
foreach ($photos as $photo) {
    require(dirname(__FILE__) . '/../common/photo.php');
}
?>

<?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/photos/{$user_id}/{$next_offset}"); ?>'>View more photos</a>
    </div>
<?php } ?>
