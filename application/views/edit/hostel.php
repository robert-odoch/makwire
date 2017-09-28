<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4><?= $heading; ?></h4>
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
                <label for='hostel'>Hostel</label>
                <?php if (isset($hostels)) { ?>
                    <select name='hostel' id='hostel' class='form-control' required>
                        <optgroup>
                            <?php
                            foreach ($hostels as $hostel) {
                                print "<option value='{$hostel['hostel_id']}'";
                                if (isset($hostel_id) && $hostel_id == $hostel['hostel_id']) {
                                    print ' selected';
                                }
                                print ">{$hostel['hostel_name']}</option>";
                            }
                            ?>
                        </optgroup>
                    </select>
                <?php
                } else { // Editing an existing hosel.
                    print "<p>{$user_hostel['hostel_name']}</p>";
                }
                ?>
            </div>
        </fieldset>

        <?php require_once(__DIR__ . '/../common/show-date-input.php'); ?>

        <?php if (isset($user_hostel)) { ?>
            <fieldset>
                <input type='hidden' name='hostel-id' value='<?= $user_hostel['hostel_id']; ?>'>
                <input type='hidden' name='user-hostel-id' value='<?= $user_hostel['id']; ?>'>
            </fieldset>
        <?php } ?>

        <input type='submit' value='Save' class='btn btn-sm'>
    </form>
</div><!-- box -->
