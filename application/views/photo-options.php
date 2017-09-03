<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<?php
switch ($object) {
    case 'post':
    case 'photo':
    case 'video':
    case 'link':
        require_once(__DIR__ . "/common/{$object}.php");
        break;
    default:
        # do nothing...
        break;
}
?>

<div class='box'>
    <h4>Options</h4>
    <div class='list-group'>
        <a href='<?php echo base_url("photo/download/{$photo['photo_id']}"); ?>' class='list-group-item'>
            Download
        </a>

        <?php if (!$photo['is_curr_profile_pic']): ?>
            <a href='<?php echo base_url("photo/make-profile-picture/{$photo['photo_id']}"); ?>' class='list-group-item'>
                Use as profile picture
            </a>
        <?php endif; ?>
    </div>
</div>
