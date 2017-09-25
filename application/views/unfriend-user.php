<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/common/user-page-start.php');
?>

<div class='box'>
    <div class='alert alert-warning' role='alert'>
        <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
        <p>Are you sure you want to unfriend this user?</p>
        <form action='<?= $form_action; ?>' method='post'>
            <input type='submit' value='Unfriend' class='btn btn-sm'>
            <a href='<?= $cancel_url; ?>' class='btn btn-sm btn-default'>Cancel</a>
        </form>
    </div>
</div>
