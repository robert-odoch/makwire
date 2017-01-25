<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
        <?php require_once('common/user-page-start.php'); ?>
                        <?php if ($visitor): ?>
                        <div id="secondary-user" class="box">
                            <figure>
                                <img src="<?= base_url("images/kasumba.jpg"); ?>" alt="<?= $secondary_user; ?>'s photo" class="profile-pic">
                            </figure>
                            <div>
                                <a href="<?= base_url("user/index/{$suid}"); ?>"><?= $secondary_user; ?></a>
                                <?php
                                if ($friendship_status['friends']) {
                                    // Do nothing.
                                }
                                elseif ($friendship_status['fr_sent'] && $friendship_status['target_id']==$_SESSION['user_id']) {
                                    print("<a href='" . base_url("user/accept_friend/{$suid}") . "' class='btn'>Confirm Friend</a>");
                                }
                                elseif ($friendship_status['fr_sent'] && $friendship_status['user_id']==$_SESSION['user_id']) {
                                    print("<a href='' class='btn'>Friend Request Sent</a>");
                                }
                                else {
                                    print("<a href='" . base_url("user/add_friend/{$suid}") . "' class='btn'>Add Friend</a>");
                                }
                                ?>
                            </div>
                            <ul>
                                <li><a href="<?= base_url("user/index/{$suid}"); ?>">Timeline</a></li>
                                <li><a href="<?= base_url("user/profile/{$suid}"); ?>">About</a></li>
                                <li><a href="<?= base_url("user/friends/{$suid}"); ?>" class="active">Friends</a></li>
                                <li><a href="">Groups</a></li>
                                <li><a href="">Photos</a></li>
                            </ul>
                            <span class="clearfix"></span>
                        </div>
                        <?php endif; ?>
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
                    </div><!-- .main-content -->
                </div><!-- .main -->

                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>
            <div class="col-small">
                <?php require_once("common/active-users.php"); ?>
            </div>
        <span class="clearfix"></span>
        </div> <!-- #wrapper -->
