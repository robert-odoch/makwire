<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='media separated'>
    <div class='media-left'>
        <img src='<?= $like['profile_pic_path']; ?>'
            alt='<?= $like['liker']; ?>' class='media-object profile-pic-sm'>
    </div>
    <div class='media-body'>
        <h4 class='media-heading'>
            <a href='<?= base_url("user/{$like['liker_id']}"); ?>'>
                <strong><?= $like['liker']; ?></strong>
            </a>
        </h4>
        <small class='time'>
            <span class='fa fa-clock-o' aria-hidden='true'></span>
            <?= $like['timespan']; ?> ago
        </small>
    </div>
</div>
