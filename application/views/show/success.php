<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class='wrapper-md'>
    <div class='main' role='main'>
        <div class='box'>
            <h4><?= $heading; ?></h4>
            <div class='alert alert-success' role='alert'>
                <p>
                    <span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span>
                    <span class='sr-only'>Success: </span>
                    <?= $message; ?>
                </p>
            </div>
        </div>
