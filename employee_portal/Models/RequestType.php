<?php
require_once "../app/Core/Database.php";

class RequestType
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function all()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                id,
                name,
                description,
                icon,
                requires_attachment,
                is_active,
                created_at
            FROM request_types
            ORDER BY id DESC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
        INSERT INTO request_types
        (name, description, icon, requires_attachment, is_active)
        VALUES (?, ?, ?, ?, ?)
    ");

        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['icon'],
            $data['requires_attachment'],
            $data['is_active']
        ]);
    }

    public function update($data)
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE request_types
                SET
                    name = :name,
                    description = :description,
                    icon = :icon,
                    requires_attachment = :requires_attachment,
                    is_active = :is_active
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':icon' => $data['icon'],
                ':requires_attachment' => $data['requires_attachment'],
                ':is_active' => $data['is_active']
            ]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to update request type: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM request_types WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("
        SELECT *
        FROM request_types
        WHERE id = :id
        LIMIT 1
    ");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
