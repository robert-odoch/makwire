<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="box">
    <article class="photo">
        <header>
            <div class="media">
                <div class="media-left">
                    <img src="<?= $photo['profile_pic_path']; ?>" alt="" class="media-object">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                    <?php
                    if ($photo['shared']) {
                        if ($photo['is_profile_pic']) {
                            print "<a href='" . base_url("user/{$photo['sharer_id']}") . "'>{$photo['sharer']}</a> " .
                                    "shared <a href='" . base_url("user/{$photo['user_id']}") . "'>";
                            if ($photo['author_name_ends_with_s']) {
                                print "{$photo['author']}</a>' ";
                            }
                            else {
                                print "{$photo['author']}</a>'s ";
                            }
                            print "profile picutre.";
                        }
                        else {
                            print "<a href='" . base_url("user/{$photo['sharer_id']}") . "'>{$photo['sharer']}</a> " .
                                    "shared <a href='" . base_url("user/{$photo['user_id']}") . "'>";
                            if ($photo['author_name_ends_with_s']) {
                                print "{$photo['author']}' ";
                            }
                            else {
                                print "{$photo['author']}'s ";
                            }
                            print "photo";
                        }
                        print "<small class='time'><span class='glyphicon glyphicon-time'></span> {$photo['timespan']} ago</small>";
                    }
                    else {
                        if ($photo['is_profile_pic']) {
                            print "<a href='" . base_url("user/{$photo['user_id']}") . "'>{$photo['author']}</a> " .
                                    "updated {$photo['user_gender']} profile picture.";
                        } else {
                            print "<a href='" . base_url("user/{$photo['user_id']}") . "'>{$photo['author']}</a>";
                        }
                        print "<small class='time'><span class='glyphicon glyphicon-time'></span> {$photo['timespan']} ago</small>";
                    }
                    ?>
                    </h4>
                </div>
            </div>
        </header>
        <img src="<?= $photo['web_path']; ?>" alt="<?= $photo['alt']?>">
        <?php
        $object = 'photo';
        require("media-footer.php");
        ?>
    </article>
</div>
