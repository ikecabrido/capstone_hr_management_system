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

        $query = "SELECT * FROM $this->table WHERE id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateName($user_id, $new_name)
    {
        if (!$user_id || !$new_name) {
            return false;
        }

        $query = "UPDATE users SET full_name = :full_name WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':full_name' => $new_name,
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
