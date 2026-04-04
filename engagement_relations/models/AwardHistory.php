<?php
namespace App\Models;

class AwardHistory extends BaseModel
{
    public function all()
    {
        $sql = 'SELECT ah.*, e.full_name AS employee_name FROM eer_award_history ah 
                JOIN employees e ON ah.employee_id = e.employee_id';
        return $this->execute($sql)->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM eer_award_history WHERE eer_award_history_id = :id', ['id' => $id])->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO eer_award_history (employee_id, award_name) VALUES (:employee_id, :award_name)';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
