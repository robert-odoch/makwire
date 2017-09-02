<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4><?= $heading; ?></h4>
    <?php if (isset($error_message)) { ?>
        <div class='alert alert-danger' role='alert'>
            <p>
                <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                <span class='sr-only'>Error: </span>
                <?= $error_message; ?>
            </p>
        </div>
    <?php } ?>

    <form action='<?= $form_action ?>' method='post' accept-charset='utf-8' role='form'>
        <fieldset>
            <div class='form-group'>
                <label for='school'>School</label>
                <?php if (isset($schools)) { ?>
                    <select name='school' id='school' class='form-control' required>
                        <optgroup>
                        <?php
                        foreach ($schools as $s) {
                            print "<option value='{$s['school_id']}'";
                            if (isset($school_id) && ($school_id == $s['school_id'])) {
                                print ' selected';
                            }
                            print ">{$s['school_name']}</option>";
                        }
                        ?>
                        </optgroup>
                    </select>
                <?php
                } else {  // Editing a school.
                    print "<p>{$user_school['school_name']}</p>";
                }
                ?>
            </div>
        </fieldset>

        <?php
        require_once(dirname(__FILE__) . '/../common/show-date-input.php');

        // Only show this if the user is  editing an existing school.
        if (isset($user_school)) {
        ?>
        <fieldset>
            <input type='hidden' name='user-school-id' value='<?php echo $user_school['id']; ?>'>
            <input type='hidden' name='school-id' value='<?= $user_school['school_id']; ?>'>
        </fieldset>
        <?php } ?>
        <input type='submit' value='Save' class='btn btn-sm'>
    </form>
</div><!-- box -->
