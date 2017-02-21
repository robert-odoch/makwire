<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Notifications</h4>
    <?php if (count($notifications) == 0): ?>
    <div class="alert alert-info">
        <p><span class="glyphicon glyphicon-info-sign"></span> No notifications to show.</p>
    </div>
    <?php else:
        if (isset($has_prev)) { ?>
            <i><a href="<?= base_url("user/notifications/{$prev_offset}"); ?>">
                View previous notifications.
            </a></i>
        <?php } ?>
        <ul class="notifications">
            <?php
            foreach ($notifications as $notif) {
                switch ($notif['activity']) {
                case 'friend_request':
                    print "<li><a href='" . base_url("user/friend_requests/") . "'><strong class='object'>{$notif['actor']}</strong> sent you a friend request.</a> " .
                        "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    break;
                case 'confirmed_friend_request':
                    print "<li><a href='" . base_url("user/index/{$notif['actor_id']}") . "'><strong class='object'>{$notif['actor']}</strong> accepted your friend request.</a> " .
                        "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    break;
                case 'like':
                    if ($notif['source_type'] == 'post') {
                        print "<li><a href='" . base_url("user/post/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> liked your post \"{$notif['post']}\"</a> " .
                        "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    }
                    else if ($notif['source_type'] == "comment") {
                        print "<li><a href='" . base_url("comment/likes/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> liked your comment \"{$notif['comment']}\"</a>" .
                        "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";;
                    }
                    else if ($notif['source_type'] == "reply") {
                        print "<li><a href='" . base_url("reply/likes/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> liked your reply \"{$notif['reply']}\"</a>" .
                              "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    }
                    break;
                case 'comment':
                    if ($notif['subject_id'] == $_SESSION['user_id']) {
                        if ($notif['source_type'] == 'post') {
                            print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> commented on your post \"{$notif['post']}\"</a> " .
                                  "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                        }
                        elseif ($notif['source_type'] == 'photo') {
                            print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> commented on your photo</a> " .
                                  "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                        }
                    }
                    elseif(isset($notif['shared_post'])) {
                        print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> commented on a post you shared \"{$notif['post']}\"</a> " .
                              "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    }
                    else {
                        if ($notif['source_type'] == 'post') {
                            print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> also commented on <strong class='object'>{$notif['subject']}</strong>'s post \"{$notif['post']}\"</a> " .
                                  "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                        }
                        elseif ($notif['source_type'] == 'photo') {
                            print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> also commented on <strong class='object'>{$notif['subject']}</strong>'s photo</a> " .
                                  "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                        }
                    }
                    break;
                case 'reply':
                    if ($notif['subject_id'] == $_SESSION['user_id']) {
                        print "<li><a href='" . base_url("comment/replies/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> replied to your comment \"{$notif['comment']}\"</a>" .
                              "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    }
                    else {
                        print "<li><a href='" . base_url("comment/replies/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> also replied to <strong class='object'>{$notif['subject']}</strong>'s comment \"{$notif['comment']}\"</a>" .
                              "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    }
                    break;
                case 'share':
                    if ($notif['source_type'] == 'post') {
                        print "<li><a href='" . base_url("/user/post/{$notif['new_post_id']}") . "'><strong class='object'>{$notif['actor']}</strong> shared your post \"{$notif['post']}\" on his timeline.</a> " .
                        "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                    }
                    break;
                case 'birthday':
                    print("<li><a href=''>Today is <strong class='object'>{$notif['actor']}</strong>'s birthday.</a></li>");
                    break;
                case 'profile_pic_change':
                    print("<li><a href='" . base_url("user/photo/{$notif['source_id']}") . "'><strong class='object'>{$notif['actor']}</strong> updated his profile picture.</a>" .
                          "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>");
                    break;
                }
            }
            ?>
        </ul>
    <?php endif; ?>
</div><!-- box -->
<?php if ($has_next): ?>
<div class="box more">
    <a href="<?= base_url("user/notifications/{$next_offset}/"); ?>">View more notifications</a>
</div>
<?php endif; ?>
