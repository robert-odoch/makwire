<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<ul class='nav nav-tabs' id='status-nav'>
    <li role='presentation' <?php if (STATUS == 'post') print "class='active'"; ?>>
        <a href='<?= base_url('post/new'); ?>' id='new-post'>
            <span class='fa fa-pencil-square-o' aria-hidden='true'></span> Post
        </a>
    </li>
    <li role='presentation' <?php if (STATUS == 'photo') print "class='active'"; ?>>
        <a href='<?= base_url('photo/new'); ?>' id='new-photo'>
            <span class='fa fa-file-image-o' aria-hidden='true'></span> Photo
        </a>
    </li>
    <li role='presentation' <?php if (STATUS == 'link') print "class='active'"; ?>>
        <a href='<?= base_url('link/new'); ?>' id='new-link'>
            <span class='fa fa-external-link' aria-hidden='true'></span> Link
        </a>
    </li>
    <li role='presentation' <?php if (STATUS == 'video') print "class='active'"; ?>>
        <a href='<?= base_url('video/new'); ?>' id='new-video'>
            <span class='fa fa-film' aria-hidden='true'></span> Video
        </a>
    </li>
</ul>
