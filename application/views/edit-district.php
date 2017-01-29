<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4><?= $heading; ?></h4>
    <?php if (isset($success_message)) { ?>
        <div class="alert alert-success">
            <p><?= "{$success_message}"; ?></p>
        </div>
    <?php } elseif (isset($districts)) { ?>
        <?php if (count($districts) > 0) { ?>
            <p>Districts that matched</p>
            <ul>
                <?php foreach($districts as $district): ?>
                    <li><a href="<?= base_url("user/add-district/{$district['district_id']}"); ?>"><?= $district['district_name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php } else { ?>
            <p>
                It seems the district you entered <?= "<em><b>{$district}</b></em>"; ?> is not in our records. However, you can <a href="<?= base_url("request-admin/add-district"); ?>">request the
                administrator to add it to the records</a>.
            </p>
        <?php } // count($districts) > 0). ?>
    <?php } else { ?>
        <?php if (isset($error_message)) { ?>
        <div class="alert alert-danger">
            <p><?= "{$error_message}"; ?></p>
        </div>
        <?php } ?>

        <form action="<?= base_url("user/add-district"); ?>" method="post" accept-charset="utf-8" role="form">
            <fieldset>
                <div class="form-group">
                    <label for="district">District</label>
                    <input type="text" name="district" id="district" size="30">
                </div>
            </fieldset>
            <input type="submit" value="Submit" class="btn">
        </form>
    <?php } // isset($success_message). ?>
</div><!-- box -->
