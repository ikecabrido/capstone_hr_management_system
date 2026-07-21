<?php
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../helpers/Helper.php';
require_once __DIR__ . '/../helpers/LeaveAttendanceHelper.php';

use TimeAttendancePortal\Helpers\LeaveAttendanceHelper;

class LeaveController
{
    private $leaveModel;
    private $leaveHelper;

    public function __construct()
    {
        $this->leaveModel = new Leave();
        $this->leaveHelper = new LeaveAttendanceHelper();
    }

    /**
     * Submit a new leave request
     * CRITICAL: Checks overlaps, balance, and can auto-approve
     */
    public function submitRequest($data)
    {
        try {
            // Extract data
            $employee_id = $data['employee_id'];
            $leave_type_id = $data['leave_type_id'];
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];

            // 1. Check for overlapping leave (CRITICAL)
            if ($this->leaveModel->hasOverlappingLeave($employee_id, $start_date, $end_date)) {
                return [
                    'success' => false,
                    'message' => 'You have an existing leave request for these dates. Please select different dates.'
                ];
            }

            // 2. Calculate working days (excluding holidays)
            $total_days = $this->leaveHelper->calculateWorkingDays($start_date, $end_date);
            $data['total_days'] = $total_days;

            // 3. Get leave type to check if it requires approval
            $leaveType = $this->leaveModel->getLeaveTypeById($leave_type_id);
            if (!$leaveType) {
                return ['success' => false, 'message' => 'Invalid leave type'];
            }

            // 4. Check leave balance ONLY for deductible leave
            if ($leaveType['is_deductible']) {
                if (!$this->leaveHelper->hasSufficientBalance($employee_id, $leave_type_id, $total_days)) {
                    $balance = $this->leaveHelper->getLeaveBalance($employee_id, $leave_type_id);
                    $remaining = $balance[0]['remaining_balance'] ?? 0;
                    return [
                        'success' => false,
                        'message' => "Insufficient leave balance. You have {$remaining} days remaining."
                    ];
                }
            }

            // 5. Create the request
            $created = $this->leaveModel->createRequest($data);
            if (!$created) {
                return ['success' => false, 'message' => 'Failed to submit leave request'];
            }

            // 6. AUTO-APPROVE if leave_type doesn't require approval
            // This is for Emergency Leave (requires_approval = 0)
            if (!$leaveType['requires_approval']) {
                $last_insert_id = $this->leaveModel->getLastInsertedId();
                if ($last_insert_id) {
                    $this->approveLeaveRequest($last_insert_id, $employee_id, true);
                }
            }

            return [
                'success' => true,
                'message' => !$leaveType['requires_approval'] 
                    ? 'Leave request submitted and auto-approved!' 
                    : 'Leave request submitted successfully',
                'auto_approved' => !$leaveType['requires_approval']
            ];

        } catch (Exception $e) {
            error_log("Leave Request Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Get all leave requests for an employee
     */
    public function getEmployeeRequests($employee_id)
    {
        try {
            return $this->leaveModel->getRequestsByEmployee($employee_id);
        } catch (Exception $e) {
            error_log("Get Leave Requests Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get leave request by ID
     */
    public function getRequest($leave_request_id)
    {
        try {
            return $this->leaveModel->getById($leave_request_id);
        } catch (Exception $e) {
            error_log("Get Leave Request Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Approve a leave request
     * CRITICAL: Deducts balance and processes daily records
     */
    public function approve($leave_request_id, $approver_id, $is_hr = false, $remarks = '')
    {
        try {
            $leave = $this->leaveModel->getById($leave_request_id);
            if (!$leave) {
                return ['success' => false, 'message' => 'Leave request not found'];
            }

            return $this->approveLeaveRequest($leave_request_id, $approver_id, true);

        } catch (Exception $e) {
            error_log("Leave Approval Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Internal method to approve leave and deduct balance
     * @param bool $auto_approve - whether this is auto-approval (emergency leave)
     */
    private function approveLeaveRequest($leave_request_id, $approver_id, $auto_approve = false)
    {
        try {
            // Use Leave model's approveLeavRequest which handles everything
            $result = $this->leaveModel->approveLeavRequest($leave_request_id, $approver_id);

            if ($result) {
                return [
                    'success' => true,
                    'message' => $auto_approve 
                        ? 'Leave request auto-approved and balance deducted'
                        : 'Leave request approved and balance deducted'
                ];
            }

            return ['success' => false, 'message' => 'Failed to approve leave request'];

        } catch (Exception $e) {
            error_log("Approve Leave Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Reject a leave request
     */
    public function reject($leave_request_id, $approver_id, $reason = '')
    {
        try {
            $result = $this->leaveModel->rejectLeaveRequest($leave_request_id, $approver_id, $reason);
            
            if ($result) {
                return ['success' => true, 'message' => 'Leave request rejected'];
            }
            
            return ['success' => false, 'message' => 'Failed to reject leave request'];
        } catch (Exception $e) {
            error_log("Leave Rejection Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Get leave request status
     */
    public function getStatus($leave_request_id)
    {
        try {
            $request = $this->leaveModel->getById($leave_request_id);
            return $request ? $request['status'] : null;
        } catch (Exception $e) {
            error_log("Get Leave Status Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get leave balances for an employee
     */
    public function getLeaveBalances($employee_id)
    {
        try {
            return $this->leaveHelper->getLeaveBalance($employee_id);
        } catch (Exception $e) {
            error_log("Get Leave Balances Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if dates have overlapping leave
     */
    public function checkOverlappingLeave($employee_id, $start_date, $end_date)
    {
        try {
            $overlap = $this->leaveModel->hasOverlappingLeave($employee_id, $start_date, $end_date);
            return [
                'has_overlap' => $overlap,
                'message' => $overlap ? 'Overlapping leave exists' : 'No overlapping leave'
            ];
        } catch (Exception $e) {
            error_log("Overlap Check Error: " . $e->getMessage());
            return ['has_overlap' => false, 'message' => 'Error checking overlap'];
        }
    }
}
