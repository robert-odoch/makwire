<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4><?= $heading; ?></h4>
    <?php if (isset($error_message)) { ?>
        <div class='alert alert-danger' role='alert'>
            <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
            <span class='sr-only'>Error: </span>
            <p><?= $error_message; ?></p>
        </div>
    <?php } ?>

    <form action='<?= $form_action ?>' method='post' accept-charset='utf-8' role='form'>
        <fieldset>
            <div class='form-group'>
                <label for='school'>School</label>
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
            </div>
        </fieldset>

        <fieldset>
            <div class='form-group'>
                <label for='level'>Level</label>
                <select name='level' id='level' class='form-control' required>
                    <optgroup>
                        <option value='undergraduate'
                            <?php if (isset($level) && $level == 'undergraduate') print ' selected'; ?>
                            >Undergraduate</option>
                        <option value='graduate'
                            <?php if (isset($level) && $level == 'graduate') print ' selected'; ?>
                            >Graduate</option>
                        <option value='postgraduate'
                            <?php if (isset($level) && $level == 'postgraduate') print ' selected'; ?>
                            >Postgraduate</option>
                    </optgroup>
                </select>
            </div>
        </fieldset>

        <?php require_once(__DIR__ . '/../common/show-date-input.php'); ?>
        <input type='submit' value='Save' class='btn btn-sm'>
    </form>
</div><!-- box -->
