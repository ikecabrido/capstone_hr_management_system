<?php
require_once __DIR__ . '/../config/Database.php';

class LeaveType
{
    private $conn;
    private $table = 'ta_leave_types';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all leave types
     */
    public function getAll()
    {
        $query = "SELECT leave_type_id, leave_type_name, description, days_per_year 
                  FROM {$this->table} 
                  ORDER BY leave_type_name";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a leave type by ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE leave_type_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllLeaveTypes()
    {
        $query = "SELECT leave_type_id, leave_type_name FROM {$this->table} ORDER BY leave_type_name";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
