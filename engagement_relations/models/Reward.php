<?php
namespace App\Models;

class Reward extends BaseModel
{
    public function all()
    {
        return $this->execute('SELECT * FROM rewards ORDER BY points_required')->fetchAll();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO rewards (name, points_required) VALUES (:name, :points_required)';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
