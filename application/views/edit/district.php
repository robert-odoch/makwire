<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(__DIR__ . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4><?= $heading; ?></h4>
    <?php if (isset($districts)) { ?>
        <?php if (count($districts) > 0) { ?>
            <p>Districts that matched</p>
            <ul>
                <?php foreach($districts as $district) { ?>
                    <li>
                        <a href='<?= base_url("profile/add-district/{$district['district_id']}"); ?>'>
                            <?= $district['district_name']; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>
                It seems the district you entered <em><b><?= "{$district}"; ?></b></em>
                is not in our records, please make sure that you have spelt it correcty.
                If you are sure that is the correct spelling, then you can
                <a href='<?= base_url('request-admin/add-district'); ?>'>
                    request
                </a> the administrator to add it to the records.
            </p>
        <?php } // count($districts) > 0). ?>

    <?php } else { ?>

        <form action='<?= base_url('profile/add-district'); ?>' method='post'
                accept-charset='utf-8' role='form'>
            <fieldset>
                <div class='form-group'>
                    <label for='district'>District</label>
                    <input type='text' name='district' id='district' size='30' required>
                </div>
            </fieldset>
            <input type='submit' value='Submit' class='btn btn-sm'>
        </form>
    <?php } // isset($districts). ?>
</div><!-- box -->
