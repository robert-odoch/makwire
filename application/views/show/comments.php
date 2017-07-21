<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');

switch ($object) {
    case 'post':
        require_once(dirname(__FILE__) . '/../common/post.php');
        break;
    case 'photo':
        require_once(dirname(__FILE__) . '/../common/photo.php');
        break;
    case 'video':
        require_once(dirname(__FILE__) . '/../common/video.php');
        break;
    case 'link':
        require_once(dirname(__FILE__) . '/../common/link.php');
        break;
    case 'comment':
        require_once(dirname(__FILE__) . '/../common/comment-or-reply.php');
        break;
    default:
        # Do nothing.
        break;
}

require_once(dirname(__FILE__) . '/../common/comments.php');
?>
