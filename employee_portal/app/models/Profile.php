<?php
require_once __DIR__ . '/../config/Database.php';
class Profile
{
    private $conn;
    private $table = "users";
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function findByUserId($user_id)
    {
        $query = "
        SELECT
            u.username,
            u.email,
            u.role,
            u.is_admin,
            u.theme,
            u.created_at,

            e.employee_code,
            e.first_name,
            e.middle_name,
            e.last_name,
            e.suffix,
            e.gender,
            e.department,
            e.position,
            e.profile_image
        FROM users u
        LEFT JOIN employees e
            ON e.user_id = u.id
        WHERE u.id = :user_id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateName($user_id, $new_name)
    {
        if (!$user_id || !$new_name) {
            return false;
        }

        $query = "UPDATE {$this->table} SET username = :username WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':username' => $new_name,
            ':user_id' => $user_id
        ]);
    }
    public function updatePassword($user_id, $hashed_password)
    {
        if (!$user_id || !$hashed_password) {
            return false;
        }

        $query = "UPDATE users SET password = :password WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':password' => $hashed_password,
            ':user_id' => $user_id
        ]);
    }
}
