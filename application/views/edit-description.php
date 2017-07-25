<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('common/user-page-start.php');
?>

<div id='add-description' class='box'>
    <h4>Say something about this <?= $item; ?></h4>
    <?php if ($item == 'photo') { ?>
        <img src='<?= $photo['web_path']; ?>'>
    <?php } elseif ($item == 'video') { ?>
        <div class='embed-responsive embed-responsive-16by9'>
            <iframe class='embed-responsive-item' src='<?= $video['url']; ?>'></iframe>
        </div>
    <?php } elseif ($item == 'link') { ?>
        <div class='panel panel-default link-panel'>
            <?php if (strlen($link['image']) != 0) { ?>
            <div class='panel-body'>
                <img src='<?= $link['image']; ?>' alt="" class='link-image'>
            </div>
            <?php } ?>

            <div class='panel-footer'>
                <a href='<?= $link['url']; ?>' target='_blank'>
                    <h5 class='link-title'><?= $link['title']; ?></h5>
                    <span class='link-site'><?= $link['site']; ?></span>
                </a>
            </div>
        </div>
    <?php } ?>

    <form action='<?= $form_action; ?>' method='post' accept-charset='utf-8' role='form'>
        <textarea name='description' placeholder='Your say...' id='description' class='fluid
            <?php if (isset($error_message)) print ' has-error'; ?>'
            required autofocus><?= $description; ?></textarea>
        <?php if (isset($error_message)) { ?>
            <span class='error'><?= $error_message; ?></span>
        <?php } ?>
        <input type='submit' value='Save' class='btn btn-sm'>
        <a href='<?= $cancel_url; ?>' class='btn btn-sm btn-default'>Cancel</a>
    </form>
</div>
