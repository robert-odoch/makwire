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
    <?php if (isset($halls) && count($halls) > 0) { ?>
        <form action="<?= base_url($form_action); ?>" method="post" accept-charset="utf-8" role="form">
            <fieldset>
                <div class="form-group">
                    <label for="hall">Select Hall</label>
                    <select name="hall" id="hall" class="form-control">
                        <optgroup>
                            <?php
                            foreach ($halls as $hall) {
                                print("<option value='{$hall['hall_id']}");
                                if (isset($hall_id) && ($hall_id == $hall['hall_id'])) {
                                    print(" selected");
                                }
                                print("'>{$hall['hall_name']}</option>");
                            }
                            ?>
                        </optgroup>
                    </select>
                </div>
                <div class="input-group">
                    <div class="radio-inline">
                        <label for="resident">
                            <input type="radio" name="resident" id="resident" value="1"
                            <?php if (isset($resident) && ($resident == 1)) { print(" checked"); } ?>
                            > Resident
                        </label>
                    </div>
                    <div class="radio-inline">
                        <label for="non-resident">
                            <input type="radio" name="resident" id="non-resident" value="0"
                            <?php
                            if (!isset($resident) || (isset($resident) && ($resident == 0))) {
                                print(" checked");
                            }
                            ?>
                            > Non-resident
                        </label>
                    </div>
                </div>
            </fieldset>
            <?php require_once("common/show-date-input.php"); ?>
            <?php if (isset($user_hall_id)) { ?>
            <fieldset>
                <input type="hidden" name="user-hall-id" value="<?= $user_hall_id; ?>">
                <input type="hidden" name="old-start-date" value="<?= $old_start_date; ?>">
                <input type="hidden" name="old-end-date" value="<?= $old_end_date; ?>">
            </fieldset>
            <?php } ?>
            <input type="submit" value="Save" class="btn">
        </form>
    <?php } // (count($halls) > 0) ?>
    <?php endif; ?>
</div><!-- box -->
