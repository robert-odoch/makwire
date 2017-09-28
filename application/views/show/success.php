<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php if (empty($_SESSION['user_id'])): ?>
<div class='wrapper-md'>
    <div class='main' role='main'>

<?php else:
    require_once(__DIR__  . '/../common/user-page-start.php');
endif;
?>

<div class='box'>
    <h4><?= $heading; ?></h4>
    <div class='alert alert-success' role='alert'>
        <span class='fa fa-check-circle' aria-hidden='true'></span>
        <span class='sr-only'>Success: </span>
        <p><?= $message; ?></p>
    </div>
</div>
