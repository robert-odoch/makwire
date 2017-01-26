<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>Edit Programme Details</h4>
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <p><?= "{$success_message}"; ?></p>
    </div>
    <?php else: if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <p><?= "{$error_message}"; ?></p>
    </div>
    <?php endif; ?>
    <form action="" method="post" accept-charset="utf-8" role="form">
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
        <?php require_once("common/show-date-input.php"); ?>
        <fieldset>
            <div class="form-group">
                <label for="year-of-study">Year of Study</label>
                <select name="ystudy" id="year-of-study">
                    <optgroup>
                        <option value="1">one</option>
                        <option value="2">two</option>
                        <option value="3">three</option>
                        <option value="4">four</option>
                        <option value="5">five</option>
                        <option value="0">I graduated</option>
                    </optgroup>
                </select>
            </div>
        </fieldset>
        <input type="submit" value="Save" class="btn">
    </form>
    <?php endif; ?>
</div><!-- box -->
