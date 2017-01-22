<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
                        <div class="side-content">
                        <aside>
                            <div id="user">
                                <img src="<?= base_url("images/kasumba.jpg"); ?>" alt="<?= $primary_user; ?>'s photo" class="profile-pic">
                                <a href="<?= base_url("user/index/{$_SESSION['user_id']}"); ?>"><?= $primary_user; ?></a>
                            </div>
                            <nav role="navigation" class="user-nav">
                                <ul>
                                    <li><a href="">Edit Profile</a></li>
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
