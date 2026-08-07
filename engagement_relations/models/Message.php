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
                LEFT JOIN users u_s ON CAST(m.sender_id AS UNSIGNED) = u_s.id
                LEFT JOIN users u_r ON CAST(m.receiver_id AS UNSIGNED) = u_r.id
                WHERE CAST(m.sender_id AS CHAR) = :employee_id OR CAST(m.receiver_id AS CHAR) = :employee_id
                ORDER BY m.timestamp DESC";
        return $this->execute($sql, ['employee_id' => (string)$employee_id])->fetchAll();
    }

    public function allThreads()
    {
        $sql = "SELECT m.*,
                COALESCE(e_s.full_name, u_s.username, u_s.full_name, m.sender_id) AS sender_name,
                COALESCE(e_r.full_name, u_r.username, u_r.full_name, m.receiver_id) AS receiver_name
                FROM eer_messages m
                LEFT JOIN employees e_s ON m.sender_id = e_s.employee_id
                LEFT JOIN employees e_r ON m.receiver_id = e_r.employee_id
                LEFT JOIN users u_s ON CAST(m.sender_id AS UNSIGNED) = u_s.id
                LEFT JOIN users u_r ON CAST(m.receiver_id AS UNSIGNED) = u_r.id
                ORDER BY m.timestamp DESC";
        return $this->execute($sql)->fetchAll();
    }
}
