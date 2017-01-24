<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit College</h4>
                            <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <p><?= "{$success_message}"; ?></p>
                            </div>
                            <?php else: if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php
                                foreach ($error_message as $error) {
                                    print("<p>{$error}</p>");
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                            <form action="<?= base_url("user/edit_college"); ?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="college">College</label>
                                        <select name="college" id="college" class="form-control">
                                            <optgroup>
                                            <?php
                                            foreach ($colleges as $c) {
                                                print "<option value='{$c['college_id']}'>{$c['college_name']}</option>";
                                            }
                                            ?>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="school">School</label>
                                        <select name="school" id="school" class="form-control">
                                            <optgroup>
                                            <?php
                                            foreach ($schools as $s) {
                                                print "<option value='{$s['school_id']}'>{$s['school_name']}</option>";
                                            }
                                            ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </fieldset><fieldset>
                                    <div class="input-group">
                                        <p>From</p>
                                        <div>
                                            <label for="start-day">Date</label>
                                            <select name="start-day" id="start-day">
                                                <optgroup>
                                                    <?php
                                                    for ($i=1; $i < 32; $i++) {
                                                        print("<option value='{$i}'>{$i}</option>");
                                                    }
                                                    ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="start-month">Month</label>
                                            <select name="start-month" id="start-month">
                                                <optgroup>
                                                <?php
                                                $months = array(1=>'Jan',2=>'Feb',3=>'Mar',
                                                                4=>'Apr',5=>'May',6=>'Jun',
                                                                7=>'Jul',8=>'Aug',9=>'Sep',
                                                                10=>'Oct',11=>'Nov',12=>'Dec');
                                                foreach ($months as $k => $v) {
                                                    print "<option value='{$k}'>{$v}</option>";
                                                }
                                                ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="start-year">Year</label>
                                            <select name="start-year" id="start-year">
                                                <optgroup>
                                                <?php
                                                $starting_year = date('Y');
                                                for ($i = 0; $i != 20; ++$i) {
                                                    $year = $starting_year - $i;
                                                    print "<option value='{$year}'>{$year}</option>";
                                                }
                                                ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="input-group">
                                        <p>To</p>
                                        <div>
                                            <label for="end-day">Date</label>
                                            <select name="end-day" id="end-day">
                                                <optgroup>
                                                    <?php
                                                    for ($i=1; $i < 32; $i++) {
                                                        print("<option value='{$i}'>{$i}</option>");
                                                    }
                                                    ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="end-month">Month</label>
                                            <select name="end-month" id="end-month">
                                                <optgroup>
                                                <?php
                                                foreach ($months as $k => $v) {
                                                    print "<option value='{$k}'>{$v}</option>";
                                                }
                                                ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="end-year">Year</label>
                                            <select name="end-year" id="end-year">
                                                <optgroup>
                                                <?php
                                                $starting_year = date('Y') + 6;
                                                for ($i = 0; $i != 24; ++$i) {
                                                    $year = $starting_year - $i;
                                                    print "<option value='{$year}'>{$year}</option>";
                                                }
                                                ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                                <input type="submit" value="Save" class="btn">
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
