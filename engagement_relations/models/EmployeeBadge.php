<?php
namespace App\Models;

class EmployeeBadge extends BaseModel
{
    public function all()
    {
        $sql = 'SELECT eb.*, e.full_name AS employee_name, b.name AS badge_name, eb.awarded_at 
                FROM employee_badges eb 
                JOIN employees e ON eb.employee_id = e.employee_id 
                JOIN eer_badges b ON eb.badge_id = b.eer_badge_id';
        return $this->execute($sql)->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM employee_badges WHERE employee_badge_id = :id', ['id' => $id])->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO employee_badges (employee_id, badge_id, awarded_at) 
                VALUES (:employee_id, :badge_id, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
