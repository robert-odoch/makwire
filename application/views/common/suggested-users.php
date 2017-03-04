<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (count($people_you_may_know) > 0) {
?>
    <h4>People you may know</h4>
    <ul class="suggested-users">
        <?php foreach ($people_you_may_know as $p) { ?>
        <li>
            <div class="media">
                <div class="media-left">
                    <img class="media-object" src="<?= $p['profile_pic_path']; ?>"
                    alt="<?= $p['profile_name']; ?>">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        <a href="<?= base_url("user/index/{$p['user_id']}"); ?>"><?= $p['profile_name']; ?></a>
                    </h4>
                    <a href="<?= base_url("user/add-friend/{$p['user_id']}"); ?>"
                        class="btn btn-sm">Add friend</a>
                </div>
            </div>
        </li>
        <?php } ?>
    </ul>
<?php
}
?>
