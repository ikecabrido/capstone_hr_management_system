<?php

require_once __DIR__ . '/../config/Database.php';

class PayrollRequest
{
    private $conn;
    private $table = "ep_payroll_request";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function all()
    {
        $sql = "
            SELECT
                pr.*,
                e.employee_code,
                e.first_name,
                e.last_name
            FROM {$this->table} pr
            LEFT JOIN employees e
                ON pr.employee_id = e.employee_id
            ORDER BY pr.id DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $sql = "
            SELECT
                pr.*,
                e.employee_no,
                e.full_name
            FROM {$this->table} pr
            LEFT JOIN employees e
                ON pr.employee_id = e.employee_id
            WHERE pr.id = :id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByEmployeeId($employeeId)
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE employee_id = :employee_id
            ORDER BY requested_at DESC, id DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $sql = "
            INSERT INTO {$this->table} (
                employee_id,
                request_type,
                purpose,
                remarks,
                payroll_period_start,
                payroll_period_end,
                status
            ) VALUES (
                :employee_id,
                :request_type,
                :purpose,
                :remarks,
                :payroll_period_start,
                :payroll_period_end,
                'Pending'
            )
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':request_type' => $data['request_type'],
            ':purpose' => $data['purpose'] ?? null,
            ':remarks' => $data['remarks'] ?? null,
            ':payroll_period_start' => $data['payroll_period_start'] ?? null,
            ':payroll_period_end' => $data['payroll_period_end'] ?? null
        ]);
    }
    public function updateStatus($id, $status, $processedBy)
    {
        $sql = "
UPDATE {$this->table}
SET
status = :status,
processed_by = :processed_by,
processed_at = CURRENT_TIMESTAMP
WHERE id = :id
";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':processed_by' => $processedBy,
            ':id' => $id
        ]);
    }
    public function delete($id)
    {
        $sql = "
DELETE FROM {$this->table}
WHERE id = :id
";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
    public function countByStatus($status)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM {$this->table}
            WHERE status = :status
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':status' => $status
        ]);

        return (int) $stmt->fetchColumn();
    }
    public function countAll()
    {
        $sql = "
            SELECT COUNT(*)
            FROM {$this->table}
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
    public function latest($limit = 5)
    {
        $limit = (int) $limit;

        $sql = "
            SELECT
                pr.*,
                e.employee_no,
                e.full_name
            FROM {$this->table} pr
            LEFT JOIN employees e
                ON pr.employee_id = e.employee_id
            ORDER BY pr.requested_at DESC, pr.id DESC
            LIMIT {$limit}
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
