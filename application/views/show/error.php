<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class='wrap-single'>
    <div class='main' role='main'>
        <div class='box'>
            <h4>
                <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                <?= $heading; ?>
            </h4>
            <div class='alert alert-danger' role='alert'>
                <p><?= $message; ?></p>
            </div>
        </div>
