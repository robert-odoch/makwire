<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<?php
switch ($object) {
    case 'comment':
    case 'reply':
        require_once(dirname(__FILE__) . '/common/comment-or-reply.php');
    default:
        # do nothing...
        break;
}
?>

<div class='box'>
    <h4>More options</h4>
    <div class='list-group'>
        <a href='<?php echo base_url("{$object}/edit/{$$object['comment_id']}"); ?>' class='list-group-item'>
            Edit <?php echo $object; ?>
        </a>
        <a href='<?php echo base_url("{$object}/delete/{$$object['comment_id']}"); ?>' class='list-group-item'>
            Delete <?php echo $object; ?>
        </a>
    </div>
</div>
