<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
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

    <table class='table table-bordered'>
            <tr>
                <td><b>School</b></td>
                <td><?= $user_school['school_name']; ?></td>
            </tr>
            <tr>
                <td><b>Level</b></td>
                <td><?= ucfirst($user_school['level']); ?></td>
            </tr>
    </table>
    <form action='<?= $form_action ?>' method='post' accept-charset='utf-8' role='form'>
        <?php require_once(__DIR__ . '/../common/show-date-input.php'); ?>

        <fieldset>
            <input type='hidden' name='user-school-id' value='<?= $user_school['id']; ?>'>
            <input type='hidden' name='school-id' value='<?= $user_school['school_id']; ?>'>
        </fieldset>

        <input type='submit' value='Save' class='btn btn-sm'>
    </form>
</div><!-- box -->
