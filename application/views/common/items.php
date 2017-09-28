<?php
defined('BASEPATH') OR exit('No direct script access allowed');

foreach($items as $item) {
    switch ($item['source_type']) {
    case 'post':
        $post = $item['post'];
        require(__DIR__ . '/../common/post.php');
        break;
    case 'photo':
        $photo = $item['photo'];
        require(__DIR__ . '/../common/photo.php');
        break;
    case 'video':
        $video = $item['video'];
        require(__DIR__ . '/../common/video.php');
        break;
    case 'link':
        $link = $item['link'];
        require(__DIR__ . '/../common/link.php');
        break;
    default:
        # do nothing...
        break;
    }
}
if ($has_next) {
    print "<div class='box more'>";
    if ($page == 'news-feed') {
        print "<a href='" . base_url("news-feed/{$next_offset}") .
                "' class='news-feed'>Show more</a>";
    }
    else if ($page == 'timeline') {
        print "<a href='" . base_url("user/{$user_id}/{$next_offset}") .
                "' class='timeline'>Show more</a>";
    }
    print '</div>';
}
?>
