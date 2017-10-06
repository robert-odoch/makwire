<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<form action='<?= base_url('photo/new'); ?>' method='post'
        enctype='multipart/form-data' role='form'>
    <div class='form-group'>
        <label for='userfile'>Choose a photo:</label>
        <input type='file' name='userfile' id='userfile' required>
    </div>
    <input type='submit' name='submit' value='Upload' class='btn btn-sm'>
</form>
