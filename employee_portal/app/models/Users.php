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

    public function login($employee_no)
    {
        $query = "
        SELECT 
            e.employee_no,
            e.first_name,
            e.last_name,
            e.user_id,
            u.id AS user_id_ref,
            u.username,
            u.password,
            u.role
        FROM employees e
        LEFT JOIN users u ON e.user_id = u.id
        WHERE e.employee_no = :employee_no
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
