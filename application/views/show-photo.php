<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <article class="image">
        <header>
            <h4><a href="<?= base_url("user/index/{$photo['user_id']}"); ?>"><?= $photo['author']; ?></a></h4>
        </header>
        <img src="<?= $photo['web_path']; ?>" alt="<?= "{$photo['author']}'s profile picture"; ?>">
        <footer>
            <small class="time"><span class="glyphicon glyphicon-time"></span> <?php print $photo['timespan']; ?> ago</small>
            <?php
            if ($photo['num_likes'] > 0) {
                print "<span> &middot; </span><a href='" . base_url("photo/likes/{$photo['image_id']}") . "'>{$photo['num_likes']}";
                print ($photo['num_likes'] == 1) ? ' like' : ' likes';
                print '</a>';
            }
            if ($photo['num_comments'] > 0) {
                print '<span> &middot; </span><a href="' . base_url('photo/comments/' . $photo['image_id']) . '">' . $photo['num_comments'];
                print ($photo['num_comments'] == 1) ? ' comment' : ' comments';
                print '</a>';
            }
            ?>
            <ul>
                <li>
                    <a href="<?= base_url("photo/like/{$photo['image_id']}"); ?>" title="Like this post"><span class="glyphicon glyphicon-thumbs-up"></span> Like</a>
                    <span> &middot; </span>
                </li>
                <li>
                    <a href="<?= base_url("photo/comment/{$photo['image_id']}"); ?>" title="Comment on this post"><span class="glyphicon glyphicon-comment"></span> Comment</a>
                    <span> &middot; </span>
                </li>
                <li>
                    <a href="<?= base_url("photo/share/{$photo['image_id']}"); ?>" title="Share this post">
                        <span class="glyphicon glyphicon-share"></span> Share
                    </a>
                </li>
            </ul>
            <form action="<?= base_url("photo/comment/{$photo['image_id']}"); ?>" method="post" accept-charset="utf-8" role="form">
                <input type="text" name="comment" placeholder="Write a comment..." class="fluid">
            </form>
        </footer>
    </article>
</div>
