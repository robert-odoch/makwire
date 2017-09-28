<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
require_once(__DIR__ . '/../common/video.php');

if (count($comments) > 0) {
    $object = 'video';
    require_once(__DIR__ . '/../common/comments.php');
}
?>
