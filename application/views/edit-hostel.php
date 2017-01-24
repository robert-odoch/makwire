<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit Hostel</h4>
                            <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <p><?= "{$success_message}"; ?></p>
                            </div>
                            <?php else: if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <p><?= "{$error_message}"; ?></p>
                            </div>
                            <?php endif; ?>
                            <form action="<?= base_url("user/edit-hostel")?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="hostel">Select Hostel</label>
                                        <select name="hostel" id="hostel" class="form-control">
                                            <optgroup>
                                                <?php foreach ($hostels as $hostel): ?>
                                                <option value="<?= $hall['hostel_id']; ?>"><?= $hall['hostel_name']; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
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
