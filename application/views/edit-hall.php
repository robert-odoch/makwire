<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit Hall</h4>
                            <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <p><?= "{$success_message}"; ?></p>
                            </div>
                            <?php else: if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <p><?= "{$error_message}"; ?></p>
                            </div>
                            <?php endif; ?>
                            <form action="<?= base_url("user/edit-hall"); ?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="hall">Select Hall</label>
                                        <select name="hall" id="hall" class="form-control">
                                            <optgroup>
                                                <?php foreach ($halls as $hall): ?>
                                                <option value="<?= $hall['hall_id']; ?>"><?= $hall['hall_name']; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <div class="radio-inline">
                                            <label for="resident">
                                                <input type="radio" name="resident" id="resident" value="1"> Resident
                                            </label>
                                        </div>
                                        <div class="radio-inline">
                                            <label for="non-resident">
                                                <input type="radio" name="resident" id="non-resident" value="non-resident"checked> Non-resident
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
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
