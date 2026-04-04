<?php
namespace App\Models;

class Notification extends BaseModel
{
    public function getAll()
    {
        $sql = 'SELECT * FROM eer_notifications';
        return $this->execute($sql)->fetchAll();
    }

    public function markAsRead($notification_id)
    {
        $sql = 'UPDATE eer_notifications SET is_read = 1 WHERE id = :id';
        $this->execute($sql, ['id' => $notification_id]);
    }
}