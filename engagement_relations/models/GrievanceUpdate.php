<?php
namespace App\Models;

class GrievanceUpdate extends BaseModel
{
    public function create($data)
    {
        $sql = 'INSERT INTO eer_grievance_updates (grievance_id, update_text, updated_by, updated_at) VALUES (:grievance_id, :update_text, :updated_by, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function getByGrievance($grievance_id)
    {
        $nameSql = $this->getEmployeeNameSql('e', 'updated_by_name');
        $sql = "SELECT gu.*, $nameSql FROM eer_grievance_updates gu 
                LEFT JOIN employees e ON gu.updated_by = e.eer_employee_id 
                WHERE gu.grievance_id = :grievance_id 
                ORDER BY gu.updated_at ASC";
        return $this->execute($sql, ['grievance_id' => $grievance_id])->fetchAll();
    }
}
