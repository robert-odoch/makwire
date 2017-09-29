<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/common/user-page-start.php');
?>

<div class='box'>
    <?php
    $message = "<p>";

    if (in_array($item, ['post', 'photo', 'video', 'link'])) {
        if ($item_owner_id != $_SESSION['user_id']) {
            $message .= "Deleting this {$item} will only remove it from your timeline,
                            the original {$item} will not be deleted.<br><br>";
        }
    }

    $message .= "Are you sure you want to delete this {$item}?
                    </p>
                    <form action='{$form_action}' method='post'>
                        <input type='submit' value='Delete' class='btn btn-sm'>
                        <a href='{$cancel_url}' class='btn btn-sm btn-default'>Cancel</a>
                    </form>";
    show_message($message, 'warning', FALSE);
    ?>
</div>

<?php
switch ($item) {
case 'post':
    require_once(__DIR__ . '/common/post.php');
    break;
case 'photo':
    require_once(__DIR__ . '/common/photo.php');
    break;
case 'video':
    require_once(__DIR__ . '/common/video.php');
    break;
case 'link':
    require_once(__DIR__ . '/common/link.php');
    break;
case 'comment':
case 'reply':
    require_once(__DIR__ . '/common/comment-or-reply.php');
default:
    # do nothing...
    break;
}
?>
