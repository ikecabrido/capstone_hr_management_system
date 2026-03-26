<?php
require_once __DIR__ . '/../config/Database.php';

class Leave
{
    private $conn;
    private $table = 'leaves';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new leave request
     */
    public function createRequest($data)
    {
        $query = "INSERT INTO `leave_requests` 
                  (employee_id, leave_type_id, start_date, end_date, details, status)
                  VALUES (:employee_id, :leave_type_id, :start_date, :end_date, :details, 'Pending')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $stmt->bindParam(':leave_type_id', $data['leave_type_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $details = $data['details'] ?? $data['reason'] ?? '';
        $stmt->bindParam(':details', $details);

        return $stmt->execute();
    }

    /**
     * Get all pending requests for a department head
     */
    public function getPendingByDepartmentHead($deptHeadUserId)
    {
        $query = "SELECT lr.*, e.full_name, e.department, lt.leave_type_name
                  FROM leave_requests lr
                  INNER JOIN employees e ON lr.employee_id = e.employee_id
                  INNER JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  INNER JOIN department_heads dh ON dh.department = e.department
                  WHERE dh.user_id = :user_id AND lr.status = 'Pending'
                  ORDER BY lr.date_submitted DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $deptHeadUserId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get pending and head-approved requests for HR admin
     */
    public function getForHRApproval()
    {
        $query = "SELECT lr.*, e.full_name, e.department, lt.leave_type_name
                  FROM leave_requests lr
                  INNER JOIN employees e ON lr.employee_id = e.employee_id
                  INNER JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.status IN ('Pending', 'Approved')
                  ORDER BY lr.date_submitted DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update leave request status with approver details
     */
    public function updateStatus($leave_request_id, $status, $user_id, $remarks = '')
    {
        $query = "UPDATE leave_requests 
                  SET status = :status, 
                      reject_reason = :remarks,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get leave request by ID
     */
    public function getById($leave_request_id)
    {
        $query = "SELECT * FROM leave_requests WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if employee has sufficient leave balance
     */
    public function checkLeaveBalance($employee_id, $leave_type_id, $requested_days)
    {
        $query = "SELECT remaining_days FROM leave_balances 
                  WHERE employee_id = :employee_id 
                  AND leave_type_id = :leave_type_id 
                  AND year = YEAR(CURDATE())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':leave_type_id', $leave_type_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return ['status' => false, 'message' => 'Leave balance record not found'];
        }

        if ($result['remaining_days'] < $requested_days) {
            return [
                'status' => false,
                'message' => 'Insufficient leave balance. Available: ' . $result['remaining_days'] . ' days'
            ];
        }

        return ['status' => true, 'remaining_balance' => $result['remaining_days']];
    }

    /**
     * Deduct days from leave balance after approval
     */
    public function deductLeaveBalance($employee_id, $leave_type_id, $days_to_deduct)
    {
        $query = "UPDATE leave_balances 
                  SET used_days = used_days + :days,
                      remaining_days = remaining_days - :days,
                      updated_at = NOW()
                  WHERE employee_id = :employee_id 
                  AND leave_type_id = :leave_type_id 
                  AND year = YEAR(CURDATE())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':leave_type_id', $leave_type_id, PDO::PARAM_INT);
        $stmt->bindParam(':days', $days_to_deduct, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get leave balance details for an employee
     */
    public function getLeaveBalance($employee_id, $leave_type_id = null)
    {
        $query = "SELECT lb.*, lt.leave_type_name, lt.days_per_year
                  FROM leave_balances lb
                  JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
                  WHERE lb.employee_id = :employee_id 
                  AND lb.year = YEAR(CURDATE())";

        if ($leave_type_id) {
            $query .= " AND lb.leave_type_id = :leave_type_id";
        }

        $query .= " ORDER BY lt.leave_type_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        if ($leave_type_id) {
            $stmt->bindParam(':leave_type_id', $leave_type_id, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $leave_type_id ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO leaves 
              (employee_id, leave_type, start_date, end_date, reason, status) 
              VALUES 
              (:employee_id, :leave_type, :start_date, :end_date, :reason, :status)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':employee_id' => $data['employee_id'],
            ':leave_type'  => $data['leave_type'],
            ':start_date'  => $data['start_date'],
            ':end_date'    => $data['end_date'],
            ':reason'      => $data['reason'],
            ':status'      => $data['status']
        ]);
    }

    public function getTotalLeaves($employee_id)
    {
        $query = "SELECT start_date, end_date 
              FROM leaves 
              WHERE employee_id = :employee_id
              AND status = 'Approved'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);
        $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalDays = 0;

        foreach ($leaves as $leave) {
            $start = new DateTime($leave['start_date']);
            $end   = new DateTime($leave['end_date']);

            $days = $start->diff($end)->days + 1;
            $totalDays += $days;
        }

        return $totalDays;
    }

    public function getUsedLeaves($employee_id)
    {
        $query = "SELECT COUNT(*) FROM leaves 
              WHERE employee_id = :employee_id 
              AND status = 'Approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);
        return (int) $stmt->fetchColumn();
    }

    public function getLeavesByEmployee($employee_id)
    {
        $query = "SELECT * FROM leaves WHERE employee_id = :employee_id ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employee_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
