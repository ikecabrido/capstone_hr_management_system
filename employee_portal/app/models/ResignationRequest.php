<?php
require_once __DIR__ . '/../config/Database.php';

class ResignationRequest
{
    private $conn;
    private $table = "ep_resignation_requests";
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function all()
    {
        $query = "
        SELECT
            rr.*,
            e.employee_code,
            e.first_name,
            e.last_name
        FROM {$this->table} rr
        INNER JOIN employees e
            ON rr.employee_id = e.employee_id
        ORDER BY rr.date_submitted DESC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data)
    {
        $query = "
        INSERT INTO {$this->table} (
            employee_id,
            resignation_type,
            resignation_reason,
            attachment,
            intended_last_working_day,
            employee_remarks
        ) VALUES (
            :employee_id,
            :resignation_type,
            :resignation_reason,
            :attachment,
            :intended_last_working_day,
            :employee_remarks
        )
    ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $stmt->bindValue(':resignation_type', $data['resignation_type']);
        $stmt->bindValue(':resignation_reason', $data['resignation_reason']);
        $stmt->bindValue(':attachment', $data['attachment']);
        $stmt->bindValue(':intended_last_working_day', $data['intended_last_working_day']);
        $stmt->bindValue(':employee_remarks', $data['employee_remarks']);

        return $stmt->execute();
    }
    public function updateRemarks($data)
    {
        $query = "
        UPDATE {$this->table}
        SET
            hr_remarks = :hr_remarks,
            updated_at = CURRENT_TIMESTAMP
        WHERE resignation_id = :resignation_id
    ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':hr_remarks' => $data['hr_remarks'],
            ':resignation_id' => $data['resignation_id']
        ]);
    }
    public function updateStatus($data)
    {
        $query = "
        UPDATE {$this->table}
        SET
            status = :status,
            reviewed_by = :reviewed_by,
            reviewed_at = :reviewed_at
        WHERE resignation_id = :resignation_id
    ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':status' => $data['status'],
            ':reviewed_by' => $data['reviewed_by'],
            ':reviewed_at' => $data['reviewed_at'],
            ':resignation_id' => $data['resignation_id']
        ]);
    }
}
