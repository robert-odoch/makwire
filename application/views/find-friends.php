<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', 'find-friends');
require_once('common/user-page-start.php');
?>

<div class='box'>
    <h4>Find friends</h4>
    <form action='' method='post' accept-charset='utf-8' role='form'>
        <fieldset>
            <div class='form-group'>
                <label for='query'>Search by full name or email address</label>
                <input type='search' name='query' id='query' class='fluid
                <?php if (isset($error)) { print ' has-error'; } ?>' required>
                <?php if (isset($error)) { print "<span class='error'>{$error}</span>"; } ?>
            </div>
        </fieldset>
        <input type='submit' value='Search' class='btn btn-sm'>
    </form>
</div><!-- .box -->

<?php if (isset($search_results) && count($search_results) > 0) { ?>
    <div class='box'>
        <h4>Search results</h4>
        <div class='users'>
            <?php foreach ($search_results as $user) { ?>
                <div class='media separated'>
                    <div class='media-left'>
                        <img src='<?= $user['profile_pic_path']; ?>'
                            alt='<?= $user['profile_name']; ?>' class='media-object profile-pic-md'>
                    </div>
                    <div class='media-body'>
                        <h4 class='media-heading'>
                            <a href='<?= base_url("user/profile/{$user['user_id']}"); ?>'>
                                <?= $user['profile_name']; ?>
                            </a>
                        </h4>
                        <a href='<?= base_url("user/add-friend/{$user['user_id']}"); ?>'
                                class='btn btn-xs'>Add friend</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php if ($has_next) { ?>
        <div class='box more'>
            <a href='<?= base_url("user/find-friends/{$next_offset}"); ?>'>Show more results</a>
        </div>
    <?php } ?>
<?php } elseif (isset($search_results)) { ?>
    <div class='box'>
        <h4>Search results</h4>
        <div class='alert alert-info' role='alert'>
            <span class='fa fa-info-circle' aria-hidden='true'></span>
            <p>You search query returned no results.</p>
        </div>
    </div>
<?php } ?>

<?php if (isset($suggested_users) && count($suggested_users) > 0) { ?>
    <div class='box'>
        <h4>People you may know</h4>
        <div class='suggested-users'>
            <?php foreach ($suggested_users as $user) { ?>
            <div class='media separated'>
                <div class='media-left'>
                    <img src='<?= $user['profile_pic_path']; ?>'
                            alt='<?= $user['profile_name']; ?>' class='media-object profile-pic-md'>
                </div>
                <div class='media-body'>
                    <h4 class='media-heading'>
                        <a href='<?= base_url("user/profile/{$user['user_id']}"); ?>'>
                            <?= $user['profile_name']; ?>
                        </a>
                    </h4>
                    <a href='<?= base_url("user/add-friend/{$user['user_id']}"); ?>'
                            class='btn btn-xs'>Add friend</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div><!-- .box -->

    <?php if ($has_next) { ?>
    <div class='box more'>
        <a href='<?= base_url("user/find-friends/{$next_offset}"); ?>'>Show more suggestions</a>
    </div>
    <?php } ?>
<?php } ?>
