<?php
namespace App\Models;

class Message extends BaseModel
{
    public function create($data)
    {
        $sql = 'INSERT INTO eer_messages (sender_id, receiver_id, message, timestamp) 
                VALUES (:sender_id, :receiver_id, :message, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function threads($employee_id)
    {
        $sql = "SELECT m.*, 
                COALESCE(e_s.full_name, u_s.username, u_s.full_name, m.sender_id) AS sender_name,
                COALESCE(e_r.full_name, u_r.username, u_r.full_name, m.receiver_id) AS receiver_name
                FROM eer_messages m 
                LEFT JOIN employees e_s ON m.sender_id = e_s.employee_id 
                LEFT JOIN employees e_r ON m.receiver_id = e_r.employee_id 
                LEFT JOIN users u_s ON CAST(m.sender_id AS UNSIGNED) = u_s.user_id
                LEFT JOIN users u_r ON CAST(m.receiver_id AS UNSIGNED) = u_r.user_id
                ORDER BY m.timestamp DESC";
        return $this->execute($sql)->fetchAll();
    }

    public function getMessageHistory($sender_id, $receiver_id)
    {
        $sql = 'SELECT * FROM eer_messages WHERE 
                (sender_id = :sender_id AND receiver_id = :receiver_id) 
                OR (sender_id = :receiver_id AND receiver_id = :sender_id) 
                ORDER BY timestamp ASC';
        return $this->execute($sql, ['sender_id' => $sender_id, 'receiver_id' => $receiver_id])->fetchAll();
    }

    public function getUnreadMessages($employee_id)
    {
        $sql = 'SELECT * FROM eer_messages WHERE receiver_id = :employee_id AND is_read = 0';
        return $this->execute($sql, ['employee_id' => $employee_id])->fetchAll();
    }
}
