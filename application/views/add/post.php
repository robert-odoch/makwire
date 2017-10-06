<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div id='update-status' class='box'>

    <?php
    define('STATUS', 'post');
    require_once(__DIR__ . '/../common/status-nav.php');
    require_once(__DIR__ . '/../forms/new-post.php');
    ?>
</div>
