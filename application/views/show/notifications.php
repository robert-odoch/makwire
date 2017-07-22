<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
require_once(dirname(__FILE__) . '/helper functions/show_notifications_functions.php');
?>

<div class="box">
    <h4>Notifications</h4>
    <?php if (count($notifications) == 0) { ?>
    <div class="alert alert-info" role="alert">
        <p>
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            No notifications to show.
        </p>
    </div>
    <?php } else {
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
                    print "<li><a href='" . base_url("user/friend-requests/0/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong> sent you a friend request.</a> " .
                            " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    break;
                case 'confirmed_friend_request':
                    print "<li><a href='" . base_url("user/{$notif['actor_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong> accepted your friend request.</a> " .
                            " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    break;
                case 'like':
                    if (in_array($notif['source_type'], ['post', 'photo', 'video', 'link'])) {
                        print '<li>' . build_item_like_notif($notif) . '</li>';
                    }
                    elseif ($notif['source_type'] == "comment") {
                        print "<li><a href='" . base_url("comment/likes/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} liked your comment \"{$notif['comment']}\"</a>" .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    }
                    elseif ($notif['source_type'] == "reply") {
                        print "<li><a href='" . base_url("reply/likes/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} liked your reply \"{$notif['comment']}\"</a>" .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    }
                    elseif ($notif['source_type'] == 'birthday_message') {
                        print "<li><a href='" . base_url("user/birthday/{$notif['actor_id']}/{$notif['age']}") .
                                "'><strong class='object'>{$notif['actor']}</strong> liked your birthday message.</a> " .
                                "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    }
                    break;
                case 'comment':
                    if (in_array($notif['source_type'], ['post', 'photo', 'video', 'link'])) {
                        print '<li>' . build_item_comment_notif($notif) . '</li>';
                    }
                    elseif ($notif['source_type'] == 'birthday_message') {
                        print "<li><a href='" . base_url("birthday-message/replies/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                                "replied to your birthday message.</a> " .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                                "{$notif['timespan']} ago</small></li>";
                    }
                    break;
                case 'reply':
                    print '<li>' . build_reply_notif($notif) . '</li>';
                    break;
                case 'share':
                    if (in_array($notif['source_type'], ['post', 'photo', 'video', 'link'])) {
                        print '<li>' . build_item_share_notif($notif) . '</li>';
                    }
                    break;
                case 'birthday':
                    $dob_array = explode('-', $notif['dob']);
                    if (date_create(date('Y-m-d')) ==
                        date_create(($dob_array[0]+$notif['age']) . "-{$dob_array[1]}-{$dob_array[2]}")) {
                        print("<li><a href='" . base_url("user/birthday/{$notif['actor_id']}/{$notif['age']}") .
                                "'>Today is <strong class='object'>" .
                                format_name($notif['actor'], '</strong>') . " birthday.</a></li>");
                    }
                    else {
                        $birthday = date_create(($dob_array[0] + $notif['age']) . "-{$dob_array[1]}-{$dob_array[2]}");
                        $birthday = $birthday->format('F jS, Y');
                        print("<li><a href='" . base_url("user/birthday/{$notif['actor_id']}/{$notif['age']}") .
                                "'><strong class='object'>" .
                                format_name($notif['actor'], '</strong>') . " birthday was on {$birthday}.</a></li>");
                    }
                    break;
                case 'profile_pic_change':
                    print("<li><a href='" . base_url("user/photo/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong> updated his profile picture.</a>" .
                            " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>");
                    break;
                case 'message':
                    print "<li><a href='" . base_url("user/birthday/{$notif['subject_id']}/{$notif['age']}") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} sent you a birthday message.</a>" .
                            " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    break;
                }
            }
            ?>
        </ul>
    <?php } ?>
</div><!-- box -->

<?php if ($has_next) { ?>
    <div class="box more">
        <a href="<?= base_url("user/notifications/{$next_offset}/"); ?>">
            View
            <?php
            if (isset($older)) {
                // Showing new notifications, but we also have to give a link to older notifications.
                print ' older ';
            }
            else {
                print ' more ';
            }
            ?>
            notifications</a>
    </div>
<?php } ?>
