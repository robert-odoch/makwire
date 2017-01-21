<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
                        <div class="side-content">
                        <aside>
                            <div id="user">
                                <img src="<?= base_url("images/kasumba.jpg"); ?>" alt="<?= $user; ?>'s photo" class="profile-pic">
                                <a href="<?= base_url("user/index/{$user_id}"); ?>"><?= $user; ?></a>
                                <?php
                                if ($visitor) {
                                    if ($friendship_status['friends']) {
                                        print("<button class='btn'>Friends</button>");
                                    }
                                    else if ($friendship_status['fr_sent'] && $friendship_status['user_id']==$_SESSION['user_id']) {
                                        print("<button class='btn'>Friend Request Sent</button>");
                                    }
                                    else if ($friendship_status['fr_sent']) {
                                        print("<a href='" . base_url("user/accept_friend/{$friendship_status['user_id']}") . "' class='btn'>Confirm Friend</a>");
                                    }
                                    else {
                                        print '<a href="' . base_url("user/add_friend/{$user_id}") . '" class="btn">Add friend</a>';
                                    }
                                }
                                ?>
                            </div>
                            <nav role="navigation" class="user-nav">
                                <ul>
                                    <?php if ($visitor): ?>
                                    <li><a href="">About</a></li>
                                    <?php else: ?>
                                    <li><a href="">Edit Profile</a></li>
                                    <?php endif; ?>
                                    
                                    <li><a href="">Friends</a></li>
                                    <li><a href="">Groups</a></li>
                                    <li><a href="">Photos</a></li>
                                    <!--li class="dropdown">
                                        <a href="" role="button" class="dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Profile <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo base_url('user/about/' . $user_id); ?>">About</a></li>
                                            <li><a href="<?php echo base_url('user/friends/' . $user_id); ?>">Friends</a></li>
                                            <li><a href="<?php echo base_url('user/followers/' . $user_id); ?>">Followers</a></li>
                                            <li><a href="<?php echo base_url('user/following/' . $user_id); ?>">Following</a></li>
                                            <li><a href="<?php echo base_url('user/groups/' . $user_id); ?>">Groups</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="<?php echo base_url('user/photos/' . $user_id); ?>">Photos</a></li-->
                                </ul>
                            </nav>
                        </aside>  
                    </div>
