<?php
defined('BASEPATH') OR exit('No direct script access allowed.');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');
?>

<div class='box'>
    <h4>Edit message</h4>
    <form action='<?php echo base_url("user/edit-message/{$message['message_id']}") ?>' method='post'>
        <div class='form-group'>
            <label for='message'>message</label>
            <input type='text' name='message'  class='fluid'
                    value='<?php echo $message['message']; ?>' autofocus required>
            <?php if (!empty($error)) { echo "<span class='error'>{$error}</span>"; } ?>
        </div>

        <input type='submit' value='Save' class='btn btn-sm'>
    </form>
</div>
