<?php

require_once __DIR__ . '/../Core/Database.php';

class LeaveRequest extends Database
{
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    public function all(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM leave_requests ORDER BY date_submitted DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO leave_requests
            (name, type_of_leave, start_date, end_date, details, supporting_document, date_submitted, status, updated_at, reject_reason)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['name'],
            $data['type_of_leave'],
            $data['start_date'],
            $data['end_date'],
            $data['details'],
            $data['supporting_document'],
            $data['date_submitted'],
            $data['status'],
            $data['updated_at'],
            $data['reject_reason']
        ]);
    }

    public function updateStatus(int $leaveId, string $status, ?string $adminName = null, ?string $rejectReason = null): bool
    {
        $adminName = $adminName ?: 'Admin';

        $updatedAt = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("
        UPDATE leave_requests
        SET 
            status = :status,
            name = COALESCE(name, :adminName),
            updated_at = :updatedAt,
            reject_reason = :rejectReason
        WHERE id = :id
    ");

        return $stmt->execute([
            ':status'       => $status,
            ':adminName'    => $adminName,
            ':updatedAt'    => $updatedAt,
            ':rejectReason' => $rejectReason,
            ':id'           => $leaveId
        ]);
    }

    public function delete(int $leaveId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM leave_requests WHERE id = :id");
        return $stmt->execute([':id' => $leaveId]);
    }
}
