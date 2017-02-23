<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<article class="photo">
    <header>
        <h4>
        <?php
        if ($photo['shared']) {
            print "<a href='" . base_url("user/index/{$photo['sharer_id']}") . "'>{$photo['sharer']}</a> shared <a href='" . base_url("user/index/{$photo['user_id']}") . "'>{$photo['author']}</a>'s photo";
            print('<small class="time"><span class="glyphicon glyphicon-time"></span> ' . $photo['share_timespan'] .  ' ago</small>');
        }
        else {
            if ($photo['profile_pic']) {
                print("<a href='" . base_url("user/index/{$photo['user_id']}") . "'>{$photo['author']}</a> updated {$photo['user_gender']} profile picture.");
            } else {
                print("<a href='" . base_url("user/index/{$photo['user_id']}") . "'>{$photo['author']}</a>");
            }
            print('<small class="time"><span class="glyphicon glyphicon-time"></span> ' . $photo['timespan'] .  ' ago</small>');
        }
        ?>
        </h4>
    </header>
    <img src="<?= $photo['web_path']; ?>" alt="<?= "{$photo['author']}'s photo"; ?>">
    <?php
    $object = 'photo';
    require("post-or-photo-footer.php");
    ?>
</article>
