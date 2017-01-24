<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit District</h4>
                            <?php if (isset($districts)): ?>
                            <p>Districts that matched</p>
                                <ul>
                                    <?php foreach($districts as $district): ?>
                                        <li><a href="<?= base_url("user/edit-district/{$district['district_name']}/1/{$district['district_id']}"); ?>"><?= $district['district_name']; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                            <form action="<?= base_url("user/edit-district"); ?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="district">District</label>
                                        <input type="text" name="district" id="district" size="30">
                                    </div>
                                </fieldset>
                                <input type="submit" value="Submit" class="btn">
                            </form>
                            <?php endif; ?>
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
