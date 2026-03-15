<?php

require_once __DIR__ . '/../Core/Database.php';

class Request extends Database
{
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO requests
            (user_id, request_type_id, title, details, attachment)
            VALUES
            (:user_id, :request_type_id, :title, :details, :attachment)
        ");

        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':request_type_id' => $data['request_type_id'],
            ':title' => $data['title'],
            ':details' => $data['details'],
            ':attachment' => $data['attachment']
        ]);
    }

    public function all()
    {
        $stmt = $this->conn->prepare("
        SELECT 
            r.*,
            rt.name AS request_type
        FROM requests r
        LEFT JOIN request_types rt 
            ON r.request_type_id = rt.id
        ORDER BY r.created_at DESC
    ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatusAndRemarks($id, $status, $remarks)
    {
        try {
            $stmt = $this->conn->prepare("
            UPDATE requests
            SET status = :status,
                admin_remarks = :remarks,
                updated_at = NOW()
            WHERE id = :id
        ");

            return $stmt->execute([
                ':status' => $status,
                ':remarks' => $remarks,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Update Request Error: " . $e->getMessage());
            return false;
        }
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("
        SELECT * 
        FROM requests 
        WHERE id = ?
        LIMIT 1
    ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM requests WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
