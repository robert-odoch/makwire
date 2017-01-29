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
    <?php if (isset($hostels) && count($hostels) > 0) { ?>
        <form action="<?= base_url($form_action); ?>" method="post" accept-charset="utf-8" role="form">
            <fieldset>
                <div class="form-group">
                    <label for="hostel">Select Hostel</label>
                    <select name="hostel" id="hostel" class="form-control">
                        <optgroup>
                            <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['hostel_id']; ?>"><?= $hostel['hostel_name']; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </fieldset>
            <?php require_once("common/show-date-input.php"); ?>
            <?php if (isset($user_hostel_id)) { ?>
            <fieldset>
                <input type="hidden" name="user-hostel-id" value="<?= $user_hostel_id; ?>">
                <input type="hidden" name="old-start-date" value="<?= $old_start_date; ?>">
                <input type="hidden" name="old-end-date" value="<?= $old_end_date; ?>">
            </fieldset>
            <?php } ?>
            <input type="submit" value="Save" class="btn">
        </form>
    <?php } // (count($hostels) > 0) ?>
    <?php endif; ?>
</div><!-- box -->
