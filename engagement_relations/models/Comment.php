<?php
namespace App\Models;

class Comment extends BaseModel
{
    public function getComments($post_id)
    {
        $nameSql = $this->getEmployeeNameSql('e', 'employee_name');
        return $this->execute("SELECT c.*, $nameSql FROM eer_comments c JOIN employees e ON c.employee_id = e.eer_employee_id WHERE c.post_id = :post_id ORDER BY c.created_at ASC", ['post_id' => $post_id])->fetchAll();
    }

    public function addComment($post_id, $employee_id, $comment)
    {
        $sql = 'INSERT INTO eer_comments (post_id, employee_id, comment, created_at) VALUES (:post_id, :employee_id, :comment, NOW())';
        $params = ['post_id' => $post_id, 'employee_id' => $employee_id, 'comment' => $comment];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
