<?php
require_once __DIR__ . '/../config/Database.php';

class EmployeeDocuments
{
    private $conn;
    private $table = "ep_employee_documents";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function all()
    {
        $stmt = $this->conn->prepare("
        SELECT DISTINCT ed.*, 
               d.department_name, 
               e.full_name AS approver_name, 
               s.full_name AS submitter_name
        FROM {$this->table} ed
        LEFT JOIN departments d ON ed.department = d.id
        LEFT JOIN employees e ON ed.approver_id = e.id
        LEFT JOIN employees s ON ed.submit_by = s.id
        ORDER BY ed.submitted_on DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDepartment($departmentId)
    {
        $stmt = $this->conn->prepare("
        SELECT DISTINCT ed.*, 
               d.department_name, 
               e.full_name AS approver_name, 
               s.full_name AS submitter_name
        FROM {$this->table} ed
        LEFT JOIN departments d ON ed.department = d.id
        LEFT JOIN employees e ON ed.approver_id = e.id
        LEFT JOIN employees s ON ed.submit_by = s.id
        WHERE ed.department = :department
        ORDER BY ed.submitted_on DESC
    ");
        $stmt->bindParam(':department', $departmentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($approvalId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE approval_id = :approval_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':approval_id', $approvalId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (title, description, department, approver_id, submit_by, file_path, remarks, decision)
                  VALUES (:title, :description, :department, :approver_id, :submit_by, :file_path, :remarks, :decision)";

        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value ?: null);
        }

        return $stmt->execute() ? $this->conn->lastInsertId() : false;
    }

    public function update($approvalId, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE approval_id = :approval_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':approval_id', $approvalId, PDO::PARAM_INT);

        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value ?: null);
        }

        return $stmt->execute();
    }
}
