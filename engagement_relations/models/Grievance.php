<?php
namespace App\Models;

class Grievance extends BaseModel
{
    public function getGrievances()
    {
        return $this->execute('SELECT g.*, e.name AS employee_name FROM grievances g JOIN employees e ON g.employee_id = e.id ORDER BY g.created_at DESC')->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM grievances WHERE id = :id', ['id' => $id])->fetch();
    }

    public function fileGrievance($employee_id, $subject, $description)
    {
        $sql = 'INSERT INTO grievances (employee_id, subject, description, status, created_at) VALUES (:employee_id, :subject, :description, :status, NOW())';
        $params = [
            'employee_id' => $employee_id,
            'subject' => $subject,
            'description' => $description,
            'status' => 'pending',
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function updateStatus($id, $status)
    {
        $sql = 'UPDATE grievances SET status = :status WHERE id = :id';
        $this->execute($sql, ['status' => $status, 'id' => $id]);
        return $this->find($id);
    }
}
