<?php

require_once __DIR__ . '/../core/Database.php';

class Resignation
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO exit_resignations
                (employee_id, resignation_type, reason, notice_date, last_working_date,
                 comments, submitted_by, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['resignation_type'],
            $data['reason'],
            $data['notice_date'],
            $data['last_working_date'],
            $data['comments'] ?? null,
            $data['submitted_by']
        ]);

        return (int)$this->conn->lastInsertId();
    }

    public function forEmployee(string $employeeId): array
    {
        $stmt = $this->conn->prepare("
            SELECT id, resignation_type, reason, notice_date, last_working_date,
                   comments, status, created_at, approved_at
            FROM exit_resignations
            WHERE employee_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
