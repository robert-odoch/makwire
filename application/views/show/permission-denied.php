<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class="box">
    <div class="alert alert-danger">
        <p>
            <span class="glyphicon glyphicon-alert"></span> <?= $message; ?>
        </p>
    </div>
</div>
