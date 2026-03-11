<?php
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../helpers/AuditLog.php';
require_once __DIR__ . '/../core/Session.php';

class LeaveController
{
    private $leaveModel;
    private $notificationModel;
    private $auditLog;

    public function __construct()
    {
        $this->leaveModel = new Leave();
        $this->notificationModel = new Notification();
        $this->auditLog = new AuditLog();
    }

    /**
     * Submit a new leave request
     */
    public function submitRequest($data)
    {
        try {
            Session::start();
            $user_id = Session::get('user_id');

            $created = $this->leaveModel->createRequest($data);
            if ($created) {
                // Log the request submission
                $this->auditLog->log('LEAVE_REQUEST_SUBMITTED', $user_id, $data['employee_id'], null, 
                    json_encode(['leave_type_id' => $data['leave_type_id'], 'days' => $data['total_days']]), 'SUCCESS');
                return true;
            }
            $this->auditLog->log('LEAVE_REQUEST_FAILED', $user_id, $data['employee_id'], null, 
                json_encode($data), 'FAILED');
            return false;
        } catch (Exception $e) {
            error_log("Leave Request Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve a leave request
     */
    public function approve($leave_request_id, $approver_id, $is_hr = false, $remarks = '')
    {
        try {
            Session::start();
            $user_id = Session::get('user_id');
            
            $status = $is_hr ? 'APPROVED_BY_HR' : 'APPROVED_BY_HEAD';
            $result = $this->leaveModel->updateStatus($leave_request_id, $status, $approver_id, $remarks);
            
            if ($result) {
                $this->auditLog->log('LEAVE_' . $status, $user_id, null, null, 
                    json_encode(['leave_request_id' => $leave_request_id]), 'SUCCESS');
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Leave Approval Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject a leave request
     */
    public function reject($leave_request_id, $approver_id, $reason = '')
    {
        try {
            Session::start();
            $user_id = Session::get('user_id');
            
            $result = $this->leaveModel->updateStatus($leave_request_id, 'REJECTED', $approver_id, $reason);
            
            if ($result) {
                $this->auditLog->log('LEAVE_REJECTED', $user_id, null, null, 
                    json_encode(['leave_request_id' => $leave_request_id, 'reason' => $reason]), 'SUCCESS');
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Leave Rejection Error: " . $e->getMessage());
            return false;
        }
    }
}
