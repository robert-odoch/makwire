<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <div class='alert alert-success' role='alert'>
        <p>
            <span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span>
            <span class='sr-only'>Success: </span>
            <?= $message; ?>
        </p>
    </div>
</div>
