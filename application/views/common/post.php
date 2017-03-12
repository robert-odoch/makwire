<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="box">
    <article class="post">
        <header>
            <div class="media">
                <div class="media-left">
                    <img src="<?= $post['profile_pic_path']; ?>" alt="" class="media-object">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                    <?php
                    if ($post['shared']) {
                        print "<a href='" . base_url("user/{$post['sharer_id']}") . "'>{$post['sharer']}</a> shared <a href='" . base_url("user/index/{$post['user_id']}") . "'>{$post['author']}</a>'s post";
                        print('<small class="time"><span class="glyphicon glyphicon-time"></span> ' . $post['timespan'] .  ' ago</small>');
                    }
                    else {
                        print "<a href='" . base_url("user/{$post['user_id']}") . "'>{$post['author']}</a>";
                        print('<small class="time"><span class="glyphicon glyphicon-time"></span> ' . $post['timespan'] .  ' ago</small>');
                    }
                    ?>
                    </h4>
                </div>
            </div>
        </header>
        <p class="post">
            <?php
            print(htmlspecialchars($post['post']));
            if ($post['has_more']) {
                print "<a href='" . base_url("user/post/{$post['post_id']}") . "' class='more'>view more</a>";
            }
            ?>
        </p>
        <?php
        $object = 'post';
        require("media-footer.php");
        ?>
    </article>
</div>
