<?php
require_once __DIR__ . '/../Core/Database.php';

class User extends Database
{
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function all($excludeUserId = null)
    {
        $sql = "
        SELECT id, name, email, isAdmin, isActive
        FROM users
        WHERE isActive = 1
    ";

        $params = [];

        if ($excludeUserId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeUserId;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $name, $email, $isAdmin)
    {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE users 
             SET name = ?, email = ?, isAdmin = ? 
             WHERE id = ?"
            );

            return $stmt->execute([$name, $email, $isAdmin, $id]);
        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, isAdmin, isActive 
             FROM users 
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
