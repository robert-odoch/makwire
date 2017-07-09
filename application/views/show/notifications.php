<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
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
                    print "<li><a href='" . base_url("user/friend_requests/") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} sent you a friend request.</a> " .
                            " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    break;
                case 'confirmed_friend_request':
                    print "<li><a href='" . base_url("user/{$notif['actor_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong> accepted your friend request.</a> " .
                            " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    break;
                case 'like':
                    if ($notif['source_type'] == 'post') {
                        print "<li><a href='" . base_url("user/post/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} liked your post \"{$notif['post']}\"</a> " .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    }
                    elseif ($notif['source_type'] == 'photo') {
                        print "<li><a href='" . base_url("user/photo/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} liked your photo.</a> " .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
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
                    if ($notif['subject_id'] == $_SESSION['user_id']) {
                        if ($notif['source_type'] == 'post') {
                            print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") .
                                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} commented on your post \"{$notif['post']}\"</a> " .
                                    " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                        }
                        elseif ($notif['source_type'] == 'photo') {
                            print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") .
                                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} commented on your photo.</a> " .
                                    " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                        }
                        elseif ($notif['source_type'] == 'birthday_message') {
                            print "<li><a href='" . base_url("birthday-message/replies/{$notif['source_id']}") .
                                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} replied to your birthday message.</a> " .
                                    " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                        }
                    }
                    elseif($notif['subject_id'] != $_SESSION['user_id']) {
                        if ($notif['source_type'] == 'post') {
                            if ($notif['subject_id'] == $notif['actor_id']) {
                                print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} commented on {$notif['subject_gender']} post you shared \"{$notif['post']}\"</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                            else {
                                print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} commented on a post you shared \"{$notif['post']}\"</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                        }
                        elseif ($notif['source_type'] == 'photo') {
                            if ($notif['subject_id'] == $notif['actor_id']) {
                                print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} commented on {$notif['subject_gender']} photo you shared.</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                            else {
                                print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} commented on a photo you shared.</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                        }
                    }
                    else {
                        if ($notif['source_type'] == 'post') {
                            if ($notif['subject_id'] == $notif['actor_id']) {
                                print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also commented on {$notif['subject_gender']} post \"{$notif['post']}\"</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                            else {
                                print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also commented on <strong class='object'>{$notif['subject']}</strong>'s post \"{$notif['post']}\"</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                        }
                        elseif ($notif['source_type'] == 'photo') {
                            if ($notif['subject_id'] == $notif['actor_id']) {
                                print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also commented on {$notif['subject_gender']} photo.</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                            else {
                                print "<li><a href='" . base_url("photo/comments/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also commented on <strong class='object'>{$notif['subject']}</strong>'s photo.</a> " .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                        }
                    }
                    break;
                case 'reply':
                    if ($notif['subject_id'] == $_SESSION['user_id']) {
                        if ($notif['source_type'] == 'comment') {
                            print "<li><a href='" . base_url("comment/replies/{$notif['source_id']}") .
                                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} replied to your comment \"{$notif['comment']}\"</a>" .
                                    " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                        }
                        elseif ($notif['source_type'] == 'birthday_message') {
                            print "<li><a href='" . base_url("birthday-message/replies/{$notif['source_id']}") .
                                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} replied to your birthday message</a>" .
                                    " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                        }
                    }
                    else {
                        if ($notif['subject_id'] == $notif['actor_id']) {
                            if ($notif['source_type'] == 'comment') {
                                print "<li><a href='" . base_url("comment/replies/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also replied to {$notif['subject_gender']} comment \"{$notif['comment']}\"</a>" .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                            elseif ($notif['source_type'] == 'birthday_message') {
                                print "<li><a href='" . base_url("birthday-message/replies/{$notif['source_id']}") .
                                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also replied to {$notif['subject_gender']} birthday message</a>" .
                                        " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                            }
                        }
                        else {
                            print "<li><a href='" . base_url("comment/replies/{$notif['source_id']}") .
                                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} also replied to <strong class='object'>{$notif['subject']}</strong>'s comment \"{$notif['comment']}\"</a>" .
                                    " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                        }
                    }
                    break;
                case 'share':
                    if ($notif['source_type'] == 'post') {
                        print "<li><a href='" . base_url("/user/post/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} shared your post \"{$notif['post']}\"</a> " .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    }
                    elseif ($notif['source_type'] == 'photo') {
                        print "<li><a href='" . base_url("/user/photo/{$notif['source_id']}") .
                                "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} shared your photo.</a> " .
                                " <small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> {$notif['timespan']} ago</small></li>";
                    }
                    break;
                case 'birthday':
                    $dob_array = explode('-', $notif['dob']);
                    if (date_create(date('Y-m-d')) ==
                        date_create(($dob_array[0]+$notif['age']) . "-{$dob_array[1]}-{$dob_array[2]}")) {
                        print("<li><a href='" . base_url("/user/birthday/{$notif['actor_id']}/{$notif['age']}") .
                                "'>Today is <strong class='object'>" .
                                format_name($notif['actor'], '</strong>') . " birthday.</a></li>");
                    }
                    else {
                        $birthday = date_create(($dob_array[0] + $notif['age']) . "-{$dob_array[1]}-{$dob_array[2]}");
                        $birthday = $birthday->format('F jS, Y');
                        print("<li><a href='" . base_url("/user/birthday/{$notif['actor_id']}/{$notif['age']}") .
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
