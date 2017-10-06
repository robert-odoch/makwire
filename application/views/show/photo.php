<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
require_once(__DIR__ . '/../common/photo.php');

if (count($comments) > 0) {
    $object = 'photo';
    require_once(__DIR__ . '/../common/comments.php');
}
?>
