<?php
namespace App\Models;

class Recognition extends BaseModel
{
    public function getRecognitions()
    {
        $senderSql = $this->getEmployeeNameSql('s', 'sender_name');
        $receiverSql = $this->getEmployeeNameSql('t', 'receiver_name');
        $sql = "SELECT r.*, $senderSql, $receiverSql FROM eer_recognitions r JOIN employees s ON r.sender_id = s.eer_employee_id JOIN employees t ON r.receiver_id = t.eer_employee_id ORDER BY r.created_at DESC";
        return $this->execute($sql)->fetchAll();
    }

    public function sendRecognition($sender_id, $receiver_id, $message, $points)
    {
        $sql = 'INSERT INTO eer_recognitions (sender_id, receiver_id, message, points, created_at) VALUES (:sender_id, :receiver_id, :message, :points, NOW())';
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
