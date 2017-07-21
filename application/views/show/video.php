<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
require_once(dirname(__FILE__) . '/../common/video.php');

if (count($comments) > 0) {
    $object = 'video';
    require_once(dirname(__FILE__) . '/../common/comments.php');
}
?>
