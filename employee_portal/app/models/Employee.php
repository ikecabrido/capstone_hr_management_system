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
    public function getByUserId($user_id)
    {
        $query = "SELECT e.*, u.username, u.role, u.is_admin
                  FROM " . $this->table . " e
                  JOIN users u ON e.user_id = u.id
                  WHERE e.user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getById($employee_id)
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
    public function getAll($limit = 100, $offset = 0)
    {
        $query = "
        SELECT e.*, u.username, u.role
        FROM {$this->table} e
        LEFT JOIN users u ON e.user_id = u.id
        ORDER BY e.last_name
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
        SELECT *, first_name, last_name
        FROM " . $this->table . "
        ORDER BY last_name ASC
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
    public function getByEmployeeId($id)
    {
        $query = "SELECT e.*, u.username, u.role
              FROM " . $this->table . " e
              LEFT JOIN users u ON e.user_id = u.id
              WHERE e.employee_id = :id
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getNonAdminEmployees()
    {
        $query = "
        SELECT 
            e.*
        FROM $this->table e
        INNER JOIN users u 
            ON e.user_id = u.id
        WHERE u.is_admin = 0
        ORDER BY e.last_name ASC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $query = "
        SELECT *
        FROM {$this->table}
        WHERE id = :id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
