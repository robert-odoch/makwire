<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<ul class='nav nav-tabs'>
    <li role='presentation' class='active'>
        <a href='#'>
            <span class='glyphicon glyphicon-edit' aria-hidden='true'></span> Status
        </a>
    </li>
    <li role='presentation'>
        <a href='<?= base_url('photo/new'); ?>'>
            <span class='glyphicon glyphicon-picture' aria-hidden='true'></span> Photo
        </a>
    </li>
    <li role='presentation'>
        <a href='<?= base_url('link/new'); ?>'>
            <span class='glyphicon glyphicon-link' aria-hidden='true'></span> Link
        </a>
    </li>
    <li role='presentation'>
        <a href='<?= base_url('video/new'); ?>'>
            <span class='glyphicon glyphicon-film' aria-hidden='true'></span> Video
        </a>
    </li>
</ul>
