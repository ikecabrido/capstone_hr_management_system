<?php
namespace App\Models;

class Recognition extends BaseModel
{
    public function getRecognitions()
    {
        $sql = 'SELECT r.*, s.name AS sender_name, t.name AS receiver_name FROM recognitions r JOIN employees s ON r.sender_id = s.id JOIN employees t ON r.receiver_id = t.id ORDER BY r.created_at DESC';
        return $this->execute($sql)->fetchAll();
    }

    public function sendRecognition($sender_id, $receiver_id, $message, $points)
    {
        $sql = 'INSERT INTO recognitions (sender_id, receiver_id, message, points, created_at) VALUES (:sender_id, :receiver_id, :message, :points, NOW())';
        $params = [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'points' => $points,
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}
