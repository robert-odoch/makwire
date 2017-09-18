<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'autoload.php';

class News_feed_model extends CI_Model
{
    private $unfollowed_user_ids;

    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'user_model',
            'post_model', 'photo_model',
            'video_model', 'link_model'
        ]);
    }

    private function get_unfollowed_user_ids($user_id)
    {
        $sql = sprintf('SELECT user_id FROM user_unfollow WHERE follower_id = %d',
                        $user_id);
        $query = $this->db->query($sql);
        $results = $query->result_array();

        $user_ids = [];
        foreach ($results as $r) {
            $user_ids[] = $r['user_id'];
        }

        return $user_ids;
    }

    private function get_latest_shared_items_user_ids($item, $friends_ids, $latest_share_date_sql)
    {
        $friends_ids[] = 0;  // Add extra element for query-safety.
        $friends_ids_str = implode(',', $friends_ids);

        $user_ids_sql = sprintf('SELECT DISTINCT sharer_id FROM shares s1
                                WHERE (sharer_id IN(%s) AND subject_type = \'%s\' AND date_shared = (%s))',
                                $friends_ids_str, $item, $latest_share_date_sql);
        $user_ids_results = $this->utility_model->run_query($user_ids_sql)->result_array();

        $user_ids = [];
        foreach ($user_ids_results as $r) {
            $user_ids[] = $r['sharer_id'];
        }

        return $user_ids;
    }

    /**
     * Gets number of posts, photos, videos, and links on a user's news feed.
     *
     * @return number of posts, photos, videos, and links on this user's news feed.
     */
    public function get_num_news_feed_items($user_id)
    {
        $friends_ids = $this->user_model->get_friends_ids($user_id);
        $this->unfollowed_user_ids = $this->get_unfollowed_user_ids($user_id);

        // Remove IDs of friends who have been unfollowed.
        $friends_ids = array_filter($friends_ids, function($friend_id) {
            return !in_array($friend_id, $this->unfollowed_user_ids);
        });

        // Add extra element for query-safety.
        $friends_ids[] = 0;
        $friends_ids_str = implode(',', $friends_ids);

        /* Get IDs of shared posts, photos, videos, and links. */
        /// IDS of shared posts.
        $shared_posts_ids = $this->utility_model->get_shared_items_ids('post', $friends_ids);
        $shared_posts_ids[] = 0;  // Add an extra element for safety.
        $shared_posts_ids_str = implode(',', $shared_posts_ids);

        /// IDs of shared photos.
        $shared_photos_ids = $this->utility_model->get_shared_items_ids('photo', $friends_ids);
        $shared_photos_ids[] = 0;  // Add an extra element for safety.
        $shared_photos_ids_str = implode(',', $shared_photos_ids);

        /// IDs of shared videos.
        $shared_videos_ids = $this->utility_model->get_shared_items_ids('video', $friends_ids);
        $shared_videos_ids[] = 0;  // Add an extra element for safety.
        $shared_videos_ids_str = implode(',', $shared_videos_ids);

        /// IDs of shared links.
        $shared_links_ids = $this->utility_model->get_shared_items_ids('link', $friends_ids);
        $shared_links_ids[] = 0;  // Add an extra element for safety.
        $shared_links_ids_str = implode(',', $shared_links_ids);

        /* *** */
        $num_posts_sql = sprintf('SELECT COUNT(source_id) FROM activities
                                 WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND activity = \'post\')',
                                 $friends_ids_str, $shared_posts_ids_str);
        $num_posts = $this->utility_model->run_query($num_posts_sql)->row_array()['COUNT(source_id)'];

        $num_photos_sql = sprintf('SELECT COUNT(source_id) FROM activities
                                    WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND
                                            activity IN(\'photo\',\'profile_pic_change\'))',
                                    $friends_ids_str, $shared_photos_ids_str);
        $num_photos = $this->utility_model->run_query($num_photos_sql)->row_array()['COUNT(source_id)'];

        $num_videos_sql = sprintf('SELECT COUNT(source_id) FROM activities
                                    WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND activity = \'video\')',
                                    $friends_ids_str, $shared_videos_ids_str);
        $num_videos = $this->utility_model->run_query($num_videos_sql)->row_array()['COUNT(source_id)'];

        $num_links_sql = sprintf('SELECT COUNT(source_id) FROM activities
                                 WHERE (actor_id IN(%s) AND source_id NOT IN(%s) AND activity = \'link\')',
                                 $friends_ids_str, $shared_links_ids_str);
        $num_links = $this->utility_model->run_query($num_links_sql)->row_array()['COUNT(source_id)'];

        $num_shared_items_sql = sprintf('SELECT COUNT(source_id) FROM activities
                                        WHERE (actor_id IN(%s) AND activity = \'share\' AND
                                            source_type IN(\'post\',\'photo\',\'video\',\'link\') AND subject_id != %d)',
                                        $friends_ids_str, $user_id);
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
    public function get_news_feed_items($user_id, $offset, $limit)
    {
        $friends_ids = $this->user_model->get_friends_ids($user_id);
        $this->unfollowed_user_ids = $this->get_unfollowed_user_ids($user_id);

        // Remove IDs of friends who have been unfollowed.
        $friends_ids = array_filter($friends_ids, function($friend_id) {
            return !in_array($friend_id, $this->unfollowed_user_ids);
        });

        /* Get IDs of shared items. */
        /// IDs are got seperately b'se many items may share the same ID
        /// as they are stored in different tables.

        /// IDS of shared posts.
        $shared_posts_ids = $this->utility_model->get_shared_items_ids('post', $friends_ids);
        $shared_posts_ids[] = 0;  // Add an extra element for safety.
        $shared_posts_ids_str = implode(',', $shared_posts_ids);

        /// IDs of shared photos.
        $shared_photos_ids = $this->utility_model->get_shared_items_ids('photo', $friends_ids);
        $shared_photos_ids[] = 0;  // Add an extra element for safety.
        $shared_photos_ids_str = implode(',', $shared_photos_ids);

        /// IDs of shared videos.
        $shared_videos_ids = $this->utility_model->get_shared_items_ids('video', $friends_ids);
        $shared_videos_ids[] = 0;  // Add an extra element for safety.
        $shared_videos_ids_str = implode(',', $shared_videos_ids);

        /// IDs of shared links.
        $shared_links_ids = $this->utility_model->get_shared_items_ids('link', $friends_ids);
        $shared_links_ids[] = 0;  // Add an extra element for safety.
        $shared_links_ids_str = implode(',', $shared_links_ids);

        /* *** */
        /// Get shared items.

        // If the last user to share an item is the current viewer of the page,
        // then we pick the second last user who shared the same item.
        $latest_share_date_sql = sprintf("SELECT MAX(date_shared) FROM shares s2
                                            WHERE (s1.subject_id = s2.subject_id AND
                                            s1.subject_type = s2.subject_type AND
                                            s2.sharer_id != %d)",
                                            $user_id);

        /// Latest shared posts user IDs.
        $latest_shared_posts_user_ids = $this->get_latest_shared_items_user_ids('post', $friends_ids, $latest_share_date_sql);
        $latest_shared_posts_user_ids[] = 0;  // Add an extra element for safety.
        $latest_shared_posts_user_ids_str = implode(',', $latest_shared_posts_user_ids);

        /// Latest shared photos user IDs.
        $latest_shared_photos_user_ids = $this->get_latest_shared_items_user_ids('photo', $friends_ids, $latest_share_date_sql);
        $latest_shared_photos_user_ids[] = 0;  // Add an extra element for safety.
        $latest_shared_photos_user_ids_str = implode(',', $latest_shared_photos_user_ids);

        /// Latest shared videos user IDs.
        $latest_shared_videos_user_ids = $this->get_latest_shared_items_user_ids('video', $friends_ids, $latest_share_date_sql);
        $latest_shared_videos_user_ids[] = 0;  // Add an extra element for safety.
        $latest_shared_videos_user_ids_str = implode(',', $latest_shared_videos_user_ids);

        /// Latest shared links user IDs.
        $latest_shared_links_user_ids = $this->get_latest_shared_items_user_ids('link', $friends_ids, $latest_share_date_sql);
        $latest_shared_links_user_ids[] = 0;  // Add an extra element for safety.
        $latest_shared_links_user_ids_str = implode(',', $latest_shared_links_user_ids);

        /// Note: We also don't show items published by this user that were shared by his/her friends.
        //
        $friends_ids[] = 0;  // Add extra element for query-safety.
        $friends_ids_str = implode(',', $friends_ids);

        $this->unfollowed_user_ids[] = 0;
        $unfollowed_user_ids_str = implode(',', $this->unfollowed_user_ids);

        /// Get combined news feed items.
        $news_feed_items_sql = sprintf("SELECT * FROM activities
                                        WHERE (((actor_id IN(%s) AND activity = 'post' AND source_id NOT IN(%s)) OR
                                                (actor_id IN(%s) AND activity IN('photo', 'profile_pic_change') AND
                                                    source_id NOT IN(%s)) OR
                                                (actor_id IN(%s) AND activity = 'video' AND source_id NOT IN(%s)) OR
                                                (actor_id IN(%s) AND activity = 'link' AND source_id NOT IN(%s)) OR
                                                (actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND
                                                    source_type = 'post' AND subject_id != %d) OR
                                                (actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND
                                                    source_type = 'photo' AND subject_id != %d) OR
                                                (actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND
                                                    source_type = 'video' AND subject_id != %d) OR
                                                (actor_id IN(%s) AND actor_id IN(%s) AND activity = 'share' AND
                                                    source_type = 'link' AND subject_id != %d)) AND
                                                    subject_id NOT IN(%s))
                                        ORDER BY date_entered DESC LIMIT %d, %d",
                                        $friends_ids_str, $shared_posts_ids_str,
                                        $friends_ids_str, $shared_photos_ids_str,
                                        $friends_ids_str, $shared_videos_ids_str,
                                        $friends_ids_str, $shared_links_ids_str,
                                        $friends_ids_str, $latest_shared_posts_user_ids_str, $user_id,
                                        $friends_ids_str, $latest_shared_photos_user_ids_str, $user_id,
                                        $friends_ids_str, $latest_shared_videos_user_ids_str, $user_id,
                                        $friends_ids_str, $latest_shared_links_user_ids_str, $user_id,
                                        $unfollowed_user_ids_str, $offset, $limit);
        $news_feed_items = $this->utility_model->run_query($news_feed_items_sql)->result_array();

        foreach ($news_feed_items as &$r) {
            switch ($r['source_type']) {
            case 'post':
                $r['post'] = $this->post_model->get_post($r['source_id'], $user_id);

                // Get only 540 characters from post if possible.
                $post_url = base_url("user/post/{$r['post']['post_id']}");
                $r['post']['post'] = character_limiter($r['post']['post'], 540, "&#8230;<a href='{$post_url}'>view more</a>");

                // Was it shared from another user?
                $r['post']['shared'] = FALSE;
                if (in_array($r['source_id'], $shared_posts_ids)) {
                    $r['post']['shared'] = TRUE;
                    $r = $this->utility_model->update_shared_item_data('post', $r);
                }
                break;
            case 'photo':
                $r['photo'] = $this->photo_model->get_photo($r['source_id'], $user_id);

                // Was it shared from another user?
                $r['photo']['shared'] = FALSE;
                if (in_array($r['source_id'], $shared_photos_ids)) {
                    $r['photo']['shared'] = TRUE;
                    $r = $this->utility_model->update_shared_item_data('photo', $r);
                }
                break;
            case 'video':
                $r['video'] = $this->video_model->get_video($r['source_id'], $user_id);

                // Was it shared from another user?
                $r['video']['shared'] = FALSE;
                if (in_array($r['source_id'], $shared_videos_ids)) {
                    $r['video']['shared'] = TRUE;
                    $r = $this->utility_model->update_shared_item_data('video', $r);
                }
                break;
            case 'link':
                $r['link'] = $this->link_model->get_link($r['source_id'], $user_id);

                // Was it shared from another user?
                $r['link']['shared'] = FALSE;
                if (in_array($r['source_id'], $shared_links_ids)) {
                    $r['link']['shared'] = TRUE;
                    $r = $this->utility_model->update_shared_item_data('link', $r);
                }
                break;
            default:
                # do nothing...
                break;
            }
        }
        unset($r);

        return $news_feed_items;
    }
}

?>
