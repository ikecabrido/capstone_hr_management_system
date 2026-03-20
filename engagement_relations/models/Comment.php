<?php
namespace App\Models;

class Comment extends BaseModel
{
    public function getComments($post_id)
    {
        return $this->execute('SELECT c.*, e.name as employee_name FROM comments c JOIN employees e ON c.employee_id = e.id WHERE c.post_id = :post_id ORDER BY c.created_at ASC', ['post_id' => $post_id])->fetchAll();
    }

    public function addComment($post_id, $employee_id, $comment)
    {
        $sql = 'INSERT INTO comments (post_id, employee_id, comment, created_at) VALUES (:post_id, :employee_id, :comment, NOW())';
        $params = ['post_id' => $post_id, 'employee_id' => $employee_id, 'comment' => $comment];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
