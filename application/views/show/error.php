<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4>
        <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
        <?= $title; ?>
    </h4>
    <div class='alert alert-danger' role='alert'>
        <p><?= $message; ?></p>
    </div>
</div>
