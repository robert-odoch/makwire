<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
require_once("common/photo.php");

if (count($comments) > 0) {
    $object = 'photo';
    require_once("common/comments.php");
}
?>
