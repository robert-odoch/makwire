<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4><?= $heading; ?></h4>
    <?php if (isset($halls) && empty($halls)): ?>
        <div class='alert alert-info'>
            <span class='fa fa-info-circle'></span>
            <p>
                Dear user, thanks for your interest in adding
                a hall to your profile. However, at the moment we don't have
                the list of halls on record and do kindly request that you give us some time
                to compile the list.<br><br>
                Thanks for your patience!
            </p>
        </div>

    <?php else: ?>
        <?php if (isset($error_message)) { ?>
            <div class='alert alert-danger' role='alert'>
                <span class='fa fa-exclamation-circle' aria-hidden='true'></span>
                <span class='sr-only'>Error: </span>
                <p><?= $error_message; ?></p>
            </div>
        <?php } ?>

        <form action='<?= $form_action; ?>' method='post' accept-charset='utf-8' role='form'>
            <fieldset>
                <div class='form-group'>
                    <label for='hall'>Hall</label>
                    <?php if (isset($halls)) { ?>
                        <select name='hall' id='hall' class='form-control' required>
                            <optgroup>
                                <?php
                                foreach ($halls as $hall) {
                                    print "<option value='{$hall['hall_id']}'";
                                    if (isset($hall_id) && $hall_id == $hall['hall_id']) {
                                        print " selected";
                                    }
                                    print ">{$hall['hall_name']}</option>";
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
                <div class='input-group'>
                    <p><strong>Status</strong></p>
                    <div class='radio-inline'>
                        <label for='resident'>
                            <input type='radio' name='resident' id='resident' value='1'
                            <?php
                            if (isset($resident) && ($resident == 1)) {
                                print(" checked");
                            }
                            ?>
                            > Resident
                        </label>
                    </div>
                    <div class='radio-inline'>
                        <label for='non-resident'>
                            <input type='radio' name='resident' id='non-resident' value='0'
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
            <?php require_once(__DIR__ . '/../common/show-date-input.php'); ?>
            <?php if (isset($user_hall)) { ?>
                <fieldset>
                    <input type='hidden' name='user-hall-id' value='<?= $user_hall['id']; ?>'>
                    <input type='hidden' name='hall-id' value='<?= $user_hall['hall_id']; ?>'>
                </fieldset>
            <?php } ?>

            <input type='submit' value='Save' class='btn btn-sm'>
        </form>
    <?php endif; ?>
</div><!-- box -->
