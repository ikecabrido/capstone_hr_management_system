<?php
require_once __DIR__ . '/../config/Database.php';

class Leave
{
    private $conn;
    private $table = 'ta_leave_requests';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create leave request
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table}
                  (employee_id, leave_type_id, start_date, end_date, details, supporting_document, status)
                  VALUES 
                  (:employee_id, :leave_type_id, :start_date, :end_date, :details, :document, 'Pending')";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':employee_id'   => $data['employee_id'],
            ':leave_type_id' => $data['leave_type_id'],
            ':start_date'    => $data['start_date'],
            ':end_date'      => $data['end_date'],
            ':details'       => $data['details'] ?? '',
            ':document'      => $data['supporting_document'] ?? null
        ]);
    }

    /**
     * Get all leave requests of an employee
     */
    public function getByEmployee($employee_id)
    {
        $query = "SELECT lr.*, lt.leave_type_name
                  FROM {$this->table} lr
                  JOIN ta_leave_types lt 
                      ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.employee_id = :employee_id
                  ORDER BY lr.start_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single leave request
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update leave status (Approve / Reject)
     */
    public function updateStatus($id, $status, $reject_reason = null)
    {
        $query = "UPDATE {$this->table}
                  SET status = :status,
                      reject_reason = :reject_reason,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':status'        => $status,
            ':reject_reason' => $reject_reason,
            ':id'            => $id
        ]);
    }

    /**
     * Get all pending requests
     */
    public function getPending()
    {
        $query = "SELECT lr.*, lt.leave_type_name
                  FROM {$this->table} lr
                  JOIN ta_leave_types lt 
                      ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.status = 'Pending'
                  ORDER BY lr.date_submitted DESC";

        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total leave days of employee (Approved only)
     */
    public function getTotalLeaveDays($employee_id)
    {
        $query = "SELECT start_date, end_date 
                  FROM {$this->table}
                  WHERE employee_id = :employee_id
                  AND status = 'Approved'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalDays = 0;

        foreach ($rows as $row) {
            $start = new DateTime($row['start_date']);
            $end   = new DateTime($row['end_date']);

            $days = $start->diff($end)->days + 1;
            $totalDays += $days;
        }

        return $totalDays;
    }

    public function getTotalLeaves($employee_id)
    {
        $query = "SELECT start_date, end_date 
              FROM {$this->table} 
              WHERE employee_id = :employee_id
              AND status = 'Approved'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);
        $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalDays = 0;

        foreach ($leaves as $leave) {
            if (!empty($leave['start_date']) && !empty($leave['end_date'])) {
                $start = new DateTime($leave['start_date']);
                $end   = new DateTime($leave['end_date']);

                $days = $start->diff($end)->days + 1;

                $totalDays += max(0, $days);
            }
        }

        return $totalDays;
    }

    public function getUsedLeaves($employee_id)
    {
        $query = "SELECT COUNT(*) FROM {$this->table} 
              WHERE employee_id = :employee_id 
              AND status = 'Approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);
        return (int) $stmt->fetchColumn();
    }

    public function getLeavesByEmployee($employee_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE employee_id = :employee_id ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
