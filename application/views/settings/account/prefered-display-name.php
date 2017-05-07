<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class="wrapper">
    <div class="col-large">
        <div role="main" class="main user-home">
            <?php require_once(dirname(__FILE__) . '/../../common/account-settings-nav.php'); ?>

            <div class="main-content">
                <div class="box">
                    <h4>Prefered display name</h4>
                    <form action="<?= base_url('account/set-prefered-name'); ?>" method="post" accept-charset="utf-8" role="form">
                        <fieldset>
                            <div class="radio">
                                <label for="name1">
                                    <input type="radio" name="name" value="Odoch Robert" id="name1">
                                    Odoch Robert
                                </label>
                            </div>
                            <div class="radio">
                                <label for="name2">
                                    <input type="radio" name="name" value="Robert Odoch" id="name2">
                                    Robert Odoch
                                </label>
                            </div>
                        </fieldset>

                        <input type="submit" name="submit" value="Update" class="btn btn-sm">
                    </form>
                </div>
