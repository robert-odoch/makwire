<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class="box">
    <h4><?= $heading; ?></h4>
    <?php if (isset($error_message)) { ?>
        <div class="alert alert-danger">
            <p>
                <span class"glyphicon glyphicon-exclamation-sign"></span>
                <?= $error_message; ?>
            </p>
        </div>
    <?php } ?>

    <form action="<?= $form_action ?>" method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="college">College</label>
                <?php if (isset($colleges)) { ?>
                    <select name="college" id="college" class="form-control">
                        <optgroup>
                        <?php
                        foreach ($colleges as $c) {
                            print "<option value='{$c['college_id']}'";
                            if (isset($college_id) && ($college_id == $c['college_id'])) {
                                print ' selected';
                            }
                            print ">{$c['college_name']}</option>";
                        }
                        ?>
                        </optgroup>
                    </select>
                <?php
                } else {  // Editing a college.
                    print "<p>{$user_college['college_name']}</p>";
                }
                ?>
            </div>

            <div class="form-group">
                <label for="school">School</label>
                <?php if (isset($schools)) { ?>
                    <select name="school" id="school" class="form-control">
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
                } else {  // Editing a college.
                    print "<p>{$user_college['school']['school_name']}</p>";
                }
                ?>
            </div>
        </fieldset>

        <?php
        require_once(dirname(__FILE__) . '/../common/show-date-input.php');

        // Only show this if the user is  editing an existing college.
        if (isset($user_college)) {
        ?>
        <fieldset>
            <input type="hidden" name = "user-college-id" value = "<?= $user_college['id']; ?>">
            <input type="hidden" name="college-id" value="<?= $user_college['college_id']; ?>">
            <input type="hidden" name="old-start-date" value="<?= $user_college['date_from']; ?>">
            <input type="hidden" name="old-end-date" value="<?= $user_college['date_to']; ?>">
            <input type="hidden" name = "school-id" value = "<?= $user_college['school']['school_id']; ?>">
        </fieldset>
        <?php } ?>
        <input type="submit" value="Save" class="btn btn-sm">
    </form>
</div><!-- box -->
