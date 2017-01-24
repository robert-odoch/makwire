<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box edit-profile">
                            <h4>Education</h4>
                            <ul class="profile">
                                <li>College: College of Computing and Information Sciences <a href="<?= base_url("user/edit-college"); ?>">Edit</a></li>
                                <li>School: School of Computing and Informatics Technology</li>
                                <li>Programme: BSc. Software Engineering <a href="<?= base_url("user/edit-programme"); ?>">Edit</a></li>
                                <li>Year: II</li>
                            </ul>

                            <h4>Residence</h4>
                            <ul class="profile">
                                <li>Hall: Lumumba <a href="<?= base_url("user/edit-hall"); ?>">Edit</a></li>
                                <li>Hostel: Paramount <a href="<?= base_url("user/edit-hostel"); ?>">Edit</a></li>
                            </ul>

                            <h4>Origin</h4>
                            <ul class="profile">
                                <li>District: Oyam <a href="<?= base_url("user/edit-district"); ?>">Edit</a></li>
                                <li>Country: Uganda <a href="<?= base_url("user/edit-country"); ?>">Edit</a></li>
                            </ul>

                            <h4>Work</h4>
                            <ul class="profile">
                                <li>2014 - 2016: Student at Makerere University</li>
                                <li>2012 - 2013: Manager at Self Employed</li>
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
