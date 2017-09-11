<?php
defined('BASEPATH') OR exit('No direct script access allowed.');
require_once(dirname(__FILE__) . '/../common/user-page-start.php');

require_once(dirname(__FILE__) . '/../common/comment-or-reply.php');
?>

<div class='box'>
    <h4>Edit <?php echo $object; ?></h4>
    <form action='<?php echo base_url("{$object}/edit/{$$object['comment_id']}") ?>' method='post'>
        <div class='form-group'>
            <label for='<?php echo $object; ?>'><?php echo $object; ?></label>
            <input type='text' name='<?php echo $object; ?>'  class='fluid'
                    value='<?php echo $$object['comment']; ?>' autofocus required>
            <?php if (!empty($error)) { echo "<span class='error'>{$error}</span>"; } ?>
        </div>

        <input type='submit' value='Save' class='btn btn-sm'>
    </form>
</div>
