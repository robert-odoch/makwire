<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="wrap-single">
    <div role="main" class="main">
        <div class="box">
            <h4>Sign Up: step 1 of 3</h4>
            <?php if (isset($info_message)) { ?>
                <div class="alert alert-info">
                    <p><?= $info_message; ?></p>
                </div>
            <?php } elseif (isset($success_message)) { ?>
                <div class="alert alert-success">
                    <p><?= $success_message; ?></p>
                </div>
            <?php } else { ?>
                <form action="<?= base_url('register/step-one'); ?>" method="post"
                    accept-charset="utf-8" role="form">
                    <fieldset>
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <span class="help-block">Your Makerere University email address
                            e.g., name@college.mak.ac.ug</span>
                            <input type="email" name="email" id="email" size="30"
                            <?php
                            if (isset($email)) {
                                print " value = '{$email}'";
                            }
                            if (isset($error_message)) {
                                print ' class="has-error"';
                            }
                            ?>>
                            <?php
                            if (isset($error_message)) {
                                print "<span class='error'>{$error_message}</span>";
                            }
                            ?>
                        </div>
                    </fieldset>
                    <input type="submit" value="Next &rarr;" class="btn btn-sm">
                </form>
            <?php } ?>
        </div><!-- box -->
