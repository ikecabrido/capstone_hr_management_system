<?php
namespace App\Models;

class Reward extends BaseModel
{
    public function all()
    {
        return $this->execute('SELECT * FROM eer_rewards ORDER BY points_required')->fetchAll();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO eer_rewards (name, points_required, description) VALUES (:name, :points_required, :description)';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
