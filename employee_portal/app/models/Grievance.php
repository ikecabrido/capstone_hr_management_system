<?php

require_once __DIR__ . '/../config/Database.php';

class Grievance
{
    private $conn;
    private $table = "eer_grievances";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function all()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " 
              (employee_id, subject, description, assigned_to, status, category, anonymous, attachment_path)
              VALUES (:employee_id, :subject, :description, :assigned_to, :status, :category, :anonymous, :attachment_path)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':subject' => $data['subject'],
            ':description' => $data['description'],
            ':assigned_to' => $data['assigned_to'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':category' => $data['category'] ?? 'Workplace Conflict',
            ':anonymous' => $data['anonymous'] ?? 0,
            ':attachment_path' => $data['attachment_path'] ?? null
        ]) ? $this->conn->lastInsertId() : false;
    }
}
