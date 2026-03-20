<?php
namespace App\Models;

class GrievanceUpdate extends BaseModel
{
    public function create($data)
    {
        $sql = 'INSERT INTO grievance_updates (grievance_id, update_text, updated_by, updated_at) VALUES (:grievance_id, :update_text, :updated_by, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function getByGrievance($grievance_id)
    {
        return $this->execute('SELECT * FROM grievance_updates WHERE grievance_id = :grievance_id ORDER BY updated_at ASC', ['grievance_id' => $grievance_id])->fetchAll();
    }
}
