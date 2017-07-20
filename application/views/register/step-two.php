<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="wrap-single">
    <div role="main" class="main">
        <div class="box">
            <h4>Sign Up: step 2 of 3</h4>
            <form action="<?= base_url('register/step-two'); ?>" method="post"
                accept-charset="utf-8" role="form">
                <fieldset>
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" name="fname" id="first-name" class="fluid
                        <?php
                        if (isset($error_messages) && isset($error_messages['fname'])) {
                            print ' has-error';
                        }
                        ?>"
                        <?php
                        if (isset($fname)) {
                            print " value='{$fname}'";
                        }
                        ?> required>
                        <?php
                        if (isset($error_messages) && isset($error_messages['fname'])) {
                            print "<span class='error'>{$error_messages['fname']}</span>";
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" name="lname" id="last-name" class="fluid
                        <?php
                        if (isset($error_messages) && isset($error_messages['lname'])) {
                            print ' has-error';
                        }
                        ?>"
                        <?php
                        if (isset($lname)) {
                            print " value='{$lname}'";
                        }
                        ?> required>
                        <?php
                        if (isset($error_messages) && isset($error_messages['lname'])) {
                            print "<span class='error'>{$error_messages['lname']}</span>";
                        }
                        ?>
                    </div>
                    <div class="input-group">
                        <p>Gender</p>
                        <div class="radio-inline">
                            <label for="male">
                                <input type="radio" name="gender" id="male" value="male"
                                <?php
                                if(isset($gender) && $gender == 'male') {
                                    print ' checked';
                                }
                                ?>> Male
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label for="female">
                                <input type="radio" name="gender" id="female" value="female"
                                <?php
                                if (isset($gender) && $gender == 'female') {
                                    print ' checked';
                                }
                                ?>> Female
                            </label>
                        </div>
                    </div>

                    <div class="input-group">
                        <p>Date of Birth</p>
                        <div>
                            <label for="day">Day</label>
                            <select name="day" id="day">
                                <optgroup>
                                    <?php
                                    for ($i = 1; $i < 32; ++$i) {
                                        print "<option value='{$i}'";
                                        if (isset($day) && $day == $i) {
                                            print ' selected';
                                        }
                                        print ">{$i}</option>";
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label for="month">Month</label>
                            <select name="month" id="month">
                                <optgroup>
                                <?php
                                $months = array(1=>'Jan',2=>'Feb',3=>'Mar',
                                                4=>'Apr',5=>'May',6=>'Jun',
                                                7=>'Jul',8=>'Aug',9=>'Sep',
                                                10=>'Oct',11=>'Nov',12=>'Dec');
                                foreach ($months as $k => $v) {
                                    print "<option value='{$k}'";
                                    if (isset($month) && $month == $k) {
                                        print ' selected';
                                    }
                                    print ">{$v}</option>";
                                }
                                ?>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label for="year">Year</label>
                            <select name="year" id="year">
                                <optgroup>
                                    <?php
                                    $current_year = date('Y');
                                    $min_age = 15;
                                    $max_age = 80;
                                    for ($i = ($current_year - $min_age);
                                        $i > ($current_year - $max_age);
                                        --$i) {
                                        print "<option value='{$i}'";
                                        if (isset($year) && $year == $i) {
                                            print ' selected';
                                        }
                                        print ">{$i}</option>";
                                    }
                                    ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <input type="submit" value="Next &rarr;" class="btn btn-sm">
            </form>
        </div><!-- box -->
