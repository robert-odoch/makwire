<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'friends');
require_once(__DIR__ . '/../common/user-page-start.php');
require_once(__DIR__ . '/../common/secondary-user-nav.php');
?>

<div class='box'>
    <?php if (count($friends) == 0) { ?>
    <div class='alert alert-info' role='alert'>
        <span class='fa fa-info-circle' aria-hidden='true'></span>
        <p>No friends to show.</p>
    </div>
    <?php } else { ?>
    <div class='friends'>
        <?php foreach($friends as $fr) { ?>
        <div class='media separated'>
            <div class='media-left'>
                <img src='<?= $fr['profile_pic_path']; ?>'
                    alt='<?= $fr['profile_name']; ?>' class='media-object profile-pic-sm'>
            </div>
            <div class='media-body'>
                <h4 class='media-heading'>
                    <a href='<?= base_url("user/{$fr['friend_id']}"); ?>'>
                        <?= $fr['profile_name']; ?>
                    </a>
                </h4>

                <?php  // Show link for sending message.
                if ($suid == $_SESSION['user_id']) {
                    echo "<a href='" . base_url("user/send-message/{$fr['friend_id']}") .
                         "' data-toggle='tooltip' data-placement='right' title='Send message' class='send-message'>
                            <span class='fa fa-envelope-o'></span>
                        </a>";
                }
                ?>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div><!-- .box -->

<?php if ($has_next) { ?>
    <div class='box more'>
        <?php
        if ($is_visitor) {
            print "<a href='" . base_url("user/friends/{$suid}") . "'>Show more friends</a>";
        }
        else {
            print "<a href='" . base_url("user/friends/{$_SESSION['user_id']}") . "'>Show more friends</a>";
        }
        ?>
    </div>
<?php } ?>
