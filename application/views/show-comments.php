<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");

switch ($object) {
    case 'post':
    case 'photo':
        require_once("common/post-or-photo.php");
        break;
    case 'comment':
        require_once("common/comment-or-reply.php");
        break;
    default:
        # Do nothing.
        break;
}

require_once("common/comments.php");
?>
