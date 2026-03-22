<?php
namespace App\Models;

class SocialPost extends BaseModel
{
    public function getPosts()
    {
        $nameSql = $this->getEmployeeNameSql('e', 'employee_name');
        return $this->execute("SELECT p.*, $nameSql FROM eer_social_posts p JOIN employees e ON p.employee_id = e.eer_employee_id ORDER BY p.created_at DESC")->fetchAll();
    }

    public function createPost($employee_id, $content)
    {
        $sql = 'INSERT INTO eer_social_posts (employee_id, content, created_at) VALUES (:employee_id, :content, NOW())';
        $this->execute($sql, ['employee_id' => $employee_id, 'content' => $content]);
        return $this->db->lastInsertId();
    }
}
