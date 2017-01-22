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
                                </ul>
                            </nav>
                        </aside>
                    </div>
