<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4><?= $heading; ?></h4>
    <?php if (isset($error_message)) { ?>
    <div class="alert alert-danger">
        <p><?= "{$error_message}"; ?></p>
    </div>
    <?php } ?>

    <form action="<?= $form_action; ?>" method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="hall">Hall</label>
                <?php if (isset($halls)) { ?>
                <select name="hall" id="hall" class="form-control">
                    <optgroup>
                        <?php
                        foreach ($halls as $hall) {
                            print("<option value='{$hall['hall_id']}'");
                            if (isset($hall_id) && $hall_id == $hall['hall_id']) {
                                print(" selected");
                            }
                            print(">{$hall['hall_name']}</option>");
                        }
                        ?>
                    </optgroup>
                </select>
                <?php
                } else { // Editing an existing hall.
                    print "<p>{$user_hall['hall_name']}</p>";
                }
                ?>
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
        <?php if (isset($user_hall)) { ?>
        <fieldset>
            <input type="hidden" name="user-hall-id" value="<?= $user_hall['id']; ?>">
            <input type="hidden" name="hall-id" value="<?= $user_hall['hall_id']; ?>">
        </fieldset>
        <?php } ?>
        <input type="submit" value="Save" class="btn btn-sm">
    </form>
</div><!-- box -->
