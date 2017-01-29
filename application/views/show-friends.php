<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');

if ($visitor) {
    define('PAGE', 'friends');    
    require_once("common/secondary-user-nav.php");
}
?>

<div class="box">
    <?php
    if (!$visitor) {
        print("<h4>Friends</h4>");
    }
    ?>

    <?php if (count($friends) == 0): ?>
    <div class="alert alert-info">
        <p>No friends to show.</p>
    </div>
    <?php else: ?>
    <ul class="friends">
        <?php foreach($friends as $fr): ?>
        <li>
            <figure><img src="<?= base_url('images/kasumba.jpg'); ?>" alt="<?= $fr['display_name']; ?>"></figure>
            <span><a href="<?= base_url("user/index/{$fr['friend_id']}"); ?>"><?= $fr['display_name']; ?></a></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div><!-- .box -->
<?php if ($has_next): ?>
<div class="box more">
    <?php
    if ($visitor) {
        print '<a href="' . base_url("user/friends/{$suid}") . '">View more friends</a>';
    }
    else {
        print '<a href="' . base_url("user/friends/{$_SESSION['user_id']}") . '">View more friends</a>';
    }
    ?>
</div>
<?php endif; ?>
