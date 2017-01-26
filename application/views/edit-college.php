<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Edit College</h4>
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <p><?= "{$success_message}"; ?></p>
    </div>
    <?php else: if (isset($error_messages)): ?>
    <div class="alert alert-danger">
        <?php
        foreach ($error_messages as $error) {
            print("<p>{$error}</p>");
        }
        ?>
    </div>
    <?php endif; ?>
    <form action="<?= base_url("user/edit_college"); ?>" method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="college">College</label>
                <select name="college" id="college" class="form-control">
                    <optgroup>
                    <?php
                    foreach ($colleges as $c) {
                        print("<option value='{$c['college_id']}'");
                        if (isset($college_id) && ($college_id == $c['college_id'])) {
                            print(" selected");
                        }
                        print(">{$c['college_name']}</option>");
                    }
                    ?>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label for="school">School</label>
                <select name="school" id="school" class="form-control">
                    <optgroup>
                    <?php
                    foreach ($schools as $s) {
                        print("<option value='{$s['school_id']}'");
                        if (isset($school_id) && ($school_id == $s['school_id'])) {
                            print(" selected");
                        }
                        print(">{$s['school_name']}</option>");
                    }
                    ?>
                    </optgroup>
                </select>
            </div>
        </fieldset>
        <?php require_once("common/show-date-input.php"); ?>
        <input type="submit" value="Save" class="btn">
    </form>
    <?php endif; ?>
</div><!-- box -->
