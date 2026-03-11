<?php
require_once __DIR__ . '/../config/Database.php';

class Leave
{
    private $conn;
    private $table = 'leave_requests';

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
                  (employee_id, leave_type_id, start_date, end_date, reason, total_days, status, submitted_at)
                  VALUES (:employee_id, :leave_type_id, :start_date, :end_date, :reason, :total_days, 'PENDING', NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $stmt->bindParam(':leave_type_id', $data['leave_type_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':total_days', $data['total_days'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get all pending requests for a department head
     */
    public function getPendingByDepartmentHead($deptHeadUserId)
    {
        $query = "SELECT lr.*, e.first_name, e.last_name, e.employee_number, e.department, lt.leave_type_name
                  FROM leave_requests lr
                  INNER JOIN employees e ON lr.employee_id = e.employee_id
                  INNER JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  INNER JOIN department_heads dh ON dh.department = e.department
                  WHERE dh.user_id = :user_id AND lr.status = 'PENDING'
                  ORDER BY lr.submitted_at DESC";

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
        $query = "SELECT lr.*, e.first_name, e.last_name, e.employee_number, e.department, lt.leave_type_name
                  FROM leave_requests lr
                  INNER JOIN employees e ON lr.employee_id = e.employee_id
                  INNER JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.status IN ('PENDING', 'APPROVED_BY_HEAD')
                  ORDER BY lr.submitted_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update leave request status with approver details
     */
    public function updateStatus($leave_request_id, $status, $user_id, $remarks = '')
    {
        if ($status === 'APPROVED_BY_HEAD') {
            $query = "UPDATE leave_requests 
                      SET status = :status, 
                          department_head_id = :user_id, 
                          department_head_approval_date = NOW(), 
                          department_head_remarks = :remarks,
                          updated_at = NOW()
                      WHERE leave_request_id = :id";
        } elseif ($status === 'APPROVED_BY_HR') {
            $query = "UPDATE leave_requests 
                      SET status = :status, 
                          hr_admin_id = :user_id, 
                          hr_admin_approval_date = NOW(), 
                          hr_admin_remarks = :remarks,
                          updated_at = NOW()
                      WHERE leave_request_id = :id";
        } else { // REJECTED
            $query = "UPDATE leave_requests 
                      SET status = :status, 
                          hr_admin_id = :user_id, 
                          hr_admin_remarks = :remarks,
                          updated_at = NOW()
                      WHERE leave_request_id = :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get leave request by ID
     */
    public function getById($leave_request_id)
    {
        $query = "SELECT * FROM leave_requests WHERE leave_request_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $leave_request_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
