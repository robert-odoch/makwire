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
        foreach($items as $item) {
            switch ($item['source_type']) {
            case 'post':
                $post = $item['post'];
                require(dirname(__FILE__) . '/../common/post.php');
                break;
            case 'photo':
                $photo = $item['photo'];
                require(dirname(__FILE__) . '/../common/photo.php');
                break;
            case 'video':
                $video = $item['video'];
                require(dirname(__FILE__) . '/../common/video.php');
                break;
            case 'link':
                $link = $item['link'];
                require(dirname(__FILE__) . '/../common/link.php');
                break;
            default:
                # do nothing...
                break;
            }
        }
        if ($has_next) {
            print "<div class='box more'>";
            if ($page == 'news-feed') {
                print '<a href="' . base_url("news-feed/{$next_offset}") .
                        '">Show more</a>';
            }
            else if ($page == 'timeline') {
                print '<a href="' . base_url("user/{$user_id}/{$next_offset}") .
                        '">Show more</a>';
            }
            print '</div>';
        }
    }
    ?>
