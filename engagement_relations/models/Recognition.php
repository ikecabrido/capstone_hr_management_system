<?php
namespace App\Models;

class Recognition extends BaseModel
{
    protected function getSenderNameSql($senderIdExpr, $alias = 'sender_name')
    {
        return "COALESCE(s.full_name, u.username, u.full_name, $senderIdExpr) AS $alias";
    }
    public function getRecognitions()
    {
        $senderSql = $this->getSenderNameSql('r.sender_id', 'sender_name');
        $receiverSql = $this->getEmployeeNameSql('t', 'receiver_name');
        $sql = "SELECT r.*, $senderSql, $receiverSql FROM eer_recognitions r 
                LEFT JOIN employees s ON r.sender_id = s.employee_id 
                LEFT JOIN users u ON CAST(r.sender_id AS UNSIGNED) = u.user_id
                JOIN employees t ON r.receiver_id = t.employee_id 
                ORDER BY r.created_at DESC";
        return $this->execute($sql)->fetchAll();
    }

    public function sendRecognition($sender_id, $receiver_id, $message, $points)
    {
        $sql = 'INSERT INTO eer_recognitions (sender_id, receiver_id, message, points, created_at) 
                VALUES (:sender_id, :receiver_id, :message, :points, NOW())';
        $params = [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'points' => $points,
        ];

        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function getHistoryByEmployee($employeeId)
    {
        $sql = "SELECT * FROM eer_recognitions WHERE receiver_id = :employeeId ORDER BY created_at DESC";
        return $this->execute($sql, ['employeeId' => $employeeId])->fetchAll();
    }

    public function updateRewardsCatalog($action, $data)
    {
        if ($action === 'add') {
            $sql = "INSERT INTO rewards_catalog (name, description, points) VALUES (:name, :description, :points)";
            return $this->execute($sql, $data);
        } elseif ($action === 'delete') {
            $sql = "DELETE FROM rewards_catalog WHERE id = :id";
            return $this->execute($sql, ['id' => $data['id']]);
        }
    }

    public function assignBadge($employeeId, $badgeId)
    {
        $sql = "INSERT INTO employee_badges (employee_id, badge_id, assigned_at) VALUES (:employeeId, :badgeId, NOW())";
        return $this->execute($sql, ['employeeId' => $employeeId, 'badgeId' => $badgeId]);
    }

    public function getTopRecognizedEmployees($limit = 10)
    {
        $sql = "SELECT receiver_id, COUNT(*) as recognition_count, SUM(points) as total_points 
                FROM eer_recognitions 
                GROUP BY receiver_id 
                ORDER BY total_points DESC, recognition_count DESC 
                LIMIT :limit";
        return $this->execute($sql, ['limit' => $limit])->fetchAll();
    }

    public function updateReward($id, $data)
    {
        $sql = "UPDATE rewards_catalog SET name = :name, description = :description, points = :points WHERE id = :id";
        $params = array_merge($data, ['id' => $id]);
        return $this->execute($sql, $params);
    }
}
