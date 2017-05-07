<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
                        print "<a href='" . base_url("user/{$post['sharer_id']}") . "'>" .
                                "{$post['sharer']}</a> shared <a href='" .
                                base_url("user/index/{$post['user_id']}") .
                                "'>" . format_name($post['author'], '</a>') . " post";
                    }
                    else {
                        print "<a href='" . base_url("user/{$post['user_id']}") .
                                "'>{$post['author']}</a>";
                    }
                    ?>
                    </h4>
                    <small class="time">
                        <span class="glyphicon glyphicon-time"></span> <?= $post['timespan']; ?> ago
                    </small>
                </div>
            </div>
        </header>
        <p class="post"><?= $post['post']; ?></p>
        <?php
        $object = 'post';
        require("media-footer.php");
        ?>
    </article>
</div>
