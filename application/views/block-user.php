<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php require_once('common/account-settings-nav.php'); ?>

            <div class="main-content">
                <div class="box">
                    <h4>Block a user</h4>
                    <h5>Blocking a user prevents that user from performing the following:</h5>
                    <ul class="bullet">
                        <li>Liking, commenting, or sharing your posts, photos, videos.</li>
                        <li>Sending you a message be it on chat or your birthday.</li>
                    </ul>
                    <form action="" method="post" accept-charset="utf-8" role="form">
                        <fieldset>
                            <div class="form-group">
                                <label for="query">Search by full name or email address</label>
                                <input type="search" name="query" id="query" class="fluid
                                <?php
                                if (isset($error)) {
                                    print ' has-error';
                                }
                                ?>">

                                <?php
                                if (isset($error)) {
                                    print "<span class='error'>{$error}</span>";
                                }
                                ?>
                            </div>
                        </fieldset>
                        <input type="submit" value="Search" class="btn btn-sm">
                    </form>
                </div>

                <div class="box">
                    <h4>Blocked Users</h4>
                    <div class="alert alert-info">
                        <p>You have not blocked any users.</p>
                    </div>
                </div>
