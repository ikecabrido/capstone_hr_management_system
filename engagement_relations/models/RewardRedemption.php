<?php
namespace App\Models;

class RewardRedemption extends BaseModel
{
    public function getAllRedemptions()
    {
        $sql = 'SELECT rr.*, e.full_name AS employee_name, r.name AS reward_name, rr.redeemed_at 
                FROM eer_reward_redemptions rr 
                JOIN employees e ON rr.employee_id = e.employee_id 
                JOIN eer_rewards r ON rr.reward_id = r.eer_reward_id';
        return $this->execute($sql)->fetchAll();
    }

    public function createRedemption($employee_id, $reward_id, $points_used)
    {
        $sql = 'INSERT INTO eer_reward_redemptions (employee_id, reward_id, points_used, redeemed_at) 
                VALUES (:employee_id, :reward_id, :points_used, NOW())';
        $params = [
            'employee_id' => $employee_id,
            'reward_id' => $reward_id,
            'points_used' => $points_used,
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }
}