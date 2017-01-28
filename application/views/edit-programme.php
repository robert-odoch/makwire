<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4><?= $heading; ?></h4>
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <p><?= "{$success_message}"; ?></p>
    </div>
    <?php else: if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <p><?= "{$error_message}"; ?></p>
    </div>
    <?php endif; ?>
    <?php if (isset($programmes) && (count($programmes) > 0)) { ?>
        <form action="<?= base_url($form_action); ?>" method="post" accept-charset="utf-8" role="form">
            <fieldset>
                <div class="form-group">
                    <label for="programme">Programme</label>
                    <select name="programme" id="programme" class="form-control">
                        <optgroup>
                        <?php
                        foreach ($programmes as $p) {
                            print "<option value='{$p['programme_id']}'>{$p['programme_name']}</option>";
                        }
                        ?>
                        </optgroup>
                    </select>
                </div>
            </fieldset>
            <?php if (isset($user_programme_id)) { ?>
            <fieldset>
                <input type="hidden" name="user-programme-id" value="<?= $user_programme_id; ?>">
            </fieldset>
            <?php } ?>
            <?php if (isset($start_date) && isset($end_date)) { ?>
            <fieldset>
                <input type="hidden" name="start-date" value="<?= $start_date; ?>">
                <input type="hidden" name="end-date" value="<?= $end_date; ?>">
            </fieldset>
            <?php } ?>

            <fieldset>
                <div class="form-group">
                    <label for="year-of-study">Year of Study</label>
                    <select name="ystudy" id="year-of-study">
                        <optgroup>
                            <?php
                            $years = array(1=>'one', 2=>'two', 3=>'three', 4=>'four', 5=>'five', 0=>'graduated');
                            foreach ($years as $key => $value) {
                                print("<option value='{$key}'");
                                if (isset($year_of_study) && ($year_of_study == $key)) {
                                    print(" selected");
                                }
                                print(">{$value}</option>");
                            }
                            ?>
                        </optgroup>
                    </select>
                </div>
            </fieldset>
            <input type="submit" value="Save" class="btn">
        </form>
    <?php } ?>
    <?php endif; ?>
</div><!-- box -->
