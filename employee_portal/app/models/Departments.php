<?php
require_once __DIR__ . '/../config/Database.php';

class Departments
{
    private $conn;
    private $table = "departments";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all departments
     */
    public function all()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY department_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get department by ID
     */
    public function getById($departmentId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE department_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $departmentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create department
     */
    public function create($name)
    {
        $query = "INSERT INTO " . $this->table . " (department_name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute() ? $this->conn->lastInsertId() : false;
    }

    /**
     * Update department
     */
    public function update($departmentId, $name)
    {
        $query = "UPDATE " . $this->table . " SET department_name = :name WHERE department_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $departmentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}