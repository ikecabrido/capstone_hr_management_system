<?php
require_once __DIR__ . '/../config/Database.php';
class MedicalRecord
{
    private $conn;
    private $table = "cm_medical_records";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getByEmployee($employee_id)
    {
        $query = "SELECT c.*, e.full_name
              FROM {$this->table} c
              JOIN employees e ON c.patient_id = e.id
              WHERE c.patient_id = ?
              ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
