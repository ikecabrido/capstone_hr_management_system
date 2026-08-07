<?php
namespace App\Models;

class GrievanceUpdate extends BaseModel
{
    public function create($data)
    {
        $sql = 'INSERT INTO eer_grievance_updates (grievance_id, update_text, updated_by_user_id, updated_at) VALUES (:grievance_id, :update_text, :updated_by_user_id, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }

    public function getByGrievance($grievance_id)
    {
        $nameSql = $this->getEmployeeNameSql('e', 'updated_by_name');
        $sql = "SELECT gu.*, $nameSql FROM eer_grievance_updates gu 
                LEFT JOIN users u ON gu.updated_by_user_id = u.id 
                LEFT JOIN employees e ON u.employee_id = e.employee_id
                WHERE gu.grievance_id = :grievance_id 
                ORDER BY gu.updated_at ASC";
        return $this->execute($sql, ['grievance_id' => $grievance_id])->fetchAll();
    }
}
