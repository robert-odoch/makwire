<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');

if ($is_visitor) {
    define('PAGE', 'timeline');
    require_once("common/secondary-user-nav.php");
}

if (!$is_visitor) {
?>
    <div class="box">
        <form action="<?= base_url('user/new-post'); ?>" method="post"
            accept-charset="utf-8" role="form">
            <div class="form-group">
                <label for="post" class="hidden">New Post</label>
                <textarea name="post" placeholder="What's new?" class="fluid
                <?php
                if (isset($post_error)) {
                    print ' has-error';
                }
                ?>
                "></textarea>
                <?php
                if (isset($post_errors)) {
                    print "<span class='error'>{$post_error}</span>";
                }
                ?>
            </div>

            <input type="submit" value="Post" class="btn">
            <a href="attach-post-photo.html">
                <span class="glyphicon glyphicon-picture"></span> Add photo
            </a>
        </form>
    </div>
<?php } // (!$is_visitor) ?>

    <?php
    if (empty($posts_and_photos) && $is_visitor) { ?>
        <div class="box">
            <div class="alert alert-info">
                <p><span class="glyphicon glyphicon-info-sign"></span> No previous posts.</p>
            </div>
        </div>
    <?php
    } else {
        foreach($posts_and_photos as $p) {
            if ($p['source_type'] == 'post') {
                $post = $p['post'];
                require("common/post.php");
            } elseif ($p['source_type'] == 'photo') {
                $photo = $p['photo'];
                require("common/photo.php");
            }
        }
        if ($has_next) {
            print '<div class="box more">';
            if ($page == 'news-feed') {
                print '<a href="' . base_url("user/news-feed/{$next_offset}") . '">View more stories</a>';
            }
            else if ($page == 'index') {
                print '<a href="' . base_url("user/index/{$user_id}/{$next_offset}") . '">View more posts</a>';
            }
            print '</div>';
        }
    }
    ?>
