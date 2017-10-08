<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='box'>
    <article class='photo'>
        <header>
            <div class='media'>
                <div class='media-left'>
                    <img src='<?= $photo['profile_pic_path']; ?>' class='media-object profile-pic-md'>
                </div>
                <div class='media-body'>
                    <h4 class='media-heading'>
                    <?php
                    if ($photo['shared']) {
                        if ($photo['was_profile_pic']) {
                            print "<a href='" . base_url("user/{$photo['sharer_id']}") .
                                    "'>{$photo['sharer']}</a> " .
                                    "shared <a href='" . base_url("user/{$photo['user_id']}") . "'>" .
                                    format_name($photo['author'], '</a>') . " profile picutre.";
                        }
                        else {
                            print "<a href='" . base_url("user/{$photo['sharer_id']}") .
                                    "'>{$photo['sharer']}</a> " .
                                    "shared <a href='" . base_url("user/{$photo['user_id']}") . "'>" .
                                    format_name($photo['author'], '</a>') . " photo";
                        }
                    }
                    else {
                        if ($photo['was_profile_pic']) {
                            print "<a href='" . base_url("user/{$photo['user_id']}") .
                                    "'>{$photo['author']}</a> " .
                                    "updated {$photo['user_gender']} profile picture.";
                        } else {
                            print "<a href='" . base_url("user/{$photo['user_id']}") .
                                    "'>{$photo['author']}</a> " .
                                    "added a photo";
                        }
                    }
                    ?>
                    </h4>
                    <small class='time'>
                        <span class='fa fa-clock-o' aria-hidden='true'></span>
                        <?= $photo['timespan']; ?> ago
                    </small>
                </div>
            </div>
        </header>

        <?php
        if ($photo['has_description']) {
            print "<p>" . nl2br($photo['description']) . "</p>";
        }
        elseif ($photo['user_id'] == $_SESSION['user_id'] && !$photo['was_profile_pic']) {
            print "<a href='" . base_url("photo/add-description/{$photo['photo_id']}") .
                    "'>Say something about this photo</a>";
        }
        ?>
        <img src='<?= $photo['web_path']; ?>' alt="<?= $photo['alt']; ?>">
        <?php
        $object = 'photo';
        require('media-footer.php');
        ?>
    </article>
</div>
