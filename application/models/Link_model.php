<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimpleLink.php');
require_once('exceptions/IllegalAccessException.php');
require_once('exceptions/NotFoundException.php');

/**
 * Contains functions related to a link.
 */
class Link_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'utility_model', 'activity_model',
            'user_model', 'comment_model'
        ]);
    }

    /**
     * Gets a link plus other link metadata.
     *
     * Throws NotFoundException if the link cannot be found.
     *
     * @param $link_id the ID of the link in the links table.
     * @return the link with the given ID.
     */
    public function get_link($link_id)
    {
        $link_sql = sprintf("SELECT v.*, u.profile_name AS author FROM links v " .
                            "LEFT JOIN users u ON(v.user_id = u.user_id) " .
                            "WHERE link_id = %d",
                            $link_id);
        $link_query = $this->utility_model->run_query($link_sql);
        if ($link_query->num_rows() == 0) {
            throw new NotFoundException();
        }
        $link = $link_query->row_array();
        $link['profile_pic_path'] = $this->user_model->get_profile_pic_path($link['user_id']);
        $link['timespan'] = timespan(mysql_to_unix($link['date_entered']), now(), 1);

        // Add data used by views.
        $link['has_comment'] = strlen($link['comment']) != 0;
        $link['shared'] = FALSE;

        // Check whether the user currently viewing the page is a friend to the
        // owner of the link. This will allow us to only show the like, comment
        // and share buttons to friends of the owner.
        $link['viewer_is_friend_to_owner'] = $this->user_model->are_friends($link['user_id']);

        // Get number of liks, comments and shares.
        $simpleLink = new SimpleLink($link['link_id'], $link['user_id']);
        $link['num_likes'] = $this->activity_model->getNumLikes($simpleLink);
        $link['num_comments'] = $this->activity_model->getNumComments($simpleLink);
        $link['num_shares'] = $this->activity_model->getNumShares($simpleLink);

        return $link;
    }

    /**
     * Allows a user to add a new link to his status.
     *
     * @param $url.
     * @return $link_id the ID of the link in the user_links table.
     */
    public function publish($link_data)
    {
        // Record link data in the links table.
        $link_sql = sprintf('INSERT INTO links (user_id, url, title, description, image, site) ' .
                            'VALUES (%d, %s, %s, %s, %s, %s) ',
                            $_SESSION['user_id'], $this->db->escape($link_data['url']),
                            $this->db->escape($link_data['title']), $this->db->escape($link_data['description']),
                            $this->db->escape($link_data['image']), $this->db->escape($link_data['site']));
        $this->utility_model->run_query($link_sql);
        $link_id = $this->db->insert_id();

        // Dispatch an activity.
        $activity_sql = sprintf('INSERT INTO activities ' .
                                '(actor_id, subject_id, source_id, source_type, activity) ' .
                                'VALUES (%d, %d, %d, "link", "link")',
                                $_SESSION['user_id'], $_SESSION['user_id'], $link_id);
        $this->utility_model->run_query($activity_sql);
    }

    /**
     * Adds a brief description about a link.
     *
     * Assumes that the link alreay exists in the user_links table.
     *
     * @param $description the description entered by the user.
     * @param $link_id the id of the link in the user_links table.
     */
    public function add_comment($comment, $link_id)
    {
        $sql = sprintf('UPDATE links SET comment = %s WHERE (link_id = %d)',
                        $this->db->escape($comment), $link_id);
        $this->utility_model->run_query($sql);
    }

    /**
     * Records a like of a link.
     *
     * Throws NotFoundException exception if link is not on record.
     * It may also throw IllegalAccessException if a user attempts to like
     * a link published by a user who is not his friend.
     *
     * @param $link_id the ID of the link in the user_links table.
     */
    public function like($link_id)
    {
        // Get the id of the owner of this link.
        $owner_sql = sprintf('SELECT user_id FROM links WHERE link_id = %d', $link_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];

        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException(
                "You don't have the proper permissions to like this link."
            );
        }

        $this->activity_model->like(new SimpleLink($link_id, $owner_id));
    }

    /**
     * Shares a link on a user's timeline.
     *
     * Throws NotFoundException if a link is not on record.
     * It may also throw IllegalAccessException if a user attempts to share
     * a link that was published by a user who is not his friend.
     *
     * @param $link_id the ID of the link in the user_links table.
     */
    public function share($link_id)
    {
        $owner_sql = sprintf('SELECT user_id FROM links WHERE link_id = %d', $link_id);
        $owner_query = $this->utility_model->run_query($owner_sql);
        if ($owner_query->num_rows() == 0) {
            throw new NotFoundException();
        }

        $owner_result = $owner_query->row_array();
        $owner_id = $owner_result['user_id'];
        if (!$this->user_model->are_friends($owner_id)) {
            throw new IllegalAccessException(
                "You don't have the proper permissions to share this link."
            );
        }

        $this->activity_model->share(new SimpleLink($link_id, $owner_id));
    }

    /**
     * Records a comment on a link.
     *
     * @param $link_id the ID of the link in the user_links table.
     * @param $comment the comment a user made.
     */
    public function comment($link_id, $comment)
    {
        // Get the ID of the owner of this link.
        $owner_sql = sprintf('SELECT user_id FROM links WHERE link_id = %d', $link_id);
        $owner_result = $this->utility_model->run_query($owner_sql)->row_array();
        $owner_id = $owner_result['user_id'];

        // Record the comment.
        $this->activity_model->comment(
            new SimpleLink($link_id, $owner_id),
            $comment
        );
    }

    /**
     * Get users who liked a link.
     *
     * @param $link an array containing link data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who liked this link.
     */
    public function get_likes(&$link, $offset, $limit)
    {
        return $this->activity_model->getLikes(
            new SimpleLink($link['link_id'], $link['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the users who shared a link.
     *
     * @param $link an array containing link data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the users who shared this link.
     */
    public function get_shares(&$link, $offset, $limit)
    {
        return $this->activity_model->getShares(
            new SimpleLink($link['link_id'], $link['user_id']),
            $offset,
            $limit
        );
    }

    /**
     * Gets the comments made on a link.
     *
     * @param $link an array containing link data.
     * @param offset the position to begin returning records from.
     * @param $limit the maximum number of records to return.
     * @return the comments made on this link.
     */
    public function get_comments(&$link, $offset, $limit)
    {
        return $this->activity_model->getComments(
            new SimpleLink($link['link_id'], $link['user_id']),
            $offset,
            $limit
        );
    }

    public function delete_link(&$link)
    {
        $simpleLink = new SimpleLink($link['link_id'], $link['user_id']);
        $this->utility_model->delete_item($simpleLink);
    }
}
?>
