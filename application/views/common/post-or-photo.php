<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="box">
    <article class="<?= $object; ?>">
        <header>
            <h4>
                <a href="<?= base_url("user/index/{$$object['author_id']}"); ?>"><?= $$object['author']; ?></a>
                <small class="time"><span class="glyphicon glyphicon-time"></span> <?= $$object['timespan']; ?> ago</small>
            </h4>
        </header>
        <?php if ($object == 'post') { ?>
        <p>
            <?php
            print htmlspecialchars($post['post']);
            if ($post['has_more']) {
                print "<a href='" . base_url("user/post/{$post['author_id']}/{$post['post_id']}") . "' class='more'>view more</a>";
            }
            ?>
        </p>
        <?php } elseif ($object == 'photo') { ?>
            <img src="<?= $photo['web_path']; ?>" alt="<?= "{$photo['author']}'s photo"; ?>">
        <?php } ?>
        <?php require_once("post-or-photo-footer.php"); ?>
    </article>
</div><!-- box -->
