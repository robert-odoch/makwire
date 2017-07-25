<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contians functions that are used by two or more models.
 */
class Utility_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    /**
     * Handle database errors.
     *
     * @param $error the resulting error from running a query.
     */
    public function handle_error($error)
    {
        print($error);
        exit(1);
    }

    /**
     * Runs a query againsts the database.
     *
     * @param $sql the SQL query to be run.
     * @return query object.
     */
    public function run_query($sql)
    {
        $query = $this->db->query($sql);
        if (!$query) {
            $this->handle_error($this->db->error());
        }

        return $query;
    }

    public function send_email($email, $subject, $message)
    {
        $this->load->library('email');

        // Get full html:
        $body = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' .
            strtolower(config_item('charset')) . '" />
            <title>' . html_escape($subject) . '</title>
            <style type="text/css">
                body {
                    background-color: green;
                    font-family: Arial, Verdana, Helvetica, sans-serif;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
        ' . $message . '
        </body>
        </html>';

        $result = $this->email
                ->from('robertelvisodoch@gmail.com')
                ->to($email)
                ->subject($subject)
                ->message($body)
                ->send();

        return $result;
    }

    /**
     * For posts, photos, videos, and links.
     *
     * Adds the details of the sharer and also updates the timespan to match
     * when the item was shared.
     */
    public function update_shared_item_data($item, $data)
    {
        $data[$item]['sharer_id'] = $data['actor_id'];
        $data[$item]['sharer'] = $this->user_model->get_profile_name($data['actor_id']);

        // Change timespan to match the date it was shared on.
        $data[$item]['timespan'] = timespan(mysql_to_unix($data['date_entered']), now(), 1);

        // Replace author's profile_pic with the one for sharer.
        $data[$item]['profile_pic_path'] = $this->user_model->get_profile_pic_path($data['actor_id']);

        return $data;
    }

    public function get_shared_items_ids($item, $sharers_ids)
    {
        $sharers_ids[] = 0;  // Add extra element for query-safety.
        $sharers_ids_str = implode(',', $sharers_ids);

        $ids_sql = sprintf('SELECT DISTINCT subject_id FROM shares ' .
                            'WHERE (sharer_id IN(%s) AND subject_type = \'%s\')',
                            $sharers_ids_str, $item);
        $ids_results = $this->utility_model->run_query($ids_sql)->result_array();

        $ids = [];
        foreach ($ids_results as $r) {
            $ids[] = $r['subject_id'];
        }

        return $ids;
    }

    public function delete_item_likes(Likeable $item)
    {
        $likes_sql = sprintf('SELECT like_id FROM likes ' .
                                'WHERE source_id = %d AND source_type = \'%s\'',
                                $item->getId(), $item->getType());
        $likes_results = $this->db->query($likes_sql)->result_array();
        foreach ($likes_results as $r) {
            $this->activity_model->deleteLike($item, $r['like_id']);
        }
    }

    public function delete_item_comments(Commentable $item)
    {
        $comments_sql = sprintf('SELECT comment_id FROM comments ' .
                                'WHERE source_id = %d AND source_type = \'%s\'',
                                $item->getId(), $item->getType());
        $comments_results = $this->db->query($comments_sql)->result_array();
        foreach ($comments_results as $r) {
            $this->activity_model->deleteComment($item, $r['comment_id']);
        }
    }

    public function delete_item_shares(Shareable $item)
    {
        $shares_sql = sprintf('SELECT share_id FROM shares ' .
                                'WHERE subject_id = %d AND subject_type = \'%s\'',
                                $item->getId(), $item->getType());
        $shares_results = $this->db->query($shares_sql)->result_array();
        foreach ($shares_results as $r) {
            $this->activity_model->deleteShare($item, $r['share_id']);
        }
    }

    /**
     * @param $user_id The ID of the user whom the item is being deleted from
     * their timeline.
     */
    public function un_share_item(Shareable $item, $user_id)
    {
        $share_sql = sprintf('SELECT share_id FROM shares ' .
                                'WHERE sharer_id = %d AND subject_id = %d AND subject_type = \'%s\'',
                                $user_id, $item->getId(), $item->getType());
        $share_id = $this->db->query($share_sql)->row_array()['share_id'];
        $this->activity_model->deleteShare($item, $share_id);
    }
}
