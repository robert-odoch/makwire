<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <article class='video'>
        <header>
            <div class='media'>
                <div class='media-left'>
                    <img src='<?= $video['profile_pic_path']; ?>' alt="" class='media-object'>
                </div>
                <div class='media-body'>
                    <h4 class='media-heading'>
                    <?php
                    if ($video['shared']) {
                        print "<a href='" . base_url("user/{$video['sharer_id']}") .
                                    "'>{$video['sharer']}</a> " .
                                    "shared <a href='" . base_url("user/{$video['user_id']}") . "'>" .
                                    format_name($video['author'], '</a>') . " video";
                    }
                    else {
                        print "<a href='" . base_url("user/{$video['user_id']}") .
                                    "'>{$video['author']}</a> " .
                                    "shared a video";
                    }
                    ?>
                    </h4>
                    <small class='time'>
                        <span class='glyphicon glyphicon-time' aria-hidden='true'></span>
                        <?= $video['timespan']; ?> ago
                    </small>
                </div>
            </div>
        </header>

        <?php
        if ($video['has_description']) {
            print "<p class='text'>" . nl2br($video['description']) . "</p>";
        }
        elseif ($video['user_id'] == $_SESSION['user_id']) {
            print "<a href='" . base_url("video/add-description/{$video['video_id']}") .
                    "' class='text'>Say something about this video</a>";
        }
        ?>

        <div class='embed-responsive embed-responsive-16by9'>
            <iframe class='embed-responsive-item' src='<?= $video['url']; ?>'></iframe>
        </div>

        <?php
        $object = 'video';
        require('media-footer.php');
        ?>
    </article>
</div>
