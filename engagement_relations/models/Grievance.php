<?php
namespace App\Models;

class Grievance extends BaseModel
{
    public function getGrievances()
    {
        $reporterName = $this->getEmployeeNameSql('e', 'employee_name');
        $assignedName = $this->getEmployeeNameSql('ea', 'assigned_name');

        return $this->execute("SELECT g.*, $reporterName, $assignedName FROM eer_grievances g 
                LEFT JOIN employees e ON g.employee_id = e.employee_id 
                LEFT JOIN employees ea ON g.assigned_to = ea.employee_id 
                ORDER BY g.created_at DESC")->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM eer_grievances WHERE eer_grievance_id = :id', ['id' => $id])->fetch();
    }

    public function fileGrievance($employee_id, $subject, $description, $category = 'Workplace Conflict', $anonymous = 0, $attachment_path = null)
    {
        $employee = $this->execute('SELECT employee_id FROM employees WHERE employee_id = :id', ['id' => $employee_id])->fetch();
        if (!$employee) {
            throw new \Exception("Employee with ID {$employee_id} does not exist in the system.");
        }

        $sql = 'INSERT INTO eer_grievances (employee_id, subject, description, category, anonymous, attachment_path, status, created_at) 
                VALUES (:employee_id, :subject, :description, :category, :anonymous, :attachment_path, :status, NOW())';
        $params = [
            'employee_id' => $employee_id,
            'subject' => $subject,
            'description' => $description,
            'category' => $category,
            'anonymous' => $anonymous,
            'attachment_path' => $attachment_path,
            'status' => 'Submitted',
        ];
        $this->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function updateStatus($id, $status)
    {
        $sql = 'UPDATE eer_grievances SET status = :status WHERE eer_grievance_id = :id';
        $this->execute($sql, ['status' => $status, 'id' => $id]);
        return $this->find($id);
    }

    public function addInvestigationNotes($id, $notes, $hrPersonnelId)
    {
        $sql = 'INSERT INTO grievance_notes (grievance_id, hr_personnel_id, notes, created_at) 
                VALUES (:id, :hrPersonnelId, :notes, NOW())';
        $this->execute($sql, ['id' => $id, 'hrPersonnelId' => $hrPersonnelId, 'notes' => $notes]);
    }

    public function updateConfidentialFlag($id, $isConfidential)
    {
        $sql = 'UPDATE eer_grievances SET confidential = :isConfidential WHERE eer_grievance_id = :id';
        $this->execute($sql, ['isConfidential' => $isConfidential, 'id' => $id]);
    }

    public function assignTo($id, $assignedTo)
    {
        $sql = 'UPDATE eer_grievances SET assigned_to = :assignedTo WHERE eer_grievance_id = :id';
        $this->execute($sql, ['assignedTo' => $assignedTo, 'id' => $id]);
        return $this->find($id);
    }
}

