<?php
namespace App\Models;

class Badge extends BaseModel
{
    public function all()
    {
        return $this->execute('SELECT * FROM eer_badges')->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM eer_badges WHERE eer_badge_id = :id', ['id' => $id])->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO eer_badges (name, description) VALUES (:name, :description)';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
