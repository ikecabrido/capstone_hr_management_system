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
                e.full_name,
                e.user_id,
                u.id AS user_id_ref,
                u.username,
                u.password,
                u.role
            FROM employees e
            LEFT JOIN {$this->table} u ON e.user_id = u.id
            WHERE e.employee_no = :employee_no
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function savePasswordResetToken($userId, $token, $expires)
    {
        $sql = "
            UPDATE {$this->table}
            SET password_reset_token = :token,
                password_reset_expires = :expires
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':id' => $userId
        ]);
    }

    public function findByResetToken($token)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE password_reset_token = :token
            AND password_reset_expires > NOW()
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':token' => $token
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $hashedPassword)
    {
        $sql = "
            UPDATE {$this->table}
            SET password = :password
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

    public function clearPasswordResetToken($userId)
    {
        $sql = "
            UPDATE {$this->table}
            SET password_reset_token = NULL,
                password_reset_expires = NULL
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $userId
        ]);
    }
    public function findById($id)
    {
        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE id = :id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
