<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');

switch ($object) {
    case 'post':
        require_once(__DIR__ . '/../common/post.php');
        break;
    case 'photo':
        require_once(__DIR__ . '/../common/photo.php');
        break;
    case 'video':
        require_once(__DIR__ . '/../common/video.php');
        break;
    case 'link':
        require_once(__DIR__ . '/../common/link.php');
        break;
    case 'comment':
        require_once(__DIR__ . '/../common/comment-or-reply.php');
        break;
    default:
        # Do nothing.
        break;
}

require_once(__DIR__ . '/../common/comments.php');
?>
