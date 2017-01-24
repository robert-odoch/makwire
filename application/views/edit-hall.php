<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit Hall</h4>
                            <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <p><?= "{$success_message}"; ?></p>
                            </div>
                            <?php else: if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <p><?= "{$error_message}"; ?></p>
                            </div>
                            <?php endif; ?>
                            <form action="<?= base_url("user/edit-hall"); ?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="hall">Select Hall</label>
                                        <select name="hall" id="hall" class="form-control">
                                            <optgroup>
                                                <?php foreach ($halls as $hall): ?>
                                                <option value="<?= $hall['hall_id']; ?>"><?= $hall['hall_name']; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <div class="radio-inline">
                                            <label for="resident">
                                                <input type="radio" name="resident" id="resident" value="resident"> Resident
                                            </label>
                                        </div>
                                        <div class="radio-inline">
                                            <label for="non-resident">
                                                <input type="radio" name="resident" id="non-resident" value="non-resident"checked> Non-resident
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                                <?php require_once("common/show-date-input.php"); ?>
                                <input type="submit" value="Save" class="btn">
                            </form>
                            <?php endif; ?>
                        </div><!-- box -->
                    </div><!-- .main-content -->
                </div><!-- main -->

                <div class="suggestions">
                    <?php require_once("common/suggested-users.php"); ?>
                </div>
            </div>

            <div class="col-small">
                <?php require_once("common/active-users.php"); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->
