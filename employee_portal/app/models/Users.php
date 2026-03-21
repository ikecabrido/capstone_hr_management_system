<?php
require_once __DIR__ . '/../config/Database.php';

class User
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($employee_id)
    {
        $query = "
        SELECT 
            e.employee_id,
            e.full_name,
            e.user_id,
            u.id AS user_id_ref,
            u.username,
            u.password,
            u.role
        FROM employees e
        LEFT JOIN users u ON e.user_id = u.id
        WHERE e.employee_id = :employee_id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
