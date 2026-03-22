<?php
namespace App\Models;

class Message extends BaseModel
{
    public function create($data)
    {
        $sql = 'INSERT INTO eer_messages (sender_id, receiver_id, message, timestamp) VALUES (:sender_id, :receiver_id, :message, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function threads($employee_id)
    {
        $senderSql = $this->getEmployeeNameSql('s', 'sender_name');
        $receiverSql = $this->getEmployeeNameSql('r', 'receiver_name');
        $sql = "SELECT m.*, $senderSql, $receiverSql FROM eer_messages m 
                LEFT JOIN employees s ON m.sender_id=s.eer_employee_id 
                LEFT JOIN employees r ON m.receiver_id=r.eer_employee_id 
                ORDER BY m.timestamp DESC";
        return $this->execute($sql)->fetchAll();
    }
}
