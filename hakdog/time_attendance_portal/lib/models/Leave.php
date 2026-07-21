<?php
require_once __DIR__ . '/../../config/Database.php';

use TimeAttendancePortal\Config\Database;

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
     * Create a new leave request
     */
    public function createRequest($data)
    {
        try {
            // Log the incoming data for debugging
            error_log("Leave::createRequest called with: " . json_encode($data));
            
            // Verify employee exists before attempting insert
            $verify_query = "SELECT employee_id FROM employees WHERE employee_id = :employee_id LIMIT 1";
            $verify_stmt = $this->conn->prepare($verify_query);
            $verify_stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);
            $result = $verify_stmt->execute();
            error_log("Employee verification - Execute result: " . ($result ? 'true' : 'false'));
            
            $employee = $verify_stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Employee verification - Fetch result: " . json_encode($employee));
            
            if (!$employee) {
                error_log("Leave creation failed: Employee ID {$data['employee_id']} not found in employees table");
                return false;
            }

            $query = "INSERT INTO `ta_leave_requests` 
                      (employee_id, leave_type_id, start_date, end_date, details, status)
                      VALUES (:employee_id, :leave_type_id, :start_date, :end_date, :details, 'Pending')";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("Failed to prepare statement: " . json_encode($this->conn->errorInfo()));
                return false;
            }
            
            // Store values in variables for bindParam
            $employee_id = $data['employee_id'];
            $leave_type_id = $data['leave_type_id'];
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];
            $details = $data['details'] ?? $data['reason'] ?? '';
            
            error_log("Binding parameters - emp_id: {$employee_id}, type_id: {$leave_type_id}, start: {$start_date}, end: {$end_date}, details: {$details}");
            
            $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->bindParam(':leave_type_id', $leave_type_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':details', $details);

            $result = $stmt->execute();
            error_log("Insert execute result: " . ($result ? 'true' : 'false'));
            if (!$result) {
                error_log("Insert failed. Error info: " . json_encode($stmt->errorInfo()));
            }
            return $result;
        } catch (\Exception $e) {
            error_log("Leave creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get last inserted leave request ID
     */
    public function getLastInsertedId()
    {
        return $this->conn->lastInsertId();
    }

    /**
     * Get all leave requests for an employee
     */
    public function getRequestsByEmployee($employee_id)
    {
        $query = "SELECT lr.*, 
                         lt.leave_type_name,
                         DATEDIFF(lr.end_date, lr.start_date) + 1 as total_days
                  FROM ta_leave_requests lr
                  INNER JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.employee_id = :employee_id
                  ORDER BY lr.date_submitted DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all pending requests for a department head
     */
    public function getPendingByDepartmentHead($deptHeadUserId)
    {
        $query = "SELECT lr.*, 
                         e.full_name, 
                         e.department, 
                         lt.leave_type_name,
                         DATEDIFF(lr.end_date, lr.start_date) + 1 as total_days
                  FROM ta_leave_requests lr
                  INNER JOIN employees e ON lr.employee_id = e.employee_id
                  INNER JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id
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
        $query = "SELECT lr.*, 
                         e.full_name, 
                         e.department, 
                         lt.leave_type_name,
                         DATEDIFF(lr.end_date, lr.start_date) + 1 as total_days
                  FROM ta_leave_requests lr
                  INNER JOIN employees e ON lr.employee_id = e.employee_id
                  INNER JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id
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
        $query = "UPDATE ta_leave_requests 
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
        $query = "SELECT * FROM ta_leave_requests WHERE id = :id";
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
        $query = "SELECT remaining_days FROM ta_leave_balances 
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
        $query = "UPDATE ta_leave_balances 
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
                  FROM ta_leave_balances lb
                  JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
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

    /**
     * Check if employee has overlapping leave request
     * CRITICAL: Prevents duplicate/conflicting leave
     */
    public function hasOverlappingLeave($employee_id, $start_date, $end_date, $exclude_leave_id = null)
    {
        $query = "SELECT COUNT(*) as count FROM ta_leave_requests
                  WHERE employee_id = :employee_id
                  AND status IN ('Pending', 'Approved')
                  AND start_date <= :end_date
                  AND end_date >= :start_date";

        $params = [
            ':employee_id' => $employee_id,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ];

        if ($exclude_leave_id) {
            $query .= " AND id != :exclude_leave_id";
            $params[':exclude_leave_id'] = $exclude_leave_id;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Get approved leave for a specific date
     * Used during attendance generation
     */
    public function getApprovedLeaveForDate($employee_id, $date)
    {
        $query = "SELECT lr.*, lt.leave_type_name, lt.is_deductible
                  FROM ta_leave_requests lr
                  JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.employee_id = :employee_id
                  AND lr.status = 'Approved'
                  AND :date BETWEEN lr.start_date AND lr.end_date
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get leave type details
     */
    public function getLeaveTypeById($leave_type_id)
    {
        $query = "SELECT * FROM ta_leave_types WHERE leave_type_id = :leave_type_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':leave_type_id', $leave_type_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Approve leave and deduct balance
     * CRITICAL: Must handle daily processing and holiday skipping
     */
    public function approveLeavRequest($leave_request_id, $approved_by_id)
    {
        try {
            $this->conn->beginTransaction();

            // Get leave request details
            $query = "SELECT * FROM ta_leave_requests WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);
            $stmt->execute();
            $leave = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$leave) {
                return false;
            }

            // Update status
            $query = "UPDATE ta_leave_requests 
                      SET status = 'Approved', 
                          updated_at = NOW()
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch (\Exception $e) {
            $this->conn->rollBack();
            error_log("Error approving leave: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject leave request
     */
    public function rejectLeaveRequest($leave_request_id, $rejected_by_id, $reason = '')
    {
        $query = "UPDATE ta_leave_requests 
                  SET status = 'Rejected', 
                      reject_reason = :reason,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);
        $stmt->bindParam(':reason', $reason);

        return $stmt->execute();
    }

    /**
     * Mark attendance as ON_LEAVE for approved leave dates
     * Called during attendance generation
     */
    public function markAttendanceAsLeave($attendance_id, $leave_request_id)
    {
        $query = "UPDATE ta_attendance
                  SET status = 'ON_LEAVE',
                      leave_request_id = :leave_request_id
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);
        $stmt->bindParam(':leave_request_id', $leave_request_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
