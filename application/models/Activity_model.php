<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('classes/SimpleReply.php');
require_once('classes/SimpleComment.php');

/**
 * Contains functions for recording and getting activities
 * (e.g., like, comment, shares, replies) performed on an object.
 */
class Activity_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['utility_model', 'user_model']);
    }

    public function like(Likeable $object)
    {
        if ($this->isLiked($object)) {
            return;
        }

        $like_sql = sprintf("INSERT INTO likes (liker_id, source_id, source_type) " .
                            "VALUES (%d, %d, '%s')",
                            $_SESSION['user_id'], $object->getId(), $object->getType());
        $this->utility_model->run_query($like_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, '%s', '%s')",
                                $_SESSION['user_id'], $object->getOwnerId(), $object->getId(),
                                $object->getType(), 'like');
        $this->utility_model->run_query($activity_sql);
    }

    public function share(Shareable $object)
    {
        if ($this->isShared($object)) {
            return;
        }

        // Insert it into the shares table.
        $share_sql = sprintf("INSERT INTO shares (subject_id, sharer_id, subject_type) " .
                                "VALUES (%d, %d, '%s')",
                                $object->getId(), $_SESSION['user_id'], $object->getType());
        $this->utility_model->run_query($share_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, '%s', '%s')",
                                $_SESSION['user_id'], $object->getOwnerId(), $object->getId(),
                                $object->getType(), 'share');
        $this->utility_model->run_query($activity_sql);
    }

    public function reply(Replyable $object, $reply)
    {
        // Record the reply.
        $reply_sql = sprintf("INSERT INTO comments " .
                                "(commenter_id, parent_id, source_id, source_type, comment) " .
                                "VALUES (%d, %d, %d, '%s', %s)",
                                $_SESSION['user_id'], $object->getId(), $object->getId(),
                                $object->getType(), $this->db->escape($reply));
        $this->utility_model->run_query($reply_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, '%s', '%s')",
                                $_SESSION['user_id'], $object->getOwnerId(),
                                $object->getId(), $object->getType(), 'reply');
        $this->utility_model->run_query($activity_sql);
    }

    public function comment(Commentable $object, $comment)
    {
        // Record the comment.
        $comment_sql = sprintf("INSERT INTO comments " .
                                "(commenter_id, parent_id, source_id, source_type, comment) " .
                                "VALUES (%d, %d, %d, '%s', %s)",
                                $_SESSION['user_id'], 0, $object->getId(), $object->getType(),
                                $this->db->escape($comment));
        $this->utility_model->run_query($comment_sql);

        // Dispatch an activity.
        $activity_sql = sprintf("INSERT INTO activities " .
                                "(actor_id, subject_id, source_id, source_type, activity) " .
                                "VALUES (%d, %d, %d, '%s', '%s')",
                                $_SESSION['user_id'], $object->getOwnerId(), $object->getId(),
                                $object->getType(), 'comment');
        $this->utility_model->run_query($activity_sql);
    }

    public function getLikes(Likeable $object, $offset, $limit)
    {
        $likes_sql = sprintf("SELECT l.*, u.profile_name AS liker FROM likes l " .
                            "LEFT JOIN users u ON(liker_id = u.user_id) " .
                            "WHERE (source_type = '%s' AND source_id = %d) " .
                            "LIMIT %d, %d",
                            $object->getType(), $object->getId(), $offset, $limit);
        $likes_query = $this->utility_model->run_query($likes_sql);

        $likes = $likes_query->result_array();
        foreach ($likes as &$like) {
            $like['profile_pic_path'] = $this->user_model->get_profile_pic_path($like['liker_id']);
            $like['timespan'] = timespan(mysql_to_unix($like['date_liked']), now(), 1);
        }
        unset($like);

        return $likes;
    }

    public function getShares(Shareable $object, $offset, $limit)
    {
        $shares_sql = sprintf("SELECT sharer_id, date_shared, u.profile_name AS sharer " .
                            "FROM shares s " .
                            "LEFT JOIN users u ON(sharer_id = u.user_id) " .
                            "WHERE (subject_id = %d AND subject_type = '%s') " .
                            "LIMIT %d, %d",
                            $object->getId(), $object->getType(), $offset, $limit);
        $shares_query = $this->utility_model->run_query($shares_sql);

        $shares = $shares_query->result_array();
        foreach ($shares as &$share) {
            $share['profile_pic_path'] = $this->user_model->get_profile_pic_path($share['sharer_id']);
            $share['timespan'] = timespan(mysql_to_unix($share['date_shared']), now(), 1);
        }
        unset($share);

        return $shares;
    }

    public function getReplies(Replyable $object, $offset, $limit)
    {
        $replies_sql = sprintf("SELECT comment_id FROM comments " .
                                "WHERE (source_type = '%s' AND parent_id = %d) " .
                                "LIMIT %d, %d",
                                $object->getType(), $object->getId(), $offset, $limit);
        $replies_query = $this->utility_model->run_query($replies_sql);
        $results = $replies_query->result_array();

        $replies = array();
        foreach ($results as $r) {
            // Get the detailed reply.
            $reply = $this->reply_model->get_reply($r['comment_id']);
            array_push($replies, $reply);
        }

        return $replies;
    }

    public function getComments(Commentable $object, $offset, $limit)
    {
        $this->load->model('comment_model');

        $comments_sql = sprintf("SELECT comment_id FROM comments " .
                                "WHERE (source_type = '%s' AND source_id = %d AND parent_id = %d) " .
                                "LIMIT %d, %d",
                                $object->getType(), $object->getId(), 0, $offset, $limit);
        $comments_query = $this->utility_model->run_query($comments_sql);
        $results = $comments_query->result_array();

        $comments = array();
        foreach ($results as $r) {
            // Get the detailed comment.
            $comment = $this->comment_model->get_comment($r['comment_id']);
            array_push($comments, $comment);
        }

        return $comments;
    }

    public function getNumLikes(Likeable $object)
    {
        $likes_sql = sprintf("SELECT COUNT(like_id) FROM likes " .
                                "WHERE (source_id = %d AND source_type = '%s')",
                                $object->getId(), $object->getType());
        $likes_query = $this->utility_model->run_query($likes_sql);

        return $likes_query->row_array()['COUNT(like_id)'];
    }

    public function getNumShares(Shareable $object)
    {
        $shares_sql = sprintf("SELECT COUNT(share_id) FROM shares " .
                                "WHERE (subject_id = %d AND subject_type = '%s')",
                                $object->getId(), $object->getType());
        $shares_query = $this->utility_model->run_query($shares_sql);

        return $shares_query->row_array()['COUNT(share_id)'];
    }

    public function getNumReplies(Replyable $object)
    {
        $replies_sql = sprintf("SELECT COUNT(comment_id) FROM comments " .
                                "WHERE (source_type = '%s' AND parent_id = %d)",
                                $object->getType(), $object->getId());
        $replies_query = $this->utility_model->run_query($replies_sql);

        return $replies_query->row_array()['COUNT(comment_id)'];
    }

    public function getNumComments(Commentable $object)
    {
        $comments_sql = sprintf("SELECT COUNT(comment_id) FROM comments " .
                                "WHERE (source_type = '%s' AND source_id = %d AND parent_id = %d)",
                                $object->getType(), $object->getId(), 0);
        $comments_query = $this->utility_model->run_query($comments_sql);

        return $comments_query->row_array()['COUNT(comment_id)'];
    }

    public function isLiked(Likeable $object)
    {
        // Check whether this object belongs to the current user.
        if ($object->getOwnerId() == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already liked the object.
        $like_sql = sprintf("SELECT like_id FROM likes " .
                            "WHERE (source_id = %d AND source_type = '%s' AND liker_id = %d) " .
                            "LIMIT %d",
                            $object->getId(), $object->getType(), $_SESSION['user_id'], 1);
        $like_query = $this->utility_model->run_query($like_sql);

        return ($like_query->num_rows() == 1);
    }

    public function isShared(Shareable $object)
    {
        // Check whether this object belongs to the current user.
        if ($object->getOwnerId() == $_SESSION['user_id']) {
            return TRUE;
        }

        // Check whether this user has already shared the object.
        $share_sql = sprintf("SELECT share_id FROM shares " .
                                "WHERE (subject_id = %d AND sharer_id = %d AND subject_type='%s') " .
                                "LIMIT %d",
                                $object->getId(), $_SESSION['user_id'], $object->getType(), 1);
        $share_query = $this->utility_model->run_query($share_sql);

        return ($share_query->num_rows() == 1);
    }

    public function deleteLike(Likeable $item, $like_id)
    {
        // Delete activity for this like.
        $this->delete_activity($item, 'like');

        // Delete this like.
        $like_sql = sprintf('DELETE FROM likes WHERE like_id = %d LIMIT 1', $like_id);
        $this->db->query($like_sql);
    }

    public function deleteComment(Commentable $item, $comment_id)
    {
        $comment_sql = sprintf('SELECT commenter_id FROM comments WHERE comment_id = %d',
                                $comment_id);
        $comment_result = $this->db->query($comment_sql)->row_array();
        $comment = new SimpleComment($comment_id, $comment_result['commenter_id']);

        // Delete likes for this comment.
        $likes_sql = sprintf('SELECT like_id FROM likes ' .
                                'WHERE source_id = %d AND source_type = \'comment\'',
                                $comment_id);
        $likes_results = $this->db->query($likes_sql)->result_array();
        foreach ($likes_results as $r) {
            $this->deleteLike($comment, $r['like_id']);
        }

        // Delete replies to this comment.
        $replies_sql = sprintf('SELECT comment_id FROM comments ' .
                                'WHERE source_id = %d AND source_type = \'comment\'',
                                $comment_id);
        $replies_results = $this->db->query($replies_sql)->result_array();
        foreach ($replies_results as $r) {
            $this->deleteReply($comment, $r['comment_id']);
        }

        // Delete activity for this comment.
        $this->delete_activity($item, 'comment');

        // Delete this comment.
        $comment_sql = sprintf('DELETE FROM comments WHERE comment_id = %d LIMIT 1',
                                $comment_id);
        $this->db->query($comment_sql);
    }

    public function deleteReply(Replyable $item, $reply_id)
    {
        $reply_sql = sprintf('SELECT commenter_id FROM comments WHERE comment_id = %d',
                                $reply_id);
        $reply_result = $this->db->query($reply_sql)->row_array();
        $reply = new SimpleReply($reply_id, $reply_result['commenter_id']);

        // Delete likes for this reply.
        $likes_sql = sprintf('SELECT like_id FROM likes ' .
                                'WHERE source_id = %d AND source_type = \'reply\'',
                                $reply_id);
        $likes_results = $this->db->query($likes_sql)->result_array();
        foreach ($likes_results as $r) {
            $this->deleteLike($reply, $r['like_id']);
        }

        // Delete activity for this reply.
        $this->delete_activity($item, 'reply');

        // Delete this reply.
        $reply_sql = sprintf('DELETE FROM comments WHERE comment_id = %d LIMIT 1',
                                $reply_id);
        $this->db->query($reply_sql);
    }

    public function deleteShare(Shareable $item, $share_id)
    {
        // Delete activity for this share.
        $this->delete_activity($item, 'share');

        // Delete this share.
        $share_sql = sprintf('DELETE FROM shares WHERE share_id = %d LIMIT 1', $share_id);
        $this->db->query($share_sql);
    }

    public function delete_activity(Object $object, $activity)
    {
        $activity_sql = sprintf('DELETE FROM activities ' .
                                'WHERE source_id = %d AND source_type = \'%s\' AND ' .
                                'activity = \'%s\' LIMIT 1',
                                $object->getId(), $object->getType(), $activity);
        $this->db->query($activity_sql);
    }
}
?>
