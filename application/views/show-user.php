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
        <form action="<?php echo base_url('user/new-post'); ?>" method="post" accept-charset="utf-8" role="form">
            <div class="form-group">
                <label for="post" class="hidden">New Post</label>
                <textarea name="post" placeholder="What's new?" class="fluid
                <?php
                if (array_key_exists('post', $post_errors)) {
                    print(' has-error');
                }
                ?>
                "></textarea>
                <?php
                if (array_key_exists('post', $post_errors)) {
                    echo "<span class='error'>{$post_errors['post']}</span>\n";
                }
                ?>
            </div>

            <input type="submit" value="Post" class="btn">
            <a href="attach-post-photo.html"><span class="glyphicon glyphicon-picture"></span> Add photo</a>
        </form>
    </div>
<?php } // (!$is_visitor) ?>

<?php if (empty($posts_and_photos) && $is_visitor): ?>
    <div class="box">
        <div class="alert alert-info">
            <p><span class="glyphicon glyphicon-info-sign"></span> No previous posts.</p>
        </div>
    </div>
    <?php else:
        foreach($posts_and_photos as $pp): ?>
        <div class="box">
            <?php
            if ($pp['type'] == 'post') {
                $post = $pp['post'];
                require("common/timeline-post.php");
            } elseif ($pp['type'] == 'photo') {
                $photo = $pp['photo'];
                require("common/timeline-photo.php");
            }
            ?>
        </div><!-- box -->
        <?php endforeach; ?>
    <?php if ($has_next): ?>
    <div class="box more">
        <a href="<?= base_url("user/index/{$user_id}/{$next_offset}"); ?>">View more posts</a>
    </div>
    <?php endif; ?>
<?php endif; ?>
