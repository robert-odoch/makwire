<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <article class='link'>
        <header>
            <div class='media'>
                <div class='media-left'>
                    <img src='<?= $link['profile_pic_path']; ?>' alt="" class='media-object'>
                </div>
                <div class='media-body'>
                    <h4 class='media-heading'>
                    <?php
                    if ($link['shared']) {
                        print "<a href='" . base_url("user/{$link['sharer_id']}") .
                                    "'>{$link['sharer']}</a> " .
                                    "shared <a href='" . base_url("user/{$link['user_id']}") . "'>" .
                                    format_name($link['author'], '</a>') . " link";
                    }
                    else {
                        print "<a href='" . base_url("user/{$link['user_id']}") .
                                    "'>{$link['author']}</a> " .
                                    "shared a link";
                    }
                    ?>
                    </h4>
                    <small class='time'>
                        <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                        <?= $link['timespan']; ?> ago
                    </small>
                </div>
            </div>
        </header>

        <?php
        if ($link['has_comment']) {
            print "<p>{$link['comment']}</p>";
        }
        elseif ($link['user_id'] == $_SESSION['user_id']) {
            print "<a href='" . base_url("link/add-comment/{$link['link_id']}") .
                    "'>Say something about this link</a>";
        }
        ?>

        <div class='panel panel-default link-panel'>
            <?php if (strlen($link['image']) != 0) { ?>
            <div class='panel-body'>
                <img src='<?= $link['image']; ?>' alt="" class='link-image'>
            </div>
            <?php } ?>

            <div class='panel-footer'>
                <a href='<?= $link['url']; ?>' target='_blank'>
                    <span class='link-title'><?= $link['title']; ?></span>
                    <span class='link-site'><?= $link['site']; ?></span>
                </a>
            </div>
        </div>

        <?php
        $object = 'link';
        require('media-footer.php');
        ?>
    </article>
</div>
