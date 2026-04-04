<?php
namespace App\Models;

class Comment extends BaseModel
{
    protected $table = 'eer_comments';

    private function getAuthorTypeColumn()
    {
        $column = $this->execute("SHOW COLUMNS FROM eer_comments LIKE 'author_type'")->fetch();
        return $column ? 'author_type' : 'user_type';
    }

    public function getAllComments()
    {
        $sql = "SELECT * FROM $this->table";
        return $this->execute($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createComment($data)
    {
        $sql = "INSERT INTO $this->table (post_id, employee_id, comment) VALUES (:post_id, :employee_id, :comment)";
        return $this->execute($sql, $data);
    }

    public function deleteComment($id)
    {
        $sql = "DELETE FROM $this->table WHERE eer_comment_id = :id";
        return $this->execute($sql, ['id' => $id]);
    }

    public function getComments($post_id)
    {
        $typeCol = $this->getAuthorTypeColumn();
        $nameSql = "COALESCE(e.full_name, u.full_name, u.username, c.employee_id, 'hr_engagement') AS author_name";

        $sql = "SELECT c.*, $nameSql FROM eer_comments c
                LEFT JOIN employees e ON c.employee_id = e.employee_id AND c.$typeCol = 'employee'
                LEFT JOIN users u ON c.$typeCol = 'user' AND (CAST(c.employee_id AS UNSIGNED) = u.user_id OR c.employee_id = u.username)
                WHERE c.post_id = :post_id ORDER BY c.created_at ASC";

        return $this->execute($sql, ['post_id' => $post_id])->fetchAll();
    }

    public function addComment($post_id, $author_id, $comment, $author_type = 'employee')
    {
        $typeCol = $this->getAuthorTypeColumn();
        $sql = "INSERT INTO eer_comments (post_id, employee_id, comment, created_at, $typeCol) VALUES (:post_id, :employee_id, :comment, NOW(), :author_type)";
        $params = ['post_id' => $post_id, 'employee_id' => $author_id, 'comment' => $comment, 'author_type' => $author_type];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
