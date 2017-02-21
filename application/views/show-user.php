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

<?php if (empty($posts) && $is_visitor): ?>
    <div class="box">
        <div class="alert alert-info">
            <p><span class="glyphicon glyphicon-info-sign"></span> No previous posts.</p>
        </div>
    </div>
    <?php else:
        foreach($posts as $post): ?>
            <div class="box">
                <article class="post">
                    <header>
                        <h4>
                        <?php
                        if ($post['shared']) {
                            print "<a href='" . base_url("user/index/{$post['sharer_id']}") . "'>{$post['sharer']}</a> shared <a href='" . base_url("user/index/{$post['author_id']}") . "'>{$post['author']}</a>'s post";
                        }
                        else {
                            print "<a href='" . base_url("user/index/{$post['author_id']}") . "'>{$post['author']}</a>";
                        }
                        ?>
                        </h4>
                    </header>
                    <p class="post">
                        <?php
                        print(htmlspecialchars($post['post']));
                        if ($post['has_more']) {
                            print "<a href='" . base_url("user/post/{$post['post_id']}") . "' class='more'>view more</a>";
                        }
                        ?>
                    </p>
                    <footer>
                        <small class="time"><span class="glyphicon glyphicon-time"></span> <?php print $post['timespan']; ?> ago</small>
                        <?php
                        if ($post['num_likes'] > 0) {
                            print "<span> &middot; </span><a href='" . base_url("post/likes/{$post['post_id']}") . "'>{$post['num_likes']}";
                            print ($post['num_likes'] == 1) ? ' like' : ' likes';
                            print '</a>';
                        }
                        if ($post['num_comments'] > 0) {
                            print '<span> &middot; </span><a href="' . base_url('post/comments/' . $post['post_id']) . '">' . $post['num_comments'];
                            print ($post['num_comments'] == 1) ? ' comment' : ' comments';
                            print '</a>';
                        }
                        if ($post['num_shares'] > 0) {
                            print '<span> &middot; </span><a href="' . base_url('post/shares/' . $post['post_id']) . '">' . $post['num_shares'];
                            print ($post['num_shares'] == 1) ? ' share' : ' shares';
                            print '</a>';
                        }
                        ?>
                        <ul>
                            <li>
                                <a href="<?= base_url("post/like/{$post['post_id']}"); ?>" title="Like this post"><span class="glyphicon glyphicon-thumbs-up"></span> Like</a>
                                <span> &middot; </span>
                            </li>
                            <li>
                                <a href="<?= base_url("post/comment/{$post['post_id']}"); ?>" title="Comment on this post"><span class="glyphicon glyphicon-comment"></span> Comment</a>
                                <span> &middot; </span>
                            </li>
                            <li>
                                <a href="<?= base_url("post/share/{$post['post_id']}"); ?>" title="Share this post">
                                    <span class="glyphicon glyphicon-share"></span> Share
                                </a>
                            </li>
                        </ul>
                        <form action="<?= base_url("post/comment/{$post['post_id']}"); ?>" method="post" accept-charset="utf-8" role="form">
                            <input type="text" name="comment" placeholder="Write a comment..." class="fluid">
                        </form>
                    </footer>
                </article>
            </div><!-- box -->
        <?php endforeach; ?>
    <?php if ($has_next): ?>
    <div class="box more">
        <a href="<?= base_url("user/index/{$user_id}/{$next_offset}"); ?>">View more posts</a>
    </div>
    <?php endif; ?>
<?php endif; ?>
