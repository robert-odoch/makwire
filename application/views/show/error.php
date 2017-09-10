<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php if (empty($_SESSION['user_id'])): ?>
<div class='wrapper-md'>
    <div class='main' role='main'>

<?php else:
    require_once(__DIR__ . '/../common/user-page-start.php');
endif;
?>
        <div class='box'>
            <h4>
                <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                <?= $heading; ?>
            </h4>
            <div class='alert alert-danger' role='alert'>
                <p><?= $message; ?></p>
            </div>
        </div>
