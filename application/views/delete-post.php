<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/common/user-page-start.php');
?>

<div class='box'>
    <div class='alert alert-warning'>
        <p>Are you sure you want to delete this post?</p>
        <form action='<?= base_url("post/delete/{$post['post_id']}"); ?>' method='post'>
            <input type='submit' value='Delete' class='btn btn-sm' style='background-color: red; border: 1px solid red;'>
            <a href='<?= base_url("user/post/{$post['post_id']}"); ?>' class='btn btn-sm'>Cancel</a>
        </form>
    </div>
</div>

<?php
require_once(dirname(__FILE__) . '/common/post.php');
?>
