<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../common/user-page-start.php');
require_once(dirname(__FILE__) . '/../common/photo.php');

if (count($comments) > 0) {
    $object = 'photo';
    require_once(dirname(__FILE__) . '/../common/comments.php');
}
?>
