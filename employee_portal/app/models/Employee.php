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
        $query = "SELECT e.*, u.username, u.role 
                  FROM " . $this->table . " e
                  JOIN users u ON e.user_id = u.id
                  WHERE e.user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get employee by employee_no
     */
    public function getById($employee_no)
    {
        die;
        $query = "SELECT e.*, u.username, u.role 
                  FROM " . $this->table . " e
                  LEFT JOIN users u ON e.user_id = u.id
                  WHERE e.employee_no = :employee_no LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active employees
     */
    public function getAll($status = 'Active', $limit = 100, $offset = 0)
    {
        $query = "SELECT e.*, u.username, u.role 
                  FROM " . $this->table . " e
                  LEFT JOIN users u ON e.user_id = u.id
                  WHERE e.employment_status = :status
                  ORDER BY e.full_name
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
    public function getFullName($employee_no)
    {
        $query = "SELECT full_name 
                  FROM " . $this->table . " 
                  WHERE employee_no = :employee_no LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['full_name'] ?? 'Unknown';
    }

    /**
     * Update employee information
     */
    public function update($employee_no, $data)
    {
        $query = "UPDATE " . $this->table . " SET ";
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        $query .= implode(", ", $fields) . " WHERE employee_no = :employee_no";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);

        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $data[$key]);
        }

        return $stmt->execute();
    }

    /**
     * Get employee count
     */
    public function getTotalCount($status = 'Active')
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE employment_status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function all()
    {
        $query = "
        SELECT *, full_name
        FROM " . $this->table . "
        ORDER BY full_name ASC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId($user_id)
    {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
