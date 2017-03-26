<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class="box">
    <h4>Edit Country</h4>
    <?php if (isset($success_message)) { ?>
        <div class="alert alert-success">
            <p><?= $success_message; ?></p>
        </div>
    <?php } else { ?>
        <form action="<?= base_url("user/edit-country"); ?>" method="post"
            accept-charset="utf-8" role="form">
            <fieldset>
                <div class="form-group">
                    <label for="country">Select Country</label>
                    <select name="country" id="country" class="form-control">
                        <optgroup>
                            <?php foreach ($countries as $country) { ?>
                                <option value="<?= $country['country_id']; ?>">
                                    <?= $country['country_name']; ?>
                                </option>
                            <?php } ?>
                            <option value="none">I don't see my country in this list</option>
                        </optgroup>
                    </select>
                </div>
            </fieldset>

            <input type="submit" value="Submit" class="btn btn-sm">
        </form>
    <?php } ?>
</div><!-- box -->
