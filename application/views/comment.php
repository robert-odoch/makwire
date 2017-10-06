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
    case 'video':
        require_once(__DIR__ . '/common/video.php');
        break;
    case 'link':
        require_once(__DIR__ . '/common/link.php');
        break;
    case 'comment':
        require_once("common/comment-or-reply.php");
        break;
    default:
        # do nothing.
        break;
}
?>
