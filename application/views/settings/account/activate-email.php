<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class='wrapper-md'>
    <div role='main' class='main'>
        <div class='box'>
            <h4>Verify your email address</h4>
            <?php
            if ( ! empty($info_message)) {
                show_message($info_message, 'info');
            }
            ?>
        </div><!-- box -->
