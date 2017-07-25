<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/common/user-page-start.php');
?>

<div class='box'>
    <div class='alert alert-warning'>
        <p>
            <?php if ($item_owner_id != $_SESSION['user_id']) { ?>
            Deleting this <?= $item; ?> will only remove it from your timeline,
            the original <?= $item; ?> will not be deleted.&nbsp;
            <?php } ?>
            Are you sure you want to delete this <?= $item; ?>?
        </p>
        <form action='<?= $form_action; ?>' method='post'>
            <input type='submit' value='Delete' class='btn btn-sm'
                    style='background-color: red; border: 1px solid red;'>
            <a href='<?= $cancel_url; ?>' class='btn btn-sm'>Cancel</a>
        </form>
    </div>
</div>

<?php
switch ($item) {
case 'post':
    require_once(dirname(__FILE__) . '/common/post.php');
    break;
case 'photo':
    require_once(dirname(__FILE__) . '/common/photo.php');
    break;
case 'video':
    require_once(dirname(__FILE__) . '/common/video.php');
    break;
case 'link':
    require_once(dirname(__FILE__) . '/common/link.php');
    break;
default:
    # do nothing...
    break;
}
?>
