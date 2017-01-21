<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
                    <?php if (count($suggested_users) > 0): ?>
                    <h4>People you may know</h4>
                    <ul class="suggested-users">
                        <?php foreach ($suggested_users as $su): ?>
                        <li>
                            <figure><img src="<?= base_url("images/kasumba.jpg"); ?>" alt="<?= $su['full_name']; ?>"></figure>
                            <span><a href="<?= base_url("user/index/{$su['user_id']}"); ?>"><?= $su['full_name']; ?></a> <a href="<?= base_url("user/add_friend/{$su['user_id']}"); ?>" class="btn">Add friend</a></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
