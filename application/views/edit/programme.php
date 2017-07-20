<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class="box">
    <h4><?= $heading; ?></h4>
    <form action="<?= $form_action; ?>" method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="programme">Programme</label>
                <?php if (isset($programmes)) { ?>
                <select name="programme" id="programme" class="form-control" required>
                    <optgroup>
                    <?php
                    foreach ($programmes as $p) {
                        print "<option value='{$p['programme_id']}'>{$p['programme_name']}</option>";
                    }
                    ?>
                    </optgroup>
                </select>
                <?php
                } else {  // Editing an existing programme.
                    print "<p>{$user_programme['programme_name']}</p>";
                }
                ?>
            </div>
        </fieldset>

        <fieldset>
            <div class="form-group">
                <label for="year-of-study">Year of Study</label>
                <select name="year-of-study" id="year-of-study" required>
                    <optgroup>
                        <?php
                        $years = array(1=>'one', 2=>'two', 3=>'three',
                                        4=>'four', 5=>'five', 0=>'graduated');
                        foreach ($years as $key => $value) {
                            print "<option value='{$key}'";
                            if (isset($year_of_study) && ($year_of_study == $key)) {
                                print " selected";
                            }
                            print ">{$value}</option>";
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <?php
            if (isset($programmes)) {
                print "<input type='hidden' name='user-school-id' value='{$user_school['id']}'>";
            }
            else {
                print "<input type='hidden' name='user-programme-id' value='{$user_programme['id']}'>";
            }
            ?>
        </fieldset>

        <input type="submit" value="Save" class="btn btn-sm">
    </form>
</div><!-- box -->
