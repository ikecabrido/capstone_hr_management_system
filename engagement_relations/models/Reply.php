<?php
namespace App\Models;

class Reply extends BaseModel
{
    protected $table = 'eer_replies';

    private function getAuthorTypeColumn()
    {
        $column = $this->execute("SHOW COLUMNS FROM eer_replies LIKE 'author_type'")->fetch();
        return $column ? 'author_type' : 'user_type';
    }

    public function getAllReplies()
    {
        $sql = "SELECT * FROM $this->table";
        return $this->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRepliesByComment($commentId)
    {
        $typeCol = $this->getAuthorTypeColumn();
        $nameSql = "COALESCE(e.full_name, u.full_name, u.username, r.employee_id, r.user_type, 'hr_engagement') AS author_name";

        $sql = "SELECT r.*, $nameSql FROM eer_replies r
                LEFT JOIN employees e ON r.employee_id = e.employee_id AND r.$typeCol = 'employee'
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.comment_id = :comment_id
                ORDER BY r.created_at ASC";

        return $this->execute($sql, ['comment_id' => $commentId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRepliesByPost($postId)
    {
        $typeCol = $this->getAuthorTypeColumn();
        $nameSql = "COALESCE(e.full_name, u.full_name, u.username, r.employee_id, r.user_type, 'hr_engagement') AS author_name";

        $sql = "SELECT r.*, $nameSql FROM eer_replies r
                LEFT JOIN employees e ON r.employee_id = e.employee_id AND r.$typeCol = 'employee'
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.post_id = :post_id
                ORDER BY r.created_at ASC";

        return $this->execute($sql, ['post_id' => $postId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addReply($commentId, $postId, $authorId, $content, $authorType = 'employee', $parentReplyId = null, $mentionedUserId = null)
    {
        $params = [
            'comment_id' => $commentId,
            'post_id' => $postId,
            'parent_reply_id' => $parentReplyId,
            'employee_id' => null,
            'user_id' => null,
            'user_type' => $authorType,
            'content' => $content,
            'mentioned_user_id' => $mentionedUserId,
        ];

        if ($authorType === 'employee') {
            $params['employee_id'] = $authorId;
        } else {
            $params['user_id'] = $authorId;
        }

        $sql = "INSERT INTO eer_replies (comment_id, post_id, parent_reply_id, employee_id, user_id, user_type, content, mentioned_user_id, created_at)
                VALUES (:comment_id, :post_id, :parent_reply_id, :employee_id, :user_id, :user_type, :content, :mentioned_user_id, NOW())";

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function deleteReply($replyId)
    {
        $sql = "DELETE FROM eer_replies WHERE eer_reply_id = :id";
        return $this->execute($sql, ['id' => $replyId]);
    }
}
