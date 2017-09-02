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
        <a href='<?php echo base_url("{$object}/download/{$$object[$object . '_id']}"); ?>' class='list-group-item'>
            Download
        </a>
        <a href='<?php echo base_url("{$object}/make-profile-picture/{$$object[$object . '_id']}"); ?>' class='list-group-item'>
            Use as profile picture
        </a>
    </div>
</div>
