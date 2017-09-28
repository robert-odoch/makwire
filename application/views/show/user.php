<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PAGE', $page);
require_once(__DIR__ . '/../common/user-page-start.php');
if ($page !== 'news-feed') {
    require_once(__DIR__ . '/../common/secondary-user-nav.php');
}

if (!$is_visitor) {
?>
    <div id='update-status' class='box'>

        <?php
        define('STATUS', 'post');
        require_once(__DIR__ . '/../common/status-nav.php');
        require_once(__DIR__ . '/../forms/new-post.php');
        ?>
    </div>
<?php } // (!$is_visitor) ?>

    <?php
    if (empty($items) && $is_visitor) { ?>
        <div class='box'>
            <div class='alert alert-info' role='alert'>
                <span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span>
                <p>When they do, their status updates will show up here.</p>
            </div>
        </div>
    <?php
    } else {
        require_once(__DIR__ . '/../common/items.php');
    }
    ?>
