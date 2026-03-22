<?php
namespace App\Models;

class Announcement extends BaseModel
{
    public function getAnnouncements()
    {
        $nameSql = $this->getEmployeeNameSql('e', 'author_name');
        $sql = "SELECT a.*, $nameSql FROM eer_announcements a JOIN employees e ON a.created_by = e.eer_employee_id ORDER BY a.created_at DESC";
        return $this->execute($sql)->fetchAll();
    }

    public function postAnnouncement($title, $content, $created_by)
    {
        $sql = 'INSERT INTO eer_announcements (title, content, created_by, created_at) VALUES (:title, :content, :created_by, NOW())';
        $params = [
            'title' => $title,
            'content' => $content,
            'created_by' => $created_by,
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
