<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', $page);
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
if ($page !== 'news-feed') {
    require_once(dirname(__FILE__) . '/../common/secondary-user-nav.php');
}

if (!$is_visitor) {
?>
    <div class="box">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active">
                <a href="#">
                    <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Status
                </a>
            </li>
            <li role="presentation">
                <a href="<?= base_url('photo/new'); ?>">
                    <span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Photo
                </a>
            </li>
            <li role='presentation'>
                <a href='<?= base_url('link/new'); ?>'>
                    <span class='glyphicon glyphicon-link' aria-hidden='true'></span> Link
                </a>
            </li>
            <li role="presentation">
                <a href='<?= base_url('video/new'); ?>'>
                    <span class="glyphicon glyphicon-film" aria-hidden="true"></span> Video
                </a>
            </li>
        </ul>
        <form action="<?= base_url('post/new'); ?>" method="post"
            accept-charset="utf-8" role="form">
            <div class="form-group">
                <label for="post" class="sr-only">New Post</label>
                <textarea name="post" placeholder="What's new?" class="fluid
                <?php
                if (isset($post_error)) {
                    print ' has-error';
                }
                ?>
                " required></textarea>
                <?php
                if (isset($post_error)) {
                    print "<span class='error'>{$post_error}</span>";
                }
                ?>
            </div>

            <input type="submit" value="Post" class="btn btn-sm">
        </form>
    </div>
<?php } // (!$is_visitor) ?>

    <?php
    if (empty($posts_and_photos) && $is_visitor) { ?>
        <div class="box">
            <div class="alert alert-info" role="alert">
                <p>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    No posts to show.
                </p>
            </div>
        </div>
    <?php
    } else {
        foreach($posts_and_photos as $p) {
            if ($p['source_type'] == 'post') {
                $post = $p['post'];
                require(dirname(__FILE__) . '/../common/post.php');
            } elseif ($p['source_type'] == 'photo') {
                $photo = $p['photo'];
                require(dirname(__FILE__) . '/../common/photo.php');
            }
        }
        if ($has_next) {
            print '<div class="box more">';
            if ($page == 'news-feed') {
                print '<a href="' . base_url("user/news-feed/{$next_offset}") .
                        '">View more stories</a>';
            }
            else if ($page == 'timeline') {
                print '<a href="' . base_url("user/{$user_id}/{$next_offset}") .
                        '">View more posts</a>';
            }
            print '</div>';
        }
    }
    ?>
