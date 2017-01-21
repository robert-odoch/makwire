<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">   
                            <h4>Notifications</h4>
                            <?php if (count($notifications) == 0): ?>
                            <div class="alert alert-info">
                                <p>No notifications to show.</p>
                            </div>
                            <?php else: ?>
                            <ul class="notifications">
                                <?php
                                foreach ($notifications as $notif) {
                                    switch ($notif['activity']) {
                                    case 'friend_request':
                                        print "<li><a href='" . base_url("user/friend_requests/") . "'><strong class='object'>{$notif['user']}</strong> sent you a friend request.</a> " .
                                            "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        break;
                                    case 'confirmed_friend_request':
                                        print "<li><a href='" . base_url("user/index/{$notif['trigger_id']}") . "'><strong class='object'>{$notif['user']}</strong> accepted your friend request.</a> " .
                                            "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        break;
                                    case 'like':
                                        if ($notif['source_type'] == 'post') {
                                            print "<li><a href='" . base_url("post/likes/{$notif['source_id']}") . "'><strong class='object'>{$notif['user']}</strong> liked your post \"{$notif['post']}\"</a> " .
                                            "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        }
                                        else if ($notif['source_type'] == "comment") {
                                            print "<li><a href='" . base_url("comment/likes/{$notif['source_id']}") . "'><strong class='object'>{$notif['user']}</strong> liked your comment \"{$notif['comment']}\".</a>" .
                                            "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";;
                                        }
                                        else if ($notif['source_type'] == "reply") {
                                            print "<li><a href='" . base_url("reply/likes/{$notif['source_id']}") . "'><strong class='object'>{$notif['user']}</strong> liked your reply \"{$notif['reply']}\".</a>" .
                                                  "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        }
                                        break;
                                    case 'comment':
                                        if ($notif['source_type'] == 'post') {
                                            print "<li><a href='" . base_url("post/comments/{$notif['source_id']}") . "'><strong class='object'>{$notif['user']}</strong> commented your post \"{$notif['post']}\"</a> " .
                                            "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        }
                                        break;
                                    case 'reply':
                                        print "<li><a href='" . base_url("comment/replies/{$notif['source_id']}") . "'><strong class='object'>{$notif['user']}</strong> replied to your comment \"{$notif['comment']}\".</a>" .
                                        "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        break;
                                    case 'share':
                                        if ($notif['source_type'] == 'post') {
                                            print "<li><a href='" . base_url("/user/post/{$notif['trigger_id']}/{$notif['new_post_id']}") . "'><strong class='object'>{$notif['user']}</strong> shared your post \"{$notif['post']}\" on his timeline.</a> " .
                                            "<small><span class='glyphicon glyphicon-time'></span> {$notif['timespan']} ago</small></li>";
                                        }
                                        break;
                                    }
                                }
                                ?>
                            </ul>
                            <?php endif; ?>
                        </div><!-- box -->
                        <?php if ($has_next): ?>
                        <div class="box previous">
                            <a href="<?= base_url("user/notifications/{$next_offset}/"); ?>">View previous notifications</a>
                        </div>
                        <?php endif; ?>
                    </div><!-- .main-content -->
                </div><!-- main -->
                
                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>
            <div class="col-small">
                <?php require_once('common/active-users.php'); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
