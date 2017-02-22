<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("common/user-page-start.php");
?>

<div class="box">
    <article class="post">
        <header>
            <h4>
                <a href="<?= base_url("user/index/{$post['author_id']}"); ?>"><?= $post['author']; ?></a>
            </h4>
        </header>
        <p>
            <?php
            print(htmlspecialchars($post['post']));
            if ($post['has_more']) {
                print "<a href='" . base_url("user/post/{$post['post_id']}") . "' class='more'>view more</a>";
            }
            ?>
        </p>
        <footer>
            <small><span class="glyphicon glyphicon-time"></span> <?= $post['timespan']; ?> ago</small>
            <?php
            if ($post['num_shares'] > 0) {
                print "<span> &middot; </span><a href='" . base_url("post/shares/{$post['post_id']}") . "'>{$post['num_shares']}";
                print ($post['num_shares'] == 1) ? " share" : " shares";
                print "</a>";
            }
            ?>
        </footer>
    </article>
</div><!-- box -->
<div class="box">
    <h4>Shares</h4>
    <?php if (count($shares) == 0): ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No shares to show.</p>
    </div>
    <?php else:
        if (isset($has_prev)) { ?>
            <li>
                <a href="<?= base_url("post/shares/{$post['post_id']}/{$prev_offset}"); ?>">
                    View previous shares.
                </a>
            </li>
        <?php } ?>
    <ul class="likes">
        <?php foreach($shares as $share): ?>
        <li>
            <figure><img src="<?= base_url('images/kasumba.jpg'); ?>" alt="<?= $share['sharer']; ?>"></figure>
            <span><a href="<?= base_url("user/index/{$share['sharer_id']}"); ?>"><?= $share['sharer']; ?></a></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div><!-- box -->
<?php if ($has_next): ?>
<div class="box more">
    <a href="<?= base_url("post/shares/{$post['post_id']}/{$next_offset}"); ?>">View more shares</a>
</div>
<?php endif; ?>
