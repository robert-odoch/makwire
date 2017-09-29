<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class='wrapper-lg'>
    <div class='col-large'>
        <div role='main' class='main user-home'>
            <?php
            define('PAGE', 'blocked-users');
            require_once(__DIR__ . '/../common/account-settings-nav.php');
            ?>

            <div class='main-content'>
                <div class='box'>
                    <h4>Block a user</h4>
                    <h5>Blocking a user prevents that user from performing the following:</h5>
                    <ul class='bullet'>
                        <li>Liking, commenting, or sharing your posts, photos, videos.</li>
                        <li>Sending you a message be it on chat or your birthday.</li>
                    </ul>
                    <form action='' method='post' accept-charset='utf-8' role='form'>
                        <fieldset>
                            <div class='form-group'>
                                <label for='search-query'>Search by full name or email address</label>
                                <input type='search' name='query' id='search-query' class='fluid
                                <?php if (isset($error_message)) print ' has-error'; ?>'>
                                <?php if (isset($error_message)) print "<span class='error'>{$error_message}</span>"; ?>
                            </div>
                        </fieldset>
                        <input type='submit' value='Search' class='btn btn-sm'>
                    </form>
                </div>

                <div class='box'>
                    <?php if (isset($search_results)): ?>
                        <h4>Search Results</h4>
                        <?php
                        if (empty($search_results)):
                            show_message('Your search query returned no results.', 'info');
                        else:
                        ?>
                            <div class='users'>
                                <?php foreach ($search_results as $sr): ?>
                                <div class='media'>
                                    <div class='media-left'>
                                        <img src='<?= $sr['profile_pic_path']; ?>'
                                                class='media-object' alt='<?= $sr['profile_name']; ?>'>
                                    </div>
                                    <div class='media-body'>
                                        <h4 class='media-heading'>
                                            <a href='<?= base_url("user/{$sr['user_id']}"); ?>'>
                                                <?= $sr['profile_name']; ?>
                                            </a>
                                        </h4>
                                        <a href='<?= base_url("settings/block-user/{$sr['user_id']}"); ?>'
                                                class='btn btn-xs'>Block user</a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; // empty($search_results) ?>

                    <?php else: ?>
                        <h4>Blocked Users</h4>
                        <?php
                        if (empty($blocked_users)):
                            show_message("You haven't blocked any users.", 'info');
                        else:
                            foreach ($blocked_users as $bu):
                        ?>
                                <div class='media'>
                                <div class='media-left'>
                                    <img src='<?= $bu['profile_pic_path']; ?>'
                                            class='media-object' alt='<?= $bu['profile_name']; ?>'>
                                </div>
                                <div class='media-body'>
                                    <h4 class='media-heading'>
                                        <a href='<?= base_url("user/{$bu['user_id']}"); ?>'>
                                            <?= $bu['profile_name']; ?>
                                        </a>
                                    </h4>
                                    <a href='<?= base_url("settings/unblock-user/{$bu['user_id']}"); ?>'
                                        class='btn btn-xs'>Unblock user</a>
                                </div>
                            </div>
                        <?php
                            endforeach;
                        endif; // empty($blocked_users)
                        ?>
                    <?php endif; // isset($search_results) ?>
                </div>
