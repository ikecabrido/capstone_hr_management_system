<?php
namespace App\Models;

class Announcement extends BaseModel
{
    public function postAnnouncement($title, $content, $created_by)
    {
        return $this->createAnnouncement([
            'title' => $title,
            'content' => $content,
            'created_by' => $created_by
        ]);
    }
    protected $table = 'eer_announcements';

    public function getAnnouncements()
    {
        $sql = "SELECT ea.*, 
                COALESCE(e.full_name, u.username, u.full_name, ea.created_by) AS created_by_name
                FROM $this->table ea
                LEFT JOIN employees e ON ea.created_by = e.employee_id 
                LEFT JOIN users u ON CAST(ea.created_by AS UNSIGNED) = u.user_id
                ORDER BY ea.created_at DESC";
        return $this->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createAnnouncement($data)
    {
        $sql = "INSERT INTO $this->table (title, content, created_by) VALUES (:title, :content, :created_by)";
        return $this->execute($sql, $data);
    }

    public function deleteAnnouncement($id)
    {
        $sql = "DELETE FROM $this->table WHERE eer_announcements_id = :id";
        return $this->execute($sql, ['id' => $id]);
    }

    public function getPolicyUpdates()
    {
        $sql = "SELECT * FROM $this->table WHERE category = 'Policy Update'";
        return $this->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
