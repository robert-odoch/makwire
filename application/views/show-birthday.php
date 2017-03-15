<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('common/user-page-start.php');
?>

<div class="box">
    <h4>
        <div class="media">
            <div class="media-left">
                <img src="<?= $user_profile_pic_path; ?>" class="media-object" alt="<?= $user; ?>">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <?php
                    if ($is_visitor) {
                        $dob_array = explode('-', $dob);
                        if (date_create(date('Y-m-d')) ==
                                date_create(($dob_array[0]+$age) . "-{$dob_array[1]}-{$dob_array[2]}")) {
                            print "Today is <a href='" . base_url("user/{$user_id}") .
                                    "'><strong class='object'>" .
                                    format_name($user, '</strong></a>') . " birthday";
                        }
                        else {
                            $birthday = date_create(($dob_array[0] + $age) . "-{$dob_array[1]}-{$dob_array[2]}");
                            $birthday = $birthday->format('F j, Y');
                            print "<a href='" . base_url("user/{$user_id}") .
                                    "'><strong class='object'>" .
                                    format_name($user, '</strong></a>') . " birthday was on {$birthday}";
                        }
                    }
                    else {
                        print "<a href='" . base_url("user/{$user_id}") .
                                "'><strong class='object'>{$user}</strong></a>";
                    }
                    ?>

                </h4>
                <small><?= $age; ?> years old</small>
            </div>
        </div>
    </h4>
    <form action="<?= base_url("user/send-birthday-message/{$user_id}/{$age}") ?>"
        method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="birthday-message">
                    Leave a birthday message.
                </label>
                <input type="text" name="birthday-message" id="birthday-message"
                    placeholder="your message..." class="fluid
                    <?php
                    if (isset($error_message)) {
                        print " has-error";
                    }
                    ?>">
                    <?php
                    if (isset($error_message)) {
                        print "<span class='error'>{$error_message}</span>";
                    }
                    ?>
            </div>
        </fieldset>
        <input type="submit" name="submit" value="Send" class="btn btn-sm">
    </form>
</div>

<?php if (count($birthday_messages) > 0) { ?>
<div class="box birthday-messages">
    <h4>Birthday Messages</h4>
    <?php foreach ($birthday_messages as $msg) { ?>
        <div class="media">
            <div class="media-left">
                <img src="<?= $msg['profile_pic_path']; ?>" alt="<?= $msg['sender']; ?>"
                    class="media-object">
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?= base_url("user/{$msg['sender_id']}"); ?>">
                        <strong><?= $msg['sender']; ?></strong>
                    </a>
                </h4>
                <p class="message"><?= $msg['message']; ?></p>
                <small class="time">
                    <span class="glyphicon glyphicon-time"></span> <?= $msg['timespan']; ?> ago
                </small>
                <?php
                if ($msg['user_id'] == $_SESSION['user_id'] &&
                    !$msg['liked']) {
                    print "<a href='" . base_url("birthday-message/like/{$msg['id']}") .
                            "'>Like</a>";
                }
                ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php if ($has_next) { ?>
<div class="box more">
    <a href="<?= base_url("user/birthday/{$user_id}/{$age}/{$next_offset}"); ?>">
        View more messages.
    </a>
</div>
<?php } ?>
<?php }  // (count($birthday_messages) > 0) ?>
