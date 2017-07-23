<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function build_item_comment_notif(array $notif)
{
    $comment = '';
    if ($notif['subject_id'] == $_SESSION['user_id']) {
        $comment = "<a href='" . base_url("{$notif['source_type']}/comments/{$notif['source_id']}") .
                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                    "commented on your {$notif['source_type']}";

        if ($notif['source_type'] == 'post') {
            $comment .= " \"{$notif['post']}\"";
        }
    }
    else {
        if ($notif['from_shared']) {
            if ($notif['subject_id'] == $notif['actor_id']) {
                $comment = "<a href='" . base_url("{$notif['source_type']}/comments/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                            "commented on {$notif['subject_gender']} {$notif['source_type']} you shared";

                if ($notif['source_type'] == 'post') {
                    $comment .= " \"{$notif['post']}\"";
                }
            }
            else {
                $comment = "<a href='" . base_url("{$notif['source_type']}/comments/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                            "commented on a {$notif['source_type']} you shared";

                if ($notif['source_type'] == 'post') {
                    $comment .= " \"{$notif['post']}\"";
                }
            }
        }
        else {
            if ($notif['subject_id'] == $notif['actor_id']) {
                $comment = "<a href='" . base_url("{$notif['source_type']}/comments/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                            "also commented on {$notif['subject_gender']} {$notif['source_type']}";
                if ($notif['source_type'] == 'post') {
                    $comment .= " \"{$notif['post']}\"";
                }
            }
            else {
                $comment = "<a href='" . base_url("{$notif['source_type']}/comments/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                            "also commented on <strong class='object'>{$notif['subject']}</strong>'s {$notif['source_type']}";

                if ($notif['source_type'] == 'post') {
                    $comment .= " \"{$notif['post']}\"";
                }
            }
        }
    }

    $comment .= "</a> " .
                "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                "{$notif['timespan']} ago</small>";

    return $comment;
}

function build_item_like_notif(array $notif)
{
    $notif_str = "<a href='" . base_url("user/{$notif['source_type']}/{$notif['source_id']}") .
                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                    "liked your {$notif['source_type']}";

    if ($notif['source_type'] == 'post') {
        $notif_str .= " \"{$notif['post']}\"";
    }

    $notif_str .= "</a> " .
                    "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                    "{$notif['timespan']} ago</small>";

    return $notif_str;
}

function build_item_share_notif(array $notif)
{
    $notif_str = "<a href='" . base_url("user/{$notif['source_type']}/{$notif['source_id']}") .
                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                    "shared your {$notif['source_type']}";
    if ($notif['source_type'] == 'post') {
        $notif_str .= " \"{$notif['post']}\"";
    }

    $notif_str .= "</a> " .
                    "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                    "{$notif['timespan']} ago</small>";

    return $notif_str;
}


function build_like_notif(array $notif)
{
    $notif_str = "<a href='" . str_replace('_', '-', base_url("{$notif['source_type']}/likes/{$notif['source_id']}")) .
                    "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                    "liked your " . str_replace('_', ' ', $notif['source_type']);

    if (in_array($notif['source_type'], ['comment', 'reply'])) {
        $notif_str .= " \"{$notif['comment']}\"";
    }

    $notif_str .= "</a> <small class='time'>" .
                    "<span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                    "{$notif['timespan']} ago</small></li>";

    return $notif_str;
}

function build_reply_notif(array $notif)
{
    $notif_str = '';
    if ($notif['subject_id'] == $_SESSION['user_id']) {
        $notif_str =  "<a href='" . str_replace('_', '-', base_url("{$notif['source_type']}/replies/{$notif['source_id']}")) .
                        "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                        "replied to your " . str_replace('_', ' ', $notif['source_type']);

        if ($notif['source_type'] == 'comment') {
            $notif_str .= " \"{$notif['comment']}\"";
        }

        $notif_str .= "</a> " .
                        "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                        "{$notif['timespan']} ago</small>";
    }
    else {
        if ($notif['subject_id'] == $notif['actor_id']) {
            $notif_str = "<a href='" . str_replace('_', '-', base_url("{$notif['source_type']}/replies/{$notif['source_id']}")) .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                            "also replied to {$notif['subject_gender']} " .
                            str_replace('_', ' ', $notif['source_type']);

            if ($notif['source_type'] == 'comment') {
                $notif_str .= " \"{$notif['comment']}\"";
            }

            $notif_str .= "</a> " .
                            "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                            "{$notif['timespan']} ago</small>";
        }
        else {
            $notif_str = "<a href='" . base_url("comment/replies/{$notif['source_id']}") .
                            "'><strong class='object'>{$notif['actor']}</strong>{$notif['others']} " .
                            "also replied to <strong class='object'>{$notif['subject']}</strong>'s comment" .
                            " \"{$notif['comment']}\"</a> " .
                            "<small class='time'><span class='glyphicon glyphicon-time' aria-hidden='true'></span> " .
                            "{$notif['timespan']} ago</small>";
        }
    }

    return $notif_str;
}
?>
