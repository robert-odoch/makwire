<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>
                        <div class="box">
                            <h4>Edit College</h4>
                            <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <p><?= "{$success_message}"; ?></p>
                            </div>
                            <?php else: if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <p><?= "<strong>Error:</strong> {$error_message}"; ?></p>
                            </div>
                            <?php endif; ?>
                            <form action="<?= base_url("user/edit_college"); ?>" method="post" accept-charset="utf-8" role="form">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="college">College</label>
                                        <select name="college" id="college" class="form-control">
                                            <optgroup>
                                            <?php
                                            foreach ($colleges as $c) {
                                                print "<option value='{$c['college_id']}'>{$c['college_name']}</option>";
                                            }
                                            ?>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="school">School</label>
                                        <select name="school" id="school" class="form-control">
                                            <optgroup>
                                            <?php
                                            foreach ($schools as $s) {
                                                print "<option value='{$s['school_id']}'>{$s['school_name']}</option>";
                                            }
                                            ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </fieldset>
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
