<?php

/**
 * Leave Approval Service
 * Handles transferring approved leave requests to Legal & Compliance tables
 * Including leave balance deduction
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/LeaveBalance.php';

class LeaveApprovalService
{
    private $conn;
    private $leaveBalance;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->leaveBalance = new LeaveBalance();
    }

    /**
     * Approve leave request and transfer to LC tables
     * @param int $leaveRequestId - ID from ta_leave_requests
     * @param int $checkedById - User ID who approved
     * @return bool
     */
    public function approveLeaveRequest($leaveRequestId, $checkedById = null)
    {
        try {
            // Get the leave request
            $query = "SELECT * FROM ta_leave_requests WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $leaveRequestId]);
            $leaveRequest = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$leaveRequest) {
                throw new Exception("Leave request not found");
            }

            // Calculate total days
            $startDate = new DateTime($leaveRequest['start_date']);
            $endDate = new DateTime($leaveRequest['end_date']);
            $totalDays = $endDate->diff($startDate)->days + 1;

            // Get leave type info
            $ltQuery = "SELECT * FROM ta_leave_types WHERE leave_type_id = :id";
            $ltStmt = $this->conn->prepare($ltQuery);
            $ltStmt->execute([':id' => $leaveRequest['leave_type_id']]);
            $leaveType = $ltStmt->fetch(PDO::FETCH_ASSOC);

            // Check if leave is deductible
            if ($leaveType && $leaveType['is_deductible']) {
                // Deduct from employee's leave balance
                $currentYear = date('Y');
                $deductResult = $this->leaveBalance->deductBalance(
                    $leaveRequest['employee_id'],
                    $leaveRequest['leave_type_id'],
                    $totalDays,
                    $currentYear
                );

                if (!$deductResult) {
                    throw new Exception("Insufficient leave balance for deduction");
                }
            }

            // Update status in ta_leave_requests
            $updateQuery = "UPDATE ta_leave_requests 
                           SET status = 'Approved', 
                               updated_at = NOW()
                           WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute([':id' => $leaveRequestId]);

            // Transfer to lc_leave_requests
            $lcQuery = "INSERT INTO lc_leave_requests 
                       (employee_id, leave_type, start_date, end_date, total_days, reason, status, checked_by, checked_at, hr_comments)
                       VALUES 
                       (:employee_id, :leave_type, :start_date, :end_date, :total_days, :reason, 'pending', :checked_by, NOW(), :hr_comments)";

            $lcStmt = $this->conn->prepare($lcQuery);
            $lcStmt->execute([
                ':employee_id' => $leaveRequest['employee_id'],
                ':leave_type' => $leaveRequest['details'] ?? 'Leave',
                ':start_date' => $leaveRequest['start_date'],
                ':end_date' => $leaveRequest['end_date'],
                ':total_days' => $totalDays,
                ':reason' => $leaveRequest['reason'] ?? '',
                ':checked_by' => $checkedById,
                ':hr_comments' => ''
            ]);

            $lcLeaveId = $this->conn->lastInsertId();

            // Transfer documents to lc_leave_documents if they exist
            if (!empty($leaveRequest['documents'])) {
                $documents = json_decode($leaveRequest['documents'], true);
                
                if (is_array($documents)) {
                    foreach ($documents as $docPath) {
                        $docType = pathinfo($docPath, PATHINFO_FILENAME);
                        
                        $docQuery = "INSERT INTO lc_leave_documents 
                                    (leave_id, document_type, file_path)
                                    VALUES 
                                    (:leave_id, :document_type, :file_path)";
                        
                        $docStmt = $this->conn->prepare($docQuery);
                        $docStmt->execute([
                            ':leave_id' => $lcLeaveId,
                            ':document_type' => $docType,
                            ':file_path' => $docPath
                        ]);
                    }
                }
            }

            return true;

        } catch (Exception $e) {
            error_log("Leave approval transfer failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject leave request and restore balance if already deducted
     * @param int $leaveRequestId
     * @param string $rejectReason
     * @return bool
     */
    public function rejectLeaveRequest($leaveRequestId, $rejectReason = '')
    {
        try {
            // Get the leave request
            $query = "SELECT * FROM ta_leave_requests WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $leaveRequestId]);
            $leaveRequest = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$leaveRequest) {
                throw new Exception("Leave request not found");
            }

            // If already approved/balance deducted, restore it
            if ($leaveRequest['status'] === 'Approved') {
                $startDate = new DateTime($leaveRequest['start_date']);
                $endDate = new DateTime($leaveRequest['end_date']);
                $totalDays = $endDate->diff($startDate)->days + 1;

                $currentYear = date('Y');
                $this->leaveBalance->restoreBalance(
                    $leaveRequest['employee_id'],
                    $leaveRequest['leave_type_id'],
                    $totalDays,
                    $currentYear
                );
            }

            // Mark as rejected
            $rejectQuery = "UPDATE ta_leave_requests 
                           SET status = 'Rejected', 
                               reject_reason = :reason,
                               updated_at = NOW()
                           WHERE id = :id";
            
            $rejectStmt = $this->conn->prepare($rejectQuery);
            return $rejectStmt->execute([
                ':reason' => $rejectReason,
                ':id' => $leaveRequestId
            ]);

        } catch (Exception $e) {
            error_log("Leave rejection failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending leave requests for approval
     * @return array
     */
    public function getPendingRequests()
    {
        try {
            $query = "SELECT tlr.*, 
                            lt.leave_type_name,
                            e.full_name,
                            e.employee_no
                     FROM ta_leave_requests tlr
                     JOIN ta_leave_types lt ON tlr.leave_type_id = lt.leave_type_id
                     JOIN employees e ON tlr.employee_id = e.employee_id
                     WHERE tlr.status = 'Pending'
                     ORDER BY tlr.date_submitted DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Fetch pending requests failed: " . $e->getMessage());
            return [];
        }
    }
}
