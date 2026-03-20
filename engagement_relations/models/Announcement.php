<?php
namespace App\Models;

class Announcement extends BaseModel
{
    public function getAnnouncements()
    {
        $sql = 'SELECT a.*, e.name AS author_name FROM announcements a JOIN employees e ON a.created_by = e.id ORDER BY a.created_at DESC';
        return $this->execute($sql)->fetchAll();
    }

    public function postAnnouncement($title, $content, $created_by)
    {
        $sql = 'INSERT INTO announcements (title, content, created_by, created_at) VALUES (:title, :content, :created_by, NOW())';
        $params = [
            'title' => $title,
            'content' => $content,
            'created_by' => $created_by,
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
