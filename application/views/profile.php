<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
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
                                <li><a href="<?= base_url("user/profile"); ?>" class="active">About</a></li>
                                <li><a href="<?= base_url("user/friends/{$suid}"); ?>">Friends</a></li>
                                <li><a href="">Groups</a></li>
                                <li><a href="">Photos</a></li>
                            </ul>
                            <span class="clearfix"></span>
                        </div>
                        <?php endif; ?>

                        <div class="box">
                            <?php
                            if ($visitor) {
                                print("<h4>About {$secondary_user}</h4>");
                            }
                            else {
                                print("<h4>About {$primary_user}</h4>");
                            }
                            ?>

                            <?php
                            // If there is nothing to show.
                            if (!$profile['programmes'] || !$profile['colleges'] || !$profile['schools'] ||
                                !$profile['year_of_study'] && !$profile['halls'] && !$profile['hostels'] &&
                                !$profile['country'] && !$profile['district']) {
                                print("<div class='alert alert-info'><p>Nothing to show...</p></div>");
                            }
                            ?>
                            <ul class="about">
                                <?php if ($profile['programmes'] || $profile['colleges'] || $profile['schools']): ?>
                                <li>
                                    <?php
                                    if ($profile['programmes']) {
                                        print("Studies/Studied <a href=''>{$profile['programmes'][0]}</a>");
                                    }
                                    if ($profile['colleges']) {
                                        print(" at <a href=''>{$profile['colleges'][0]}</a>");
                                    }
                                    if ($profile['schools']) {
                                        print(", <a href=''>{$profile['schools'][0]}</a>.");
                                    }
                                    ?>
                                </li>
                                <?php endif; ?>
                                <?php
                                if ($profile['year_of_study']) {
                                    print("<li>Currently in Year {$profile['year_of_study']}.</li>");
                                }
                                if ($profile['halls']) {
                                    print("<li>Is/Was a residence/attached to <a href=''>{$profile['halls'][0]}</a>.</li>");
                                }
                                if ($profile['hostels']) {
                                    print("<li>Stays/Stayed at <a href=''>{$profile['hostels'][0]}</a>.</li>");
                                }
                                if ($profile['country'] || $profile['district']) {
                                    print("<li>From ");
                                    if ($profile['district']) {
                                        print("{$profile['district']}");
                                    }

                                    if ($profile['country'] && $profile['district']) {
                                        print(", {$profile['country']}");
                                    }
                                    else {
                                        print("{$profile['country']}");
                                    }

                                    print("</li>");
                                }
                                ?>
                            </ul>
                        </div><!-- box -->
                    </div><!-- .main-content -->
                </div><!-- main -->

                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>

            <div class="col-small">
                <?php require_once("common/active-users.php"); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
