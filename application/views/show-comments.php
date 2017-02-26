<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");

switch ($object) {
    case 'post':
        require_once("common/post.php");
        break;
    case 'photo':
        require_once("common/photo.php");
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
