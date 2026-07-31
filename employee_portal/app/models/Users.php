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
    public function all()
    {
        $stmt = $this->conn->prepare("
                    SELECT *
            FROM {$this->table}
        ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function login($username)
    {
        $query = "
        SELECT
            id,
            username,
            password,
            email,
            role,
            is_admin,
            theme,
            created_at
        FROM {$this->table}
        WHERE username = :username
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
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
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table}
                (
                    role,
                    username,
                    email,
                    password
                )
                VALUES
                (
                    :role,
                    :username,
                    :email,
                    :password
                )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':role' => $data['role'],
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $data['password']
        ]);
    }
    public function filterSelf($excludeUserId = null)
    {
        if ($excludeUserId) {
            $query = "SELECT * FROM {$this->table}
                  WHERE id != :user_id
                  ORDER BY id DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':user_id', $excludeUserId, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
            $stmt = $this->conn->query($query);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function toggleAdmin($id)
    {
        $query = "UPDATE users
              SET is_admin = NOT is_admin
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
