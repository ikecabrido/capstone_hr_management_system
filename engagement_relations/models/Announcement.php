<?php
namespace App\Models;

class Announcement extends BaseModel
{
    public function postAnnouncement($title, $content, $created_by_user_id, $category = 'general', $priority = 'normal', $targetAudience = 'all')
    {
        return $this->createAnnouncement([
            'title' => $title,
            'content' => $content,
            'created_by_user_id' => $created_by_user_id,
            'type' => 'announcement',
            'category' => $category,
            'priority' => $priority,
            'target_audience' => $targetAudience
        ]);
    }

    public function postDepartmentUpdate($title, $content, $department, $priority, $created_by_user_id)
    {
        return $this->createAnnouncement([
            'title' => $title,
            'content' => $content,
            'created_by_user_id' => $created_by_user_id,
            'type' => 'department_update',
            'department' => $department,
            'priority' => $priority
        ]);
    }
    protected $table = 'eer_announcements';

    public function find($id)
    {
        $sql = "SELECT * FROM $this->table WHERE eer_announcements_id = :id";
        return $this->execute($sql, ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAnnouncements($type = 'announcement')
    {
        $sql = "SELECT ea.*, 
                COALESCE(e.full_name, u.full_name, u.username, ea.created_by_user_id) AS created_by_name
                FROM $this->table ea
                LEFT JOIN employees e ON ea.created_by_user_id = e.user_id 
                LEFT JOIN users u ON ea.created_by_user_id = u.id
                WHERE ea.type = :type
                ORDER BY ea.created_at DESC";
        return $this->execute($sql, ['type' => $type])->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getDepartmentUpdates()
    {
        return $this->getAnnouncements('department_update');
    }

    public function getRecognitionAnnouncements()
    {
        return $this->getAnnouncements('recognition');
    }

    public function postRecognitionAnnouncement($title, $content, $created_by_user_id)
    {
        return $this->createAnnouncement([
            'title' => $title,
            'content' => $content,
            'created_by_user_id' => $created_by_user_id,
            'type' => 'recognition'
        ]);
    }

    public function createAnnouncement($data)
    {
        $type = $data['type'] ?? 'announcement';
        $sql = "INSERT INTO $this->table (title, content, created_by_user_id, type, category, priority, target_audience, department) VALUES (:title, :content, :created_by_user_id, :type, :category, :priority, :target_audience, :department)";
        $params = [
            'title' => $data['title'],
            'content' => $data['content'],
            'created_by_user_id' => $data['created_by_user_id'],
            'type' => $type,
            'category' => $data['category'] ?? 'general',
            'priority' => $data['priority'] ?? 'normal',
            'target_audience' => $data['target_audience'] ?? 'all',
            'department' => $data['department'] ?? null
        ];
        return $this->execute($sql, $params);
    }

    public function shareFile($userId, $fileName, $filePath, $fileSize, $fileType, $description = null, $content = null)
    {
        $sql = "INSERT INTO eer_social_posts (user_id, author_type, item_type, file_name, file_path, file_size, file_type, description, content, created_at) 
                VALUES (:user_id, 'user', 'file', :file_name, :file_path, :file_size, :file_type, :description, :content, NOW())";
        $this->execute($sql, [
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'description' => $description,
            'content' => $content
        ]);
        return $this->db->lastInsertId();
    }

    public function getSharedFiles()
    {
        $sql = "SELECT sp.*, COALESCE(e.full_name, u.full_name, u.username, sp.user_id) AS uploader_name
                FROM eer_social_posts sp
                LEFT JOIN users u ON sp.user_id = u.id
                LEFT JOIN employees e ON u.employee_id = e.employee_id
                WHERE sp.item_type = 'file'
                ORDER BY sp.created_at DESC";
        return $this->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSharedFileById($id)
    {
        $sql = "SELECT sp.*, COALESCE(e.full_name, u.full_name, u.username, sp.user_id) AS uploader_name
                FROM eer_social_posts sp
                LEFT JOIN users u ON sp.user_id = u.id
                LEFT JOIN employees e ON u.employee_id = e.employee_id
                WHERE sp.eer_social_post_id = :id AND sp.item_type = 'file'";
        return $this->execute($sql, ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
    }

    public function deleteSharedFile($id)
    {
        $sql = "DELETE FROM eer_social_posts WHERE eer_social_post_id = :id AND item_type = 'file'";
        return $this->execute($sql, ['id' => $id]);
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
