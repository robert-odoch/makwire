<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<fieldset>
    <div class="input-group">
        <p>From</p>
        <div>
            <label for="start-day">Day</label>
            <select name="start-day" id="start-day">
                <optgroup>
                    <?php
                    for ($i=1; $i < 32; $i++) {
                        print "<option value='{$i}'";
                        if (isset($start_day) && ($start_day == $i)) {
                            print " selected";
                        }
                        print ">{$i}</option>";
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
                    print "<option value='{$k}'";
                    if (isset($start_month) && ($start_month == $k)) {
                        print " selected";
                    }
                    print ">{$v}</option>";
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
                    print "<option value='{$year}'";
                    if (isset($start_year) && ($start_year == $year)) {
                        print " selected";
                    }
                    print ">{$year}</option>";
                }
                ?>
                </optgroup>
            </select>
        </div>
    </div>

    <div class="input-group">
        <p>To</p>
        <div>
            <label for="end-day">Day</label>
            <select name="end-day" id="end-day">
                <optgroup>
                    <?php
                    for ($i=1; $i < 32; $i++) {
                        print "<option value='{$i}'";
                        if (isset($end_day) && ($end_day == $i)) {
                            print " selected";
                        }
                        print ">{$i}</option>";
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
                    print "<option value='{$k}'";
                    if (isset($end_month) && ($end_month == $k)) {
                        print " selected";
                    }
                    print ">{$v}</option>";
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
                    print "<option value='{$year}'";
                    if (isset($end_year) && ($end_year == $year)) {
                        print " selected";
                    }
                    print ">{$year}</option>";
                }
                ?>
                </optgroup>
            </select>
        </div>
    </div>
</fieldset>
