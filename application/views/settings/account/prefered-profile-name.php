<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php
            define('PAGE', 'account');
            require_once(dirname(__FILE__) . '/../../common/account-settings-nav.php');
            ?>

            <div class="main-content">
                <div class="box">
                    <h4>Prefered display name</h4>
                    <form action="<?= base_url('account/set-prefered-name'); ?>"
                        method="post" accept-charset="utf-8" role="form">
                        <?php foreach ($name_combinations as $n) { ?>
                        <div class="radio">
                            <label for="<?= implode('-', explode(' ', $n)); ?>">
                                <input type="radio" name="prefered_name"
                                    value="<?= $n; ?>" id="<?= implode('-', explode(' ', $n)); ?>"
                                    <?php if ($n == $primary_user) print ' checked'; ?>>
                                <?= $n; ?>
                            </label>
                        </div>
                        <?php } ?>
                        <input type="submit" name="submit" value="Save changes" class="btn btn-sm">
                    </form>
                </div>
