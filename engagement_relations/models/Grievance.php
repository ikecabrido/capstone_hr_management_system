<?php
namespace App\Models;

class Grievance extends BaseModel
{
    public function getGrievances()
    {
        // Use employee_name alias so the view can render consistently.
        $reporterName = $this->getEmployeeNameSql('e', 'employee_name');
        $assignedName = $this->getEmployeeNameSql('ea', 'assigned_name');

        return $this->execute("SELECT g.*, $reporterName, $assignedName FROM eer_grievances g JOIN employees e ON g.employee_id = e.eer_employee_id LEFT JOIN employees ea ON g.assigned_to = ea.eer_employee_id ORDER BY g.created_at DESC")->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM eer_grievances WHERE eer_grievance_id = :id', ['id' => $id])->fetch();
    }

    public function fileGrievance($employee_id, $subject, $description, $category = 'Workplace Conflict', $anonymous = 0, $attachment_path = null)
    {
        // Verify employee exists before filing grievance
        $employee = $this->execute('SELECT eer_employee_id FROM employees WHERE eer_employee_id = :id', ['id' => $employee_id])->fetch();
        if (!$employee) {
            throw new \Exception("Employee with ID {$employee_id} does not exist in the system.");
        }
        
        $sql = 'INSERT INTO eer_grievances (employee_id, subject, description, category, anonymous, attachment_path, status, created_at) VALUES (:employee_id, :subject, :description, :category, :anonymous, :attachment_path, :status, NOW())';
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

    public function assignTo($id, $assigned_to)
    {
        $sql = 'UPDATE eer_grievances SET assigned_to = :assigned_to WHERE eer_grievance_id = :id';
        $this->execute($sql, ['assigned_to' => $assigned_to, 'id' => $id]);
        return $this->find($id);
    }

    public function updateResolution($id, $resolution_notes, $action_taken)
    {
        $sql = 'UPDATE eer_grievances SET resolution_notes = :resolution_notes, action_taken = :action_taken WHERE eer_grievance_id = :id';
        $this->execute($sql, ['resolution_notes' => $resolution_notes, 'action_taken' => $action_taken, 'id' => $id]);
        return $this->find($id);
    }

    public function submitSatisfaction($id, $rating, $comment)
    {
        $sql = 'UPDATE eer_grievances SET satisfaction_rating = :rating, satisfaction_comment = :comment WHERE eer_grievance_id = :id';
        $this->execute($sql, ['rating' => $rating, 'comment' => $comment, 'id' => $id]);
        return $this->find($id);
    }
}

