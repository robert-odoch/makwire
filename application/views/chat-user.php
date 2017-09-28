<?php defined('BASEPATH') OR exit('No direct script access allowed') ?>

<div class='chat'>
    <div class='media receiver'>
        <div class='media-left media-middle'>
            <img src='<?= $receiver['profile_pic_path']; ?>' class='media-object profile-pic-sm'>
        </div>
        <div class='media-body'>
            <h4>
                <a href='<?= base_url("user/{$receiver['user_id']}"); ?>'>
                    <?= $receiver['profile_name']; ?>
                </a>
            </h4>
            <div class='media-footer'>
                <a href='<?php echo base_url("user/send-message/{$receiver['user_id']}/0/1"); ?>'
                    class='refresh-chat' title='Refresh' data-toggle='tooltip' data-placement='left'>
                    <span class='fa fa-refresh' aria-hidden='true'></span>
                </a>
            </div>
        </div>
    </div>
    <div class='chat-content'>
        <?php require_once(__DIR__ . '/chat-messages.php'); ?>

        <div class='new-message'>
            <form action='<?= base_url("user/send-message/{$receiver['user_id']}"); ?>' method='post' class='send-message'>
                <table style='width: 100%'>
                    <tbody>
                        <tr>
                            <td>
                                <input type='text' name='message' class='fluid' placeholder='new message...' autofocus>
                            </td>
                            <td><input type='submit' value='Send' class='btn btn-sm'></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<a href='<?= base_url('user/chat'); ?>' class='btn btn-default btn-block back-btn'>&laquo; Active Users</a>
