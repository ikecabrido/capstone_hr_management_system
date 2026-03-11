<?php
/**
 * Employee Model for Time & Attendance System
 * Manages employee data, credentials, and information
 */

require_once __DIR__ . '/../config/Database.php';

class Employee
{
    private $conn;
    private $table = "employees";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get employee by user_id
     */
    public function getByUserId($user_id)
    {
        $query = "SELECT e.*, u.username, u.email, u.role 
                  FROM " . $this->table . " e
                  JOIN users u ON e.user_id = u.user_id
                  WHERE e.user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee by employee_id
     */
    public function getById($employee_id)
    {
        $query = "SELECT e.*, u.username, u.email, u.role 
                  FROM " . $this->table . " e
                  JOIN users u ON e.user_id = u.user_id
                  WHERE e.employee_id = :employee_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active employees
     */
    public function getAll($status = 'ACTIVE', $limit = 100, $offset = 0)
    {
        $query = "SELECT e.*, u.username, u.email, u.role 
                  FROM " . $this->table . " e
                  JOIN users u ON e.user_id = u.user_id
                  WHERE e.status = :status
                  ORDER BY e.first_name, e.last_name
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee full name
     */
    public function getFullName($employee_id)
    {
        $query = "SELECT CONCAT(first_name, ' ', last_name) as full_name 
                  FROM " . $this->table . " 
                  WHERE employee_id = :employee_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['full_name'] ?? 'Unknown';
    }

    /**
     * Update employee information
     */
    public function update($employee_id, $data)
    {
        $query = "UPDATE " . $this->table . " SET ";
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        
        $query .= implode(", ", $fields) . " WHERE employee_id = :employee_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $data[$key]);
        }

        return $stmt->execute();
    }

    /**
     * Get employee count
     */
    public function getTotalCount($status = 'ACTIVE')
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}
