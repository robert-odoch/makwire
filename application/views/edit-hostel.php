<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit Hostel</h4>
                            <form action="<?= base_url("user/edit-hostel")?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="hostel">Select Hostel</label>
                                        <select name="hostel" id="hostel" class="form-control">
                                            <optgroup>
                                                <?php foreach ($hostels as $hostel): ?>
                                                <option value="<?= $hall['hostel_id']; ?>"><?= $hall['hostel_name']; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="input-group">
                                        <p>From</p>
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
