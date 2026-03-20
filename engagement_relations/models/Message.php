<?php
namespace App\Models;

class Message extends BaseModel
{
    public function create($data)
    {
        $sql = 'INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (:sender_id, :receiver_id, :message, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function threads($employee_id)
    {
        return $this->execute('SELECT m.*, s.name AS sender_name, r.name AS receiver_name FROM messages m JOIN employees s ON m.sender_id=s.id JOIN employees r ON m.receiver_id=r.id WHERE m.sender_id = :id OR m.receiver_id = :id ORDER BY m.timestamp DESC', ['id' => $employee_id])->fetchAll();
    }
}
