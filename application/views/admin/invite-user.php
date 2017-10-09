<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'invite-users');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4>Invite user</h4>

    <?php if ( ! empty($info_message)) show_message($info_message, 'info'); ?>
    <?php if ( ! empty($success_message)) show_message($success_message, 'success'); ?>

    <form action='' method='post' accept-charset='utf-8'>
        <div class='form-group'>
            <label for='user-email'>Email address</label>
            <input type='email' name='email' id='user-email'
                <?php if ( ! empty($email)) print " value='{$email}'" ?>class='fluid
            <?php if ( ! empty($error)) { print " has-error"; } ?>' required>
            <?php if ( ! empty($error)) { print "<span class='error'>{$error}</span>"; } ?>
        </div>
        <div class='form-group'>
            <label for='user-college'>College</label>
            <select name='college' id='user-college'>
                <?php foreach ($colleges as $c): ?>
                    <option value='<?= $c['college_id']; ?>'><?= $c['short_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class='checkbox'>
            <label for='return-here' style='font-weight: normal;'>
                <input type='checkbox' name='return' value='true' id='return-here' checked>
                Return to this page
            </label>
        </div>

        <input type='submit' value='Invite' class='btn btn-sm'>
    </form>
</div><!-- .box -->
