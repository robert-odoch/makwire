<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('exceptions/NotFoundException.php');

class News_feed_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'user_model',
            'post_model', 'photo_model',
            'video_model'
        ]);
    }

    /**
     * Gets number of posts, photos, videos, and links on a user's news feed.
     *
     * @return number of posts, photos, videos, and links on this user's news feed.
     */
    public function get_num_news_feed_items()
    {
        $friends_ids = $this->user_model->get_friends_ids();
        // Add a zero element; so if network is empty the IN part of the query won't fail
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        /* Get IDs of shared posts, photos, videos, and links. */

        /// IDS of shared posts.
        $shared_posts_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (sharer_id IN(%s) AND subject_type = 'post')",
                                        $friends_ids);
        $shared_posts_ids_results = $this->utility_model->run_query($shared_posts_ids_sql)->result_array();
        foreach ($shared_posts_ids_results as $r) {
            $shared_posts_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_posts_ids[] = 0;
        $shared_posts_ids = implode(',', $shared_posts_ids);

        /// IDs of shared photos.

        $shared_photos_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                         "WHERE (sharer_id IN(%s) AND subject_type = 'photo')",
                                         $friends_ids);
        $shared_photos_ids_results = $this->utility_model->run_query($shared_photos_ids_sql)->result_array();
        foreach ($shared_photos_ids_results as $r) {
            $shared_photos_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_photos_ids[] = 0;
        $shared_photos_ids = implode(',', $shared_photos_ids);

        /// IDs of shared videos.

        $shared_videos_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                         "WHERE (sharer_id IN(%s) AND subject_type = 'video')",
                                         $friends_ids);
        $shared_videos_ids_results = $this->utility_model->run_query($shared_videos_ids_sql)->result_array();
        foreach ($shared_videos_ids_results as $r) {
            $shared_videos_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_videos_ids[] = 0;
        $shared_videos_ids = implode(',', $shared_videos_ids);

        /// IDs of shared links.

        $shared_links_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                         "WHERE (sharer_id IN(%s) AND subject_type = 'link')",
                                         $friends_ids);
        $shared_links_ids_results = $this->utility_model->run_query($shared_links_ids_sql)->result_array();
        foreach ($shared_links_ids_results as $r) {
            $shared_links_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_links_ids[] = 0;
        $shared_links_ids = implode(',', $shared_links_ids);

        ///

        /* *** */

        $num_posts_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                 "WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND " .
                                        "activity = 'post')",
                                 $friends_ids, $shared_posts_ids);
        $num_posts = $this->utility_model->run_query($num_posts_sql)->row_array()['COUNT(source_id)'];

        $num_photos_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                 "WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND " .
                                        "activity IN('photo','profile_pic_change'))",
                                 $friends_ids, $shared_photos_ids);
        $num_photos = $this->utility_model->run_query($num_photos_sql)->row_array()['COUNT(source_id)'];

        $num_videos_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                 "WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND " .
                                        "activity = 'video')",
                                 $friends_ids, $shared_videos_ids);
        $num_videos = $this->utility_model->run_query($num_videos_sql)->row_array()['COUNT(source_id)'];

        $num_links_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                 "WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND " .
                                        "activity = 'link')",
                                 $friends_ids, $shared_links_ids);
        $num_links = $this->utility_model->run_query($num_links_sql)->row_array()['COUNT(source_id)'];

        $num_shared_items_sql = sprintf("SELECT COUNT(source_id) FROM activities " .
                                                    "WHERE (actor_id IN(%s) AND activity = 'share' AND " .
                                                    "source_type IN('post','photo','video','link') AND subject_id != %d)",
                                                    $friends_ids, $_SESSION['user_id']);
        $num_shared_items = $this->utility_model->run_query($num_shared_items_sql)->row_array()['COUNT(source_id)'];

        return ($num_posts + $num_photos + $num_videos + $num_links + $num_shared_items);
    }

    /**
     * Gets posts, photos, videos, and links to be shown on a user's news feed.
     *
     * @param $offset
     * @param $limit
     * @return number of posts, photos, videos, and links to be shown on this user's news feed.
     */
    public function get_news_feed_items($offset, $limit)
    {
        $friends_ids = $this->user_model->get_friends_ids();

        // Add a zero element; so if network is empty the IN part of the query won't fail
        $friends_ids[] = 0;
        $friends_ids = implode(',', $friends_ids);

        /*
         * Get IDs of shared posts and photos.
         * IDs are got seperately b'se an ID of a shared post can be equal to an ID of
         * a shared photo as they are in different tables.
         */

        /// IDS of shared posts.
        $shared_posts_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (sharer_id IN(%s) AND subject_type = 'post')",
                                        $friends_ids);
        $shared_posts_ids_results = $this->utility_model->run_query($shared_posts_ids_sql)->result_array();
        foreach ($shared_posts_ids_results as $r) {
            $shared_posts_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_posts_ids[] = 0;
        $shared_posts_ids = implode(',', $shared_posts_ids);

        /// IDs of shared photos.

        $shared_photos_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (sharer_id IN(%s) AND subject_type = 'photo')",
                                        $friends_ids);
        $shared_photos_ids_results = $this->utility_model->run_query($shared_photos_ids_sql)->result_array();
        foreach ($shared_photos_ids_results as $r) {
            $shared_photos_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_photos_ids[] = 0;
        $shared_photos_ids = implode(',', $shared_photos_ids);

        /// IDs of shared videos.

        $shared_videos_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (sharer_id IN(%s) AND subject_type = 'video')",
                                        $friends_ids);
        $shared_videos_ids_results = $this->utility_model->run_query($shared_videos_ids_sql)->result_array();
        foreach ($shared_videos_ids_results as $r) {
            $shared_videos_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_videos_ids[] = 0;
        $shared_videos_ids = implode(',', $shared_videos_ids);

        /// IDs of shared links.

        $shared_links_ids_sql = sprintf("SELECT DISTINCT subject_id FROM shares " .
                                        "WHERE (sharer_id IN(%s) AND subject_type = 'link')",
                                        $friends_ids);
        $shared_links_ids_results = $this->utility_model->run_query($shared_links_ids_sql)->result_array();
        foreach ($shared_links_ids_results as $r) {
            $shared_links_ids[] = $r['subject_id'];
        }

        // Add an extra element for safety.
        $shared_links_ids[] = 0;
        $shared_links_ids = implode(',', $shared_links_ids);

        ///

        /* *** */

        // Query to get all posts, photos, videos, and links by this user's friends.
        // Get shared items.

        // If the last user to share an item is the current viewer of the page,
        // then we pick the second last user who shared the same item.
        $latest_share_date_sql = sprintf("SELECT MAX(date_shared) FROM shares s2 " .
                                        "WHERE (s1.subject_id = s2.subject_id AND " .
                                        "s1.subject_type = s2.subject_type AND " .
                                        "s2.sharer_id != %d)",
                                        $_SESSION['user_id']);

        $latest_shared_posts_user_ids_sql = sprintf("SELECT sharer_id FROM shares s1 " .
                                                    "WHERE (sharer_id IN(%s) AND subject_type = 'post' AND " .
                                                    "date_shared = (%s))",
                                                    $friends_ids, $latest_share_date_sql);
        $latest_shared_posts_user_ids_results = $this->utility_model->run_query($latest_shared_posts_user_ids_sql)->result_array();
        foreach ($latest_shared_posts_user_ids_results as $r) {
            $latest_shared_posts_user_ids[] = $r['sharer_id'];
        }

        // Add an extra element for safety.
        $latest_shared_posts_user_ids[] = 0;
        $latest_shared_posts_user_ids = implode(',', $latest_shared_posts_user_ids);

        $latest_shared_photos_user_ids_sql = sprintf("SELECT sharer_id FROM shares s1 " .
                                                    "WHERE (sharer_id IN(%s) AND subject_type = 'photo' AND " .
                                                    "date_shared = (%s))",
                                                    $friends_ids, $latest_share_date_sql);
        $latest_shared_photos_user_ids_results = $this->utility_model->run_query($latest_shared_photos_user_ids_sql)->result_array();
        foreach ($latest_shared_photos_user_ids_results as $r) {
            $latest_shared_photos_user_ids[] = $r['sharer_id'];
        }

        // Add an extra element for safety.
        $latest_shared_photos_user_ids[] = 0;
        $latest_shared_photos_user_ids = implode(',', $latest_shared_photos_user_ids);

        $latest_shared_videos_user_ids_sql = sprintf("SELECT sharer_id FROM shares s1 " .
                                                    "WHERE (sharer_id IN(%s) AND subject_type = 'video' AND " .
                                                    "date_shared = (%s))",
                                                    $friends_ids, $latest_share_date_sql);
        $latest_shared_videos_user_ids_results = $this->utility_model->run_query($latest_shared_videos_user_ids_sql)->result_array();
        foreach ($latest_shared_videos_user_ids_results as $r) {
            $latest_shared_videos_user_ids[] = $r['sharer_id'];
        }

        // Add an extra element for safety.
        $latest_shared_videos_user_ids[] = 0;
        $latest_shared_videos_user_ids = implode(',', $latest_shared_videos_user_ids);

        $latest_shared_links_user_ids_sql = sprintf("SELECT sharer_id FROM shares s1 " .
                                                    "WHERE (sharer_id IN(%s) AND subject_type = 'link' AND " .
                                                    "date_shared = (%s))",
                                                    $friends_ids, $latest_share_date_sql);
        $latest_shared_links_user_ids_results = $this->utility_model->run_query($latest_shared_links_user_ids_sql)->result_array();
        foreach ($latest_shared_links_user_ids_results as $r) {
            $latest_shared_links_user_ids[] = $r['sharer_id'];
        }

        // Add an extra element for safety.
        $latest_shared_links_user_ids[] = 0;
        $latest_shared_links_user_ids = implode(',', $latest_shared_links_user_ids);

        // We also don't show items published by this user that were shared by his/her friends.
        $news_feed_items_sql = sprintf("SELECT * FROM activities " .
                                        "WHERE ((actor_id IN(%s) AND activity = 'post' AND source_id NOT IN(%s)) OR " .
                                                "(actor_id IN(%s) AND activity IN('photo', 'profile_pic_change') AND " .
                                                    "source_id NOT IN(%s)) OR " .
                                                "(actor_id IN(%s) AND activity = 'video' AND source_id NOT IN(%s)) OR " .
                                                "(actor_id IN(%s) AND activity = 'link' AND source_id NOT IN(%s)) OR " .
                                                "(actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND " .
                                                    "source_type = 'photo' AND subject_id != %d) OR " .
                                                "(actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND " .
                                                    "source_type = 'post' AND subject_id != %d)) " .
                                        "ORDER BY date_entered DESC LIMIT %d, %d",
                                        $friends_ids, $shared_posts_ids,
                                        $friends_ids, $shared_photos_ids,
                                        $friends_ids, $shared_videos_ids,
                                        $friends_ids, $shared_links_ids,
                                        $friends_ids, $latest_shared_photos_user_ids, $_SESSION['user_id'],
                                        $friends_ids, $latest_shared_posts_user_ids, $_SESSION['user_id'],
                                        $offset, $limit);
        $news_feed_items = $this->utility_model->run_query($news_feed_items_sql)->result_array();


        foreach ($news_feed_items as &$r) {
            switch ($r['source_type']) {
                case 'post':
                    $r['post'] = $this->post_model->get_post($r['source_id']);

                    // Get only 540 characters from post if possible.
                    $post_url = base_url("user/post/{$r['post']['post_id']}");
                    $r['post']['post'] = character_limiter($r['post']['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");

                    // Was it shared from another user?
                    $r['post']['shared'] = FALSE;
                    if (in_array($r['source_id'], explode(',', $shared_posts_ids))) {
                        $r['post']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('post', $r);
                    }
                    break;
                case 'photo':
                    $r['photo'] = $this->photo_model->get_photo($r['source_id']);

                    // Was it shared from another user?
                    $r['photo']['shared'] = FALSE;
                    if (in_array($r['source_id'], explode(',', $shared_photos_ids))) {
                        $r['photo']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('photo', $r);
                    }
                    break;
                case 'video':
                    $r['video'] = $this->video_model->get_video($r['source_id']);

                    // Was it shared from another user?
                    $r['video']['shared'] = FALSE;
                    if (in_array($r['source_id'], explode(',', $shared_videos_ids))) {
                        $r['video']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('video', $r);
                    }
                    break;
                case 'link':
                    $r['link'] = $this->link_model->get_link($r['source_id']);

                    // Was it shared from another user?
                    $r['link']['shared'] = FALSE;
                    if (in_array($r['source_id'], explode(',', $shared_links_ids))) {
                        $r['link']['shared'] = TRUE;
                        $r = $this->utility_model->update_shared_item_data('link', $r);
                    }
                    break;
                default:
                    break;
                    # do nothing.
            }
        }
        unset($r);

        return $news_feed_items;
    }
}

?>
