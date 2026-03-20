<?php
namespace App\Models;

class SocialPost extends BaseModel
{
    public function getPosts()
    {
        return $this->execute('SELECT p.*, e.name as employee_name FROM social_posts p JOIN employees e ON p.employee_id = e.id ORDER BY p.created_at DESC')->fetchAll();
    }

    public function createPost($employee_id, $content)
    {
        $sql = 'INSERT INTO social_posts (employee_id, content, created_at) VALUES (:employee_id, :content, NOW())';
        $this->execute($sql, ['employee_id' => $employee_id, 'content' => $content]);
        return $this->db->lastInsertId();
    }
}
