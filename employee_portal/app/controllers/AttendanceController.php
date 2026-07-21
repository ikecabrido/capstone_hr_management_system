<?php

/**
 * Attendance Controller for Time & Attendance System
 * Handles attendance recording, time in/out, and QR processing
 */

// Set PHP timezone to Philippines (UTC+8)
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../core/QRHelper.php';
require_once __DIR__ . '/../core/Helper.php';
require_once __DIR__ . '/../core/AuditLog.php';
require_once __DIR__ . '/../core/Session.php';

class AttendanceController
{
    private $attendanceModel;
    private $employeeModel;
    private $qrHelper;
    private $auditLog;

    public function __construct()
    {
        $this->attendanceModel = new Attendance();
        $this->employeeModel = new Employee();
        $this->qrHelper = new QRHelper();
        $this->auditLog = new AuditLog();
    }
    public function timeIn()
    {
        try {
            Session::start();
            $employee_id = $_POST['employee_id'];

            // Only allow POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: index.php?url=dashboard");
                exit;
            }
            $user_id = Session::get('user_id');
            $method = 'MANUAL';
            $todayDate = date('Y-m-d');
            if (!$employee_id) {
                Session::set('error', 'Employee not found.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            $existingRecord = $this->attendanceModel->getTodayAttendance($employee_id);

            //Holiday check
            if ($this->attendanceModel->isHoliday($todayDate)) {

                $holiday = $this->attendanceModel->getHolidayInfo($todayDate);

                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_id,
                    null,
                    ['reason' => 'Holiday'],
                    'FAILED',
                    'Cannot time in on holiday: ' . ($holiday['name'] ?? 'Unknown')
                );

                Session::set('error', 'Today is a holiday (' . ($holiday['name'] ?? 'Unknown') . ')');

                header("Location: index.php?url=dashboard");
                exit;
            }

            //Already timed in
            if ($existingRecord && !empty($existingRecord['time_in'])) {

                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_id,
                    null,
                    ['reason' => 'Already timed in'],
                    'FAILED'
                );

                Session::set('error', 'Already timed in at ' . Helper::formatTime($existingRecord['time_in']));

                header("Location: index.php?url=dashboard");
                exit;
            }
            //Insert Time In
            if ($this->attendanceModel->timeIn($employee_id, $method)) {

                $record = $this->attendanceModel->getTodayAttendance($employee_id);
                $status = Helper::determineStatus($record['time_in']);

                $this->auditLog->log(
                    'TIME_IN_SUCCESS',
                    $user_id,
                    $employee_id,
                    $record['attendance_id'],
                    ['method' => $method, 'status' => $status],
                    'SUCCESS'
                );

                Session::set('success', 'Time In recorded at ' . Helper::formatTime($record['time_in']));
            } else {

                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_id,
                    null,
                    ['reason' => 'DB error'],
                    'FAILED'
                );

                Session::set('error', 'Failed to record time in.');
            }

            //REDIRECT 
            header("Location: index.php?url=dashboard");
            exit;
        } catch (Exception $e) {

            Session::set('error', $e->getMessage() ?: "Something went wrong.");
            header("Location: index.php?url=dashboard");
            exit;
        }
    }
    public function timeOut()
    {
        Session::start();

        $employee_id = $_POST['employee_id'] ?? null;
        $user_id = Session::get('user_id');

        if (!$employee_id) {
            Session::set('error', 'Employee not found');
            header("Location: index.php?url=dashboard");
            exit;
        }

        $record = $this->attendanceModel->getTodayAttendance($employee_id);

        if (!$record) {
            Session::set('error', 'Please record Time In first.');
            header("Location: index.php?url=dashboard");
            exit;
        }

        if (!empty($record['time_out'])) {
            Session::set('error', 'Already timed out at ' . Helper::formatTime($record['time_out']));
            header("Location: index.php?url=dashboard");
            exit;
        }

        if ($this->attendanceModel->timeOut($record['attendance_id'])) {

            $updatedRecord = $this->attendanceModel->getTodayAttendance($employee_id);
            $hoursData = Helper::calculateHours($updatedRecord['time_in'], $updatedRecord['time_out'], 8);

            $this->attendanceModel->updateHours($record['attendance_id'], $hoursData);

            Session::set(
                'success',
                'Time Out at ' . Helper::formatTime($updatedRecord['time_out']) .
                    ' | Total Hours: ' . $hoursData['total_hours']
            );
        } else {
            Session::set('error', 'Failed to record time out.');
        }

        header("Location: index.php?url=dashboard");
        exit;
    }

    /**
     * QR Attendance - Shows confirmation modal before recording attendance
     * Handles both initial token validation and attendance confirmation
     */
    public function qrAttendance()
    {
        try {
            Session::start();
            
            $token = $_GET['token'] ?? '';
            $confirm = $_POST['confirm'] ?? '';
            $user_id = Session::get('user_id');

            if (!$user_id) {
                Session::set('error', 'Please login first.');
                header("Location: index.php?url=auth-index");
                exit;
            }

            // Get employee ID from logged-in user
            $employeeData = $this->employeeModel->getByUserId($user_id);
            if (!$employeeData) {
                Session::set('error', 'Employee profile not found.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            $employee_id = $employeeData['employee_id'];

            // Empty token = should not happen
            if (empty($token)) {
                Session::set('error', 'No QR token provided.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            // If confirmation is submitted (POST request)
            if ($confirm === 'yes') {
                // Process the QR attendance with confirmation
                $result = $this->processQRAttendance($employee_id, $token);
                
                if ($result['success']) {
                    Session::set('success', $result['message']);
                } else {
                    Session::set('error', $result['message']);
                }
                
                header("Location: index.php?url=dashboard");
                exit;
            }

            // If cancellation is submitted
            if ($confirm === 'no') {
                Session::set('info', 'QR scan cancelled.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            // Show confirmation page (GET request)
            // NOTE: We don't validate token here - just show the modal
            // Token validation happens when user confirms
            // This matches hakdog's approach and allows for better UX

            // Check today's attendance to determine if TIME_IN or TIME_OUT
            $todayRecord = $this->attendanceModel->getTodayAttendance($employee_id);
            $action = 'TIME_IN';
            $statusText = 'Record Time In';
            
            if ($todayRecord && !empty($todayRecord['time_in']) && empty($todayRecord['time_out'])) {
                $action = 'TIME_OUT';
                $statusText = 'Record Time Out';
            } elseif ($todayRecord && !empty($todayRecord['time_out'])) {
                Session::set('error', 'Attendance already completed for today.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            // Load the confirmation modal view directly (standalone, mobile-friendly)
            $qrToken = $token;
            $currentTime = date('H:i:s');
            $currentDate = date('Y-m-d');
            $employee = $employeeData; // Pass employee data to view
            $employee_id = $employee_id; // Pass employee_id for AJAX
            
            // Load modal view directly without layout wrapper
            require __DIR__ . '/../views/employee-portal/qr-confirmation-modal.php';
            exit;

        } catch (Exception $e) {
            Session::set('error', 'Error: ' . $e->getMessage());
            header("Location: index.php?url=dashboard");
            exit;
        }
    }

    public function processQRAttendance($employee_no, $token)
    {
        Session::start();
        $user_id = Session::get('user_id');

        try {
            // Validate QR token
            $tokenData = $this->qrHelper->validateToken($token);

            if (!$tokenData) {
                $this->auditLog->log(
                    'QR_SCAN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Invalid or expired token'],
                    'FAILED',
                    'Token validation failed'
                );
                return [
                    'success' => false,
                    'message' => 'QR code has expired or is invalid. Please ask HR to generate a new one.'
                ];
            }

            // Check if token is for today
            if ($tokenData['generated_for_date'] !== Helper::getCurrentDate()) {
                $this->auditLog->log(
                    'QR_SCAN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Token not for today'],
                    'FAILED',
                    'Token date mismatch'
                );
                return [
                    'success' => false,
                    'message' => 'QR code is not valid for today.'
                ];
            }

            // Check if employee has timed in today
            $todayRecord = $this->attendanceModel->getTodayAttendance($employee_no);
            $action = '';

            // Smart decision: if already timed in, do time out; otherwise do time in
            if ($todayRecord && !empty($todayRecord['time_in']) && empty($todayRecord['time_out'])) {
                // Employee already timed in, so do TIME OUT
                if ($this->attendanceModel->timeOut($todayRecord['attendance_id'])) {
                    $updatedRecord = $this->attendanceModel->getTodayAttendance($employee_no);
                    $hoursData = Helper::calculateHours($updatedRecord['time_in'], $updatedRecord['time_out'], 8);
                    $this->attendanceModel->updateHours($todayRecord['attendance_id'], $hoursData);
                    
                    $action = 'TIME_OUT';
                    $message = 'Time Out recorded at ' . Helper::formatTime($updatedRecord['time_out']) . ' | Total Hours: ' . $hoursData['total_hours'];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to record time out.'
                    ];
                }
            } else if (!$todayRecord || empty($todayRecord['time_in'])) {
                // Employee hasn't timed in yet, so do TIME IN
                if ($this->attendanceModel->timeIn($employee_no, 'QR')) {
                    $record = $this->attendanceModel->getTodayAttendance($employee_no);
                    $action = 'TIME_IN';
                    $message = 'Time In recorded at ' . Helper::formatTime($record['time_in']);
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to record time in.'
                    ];
                }
            } else {
                // Already timed out
                return [
                    'success' => false,
                    'message' => 'You have already timed out today at ' . Helper::formatTime($todayRecord['time_out'])
                ];
            }

            // Mark token as used
            $this->qrHelper->markUsed($token, $employee_no);

            $this->auditLog->log(
                'QR_SCAN_SUCCESS',
                $user_id,
                $employee_no,
                null,
                ['token_id' => $tokenData['token_id'], 'action' => $action],
                'SUCCESS'
            );

            return [
                'success' => true,
                'message' => $message,
                'action' => $action
            ];
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            error_log("QR Processing Error: " . $error_msg . " | Trace: " . $e->getTraceAsString());
            $this->auditLog->log(
                'QR_SCAN_ERROR',
                $user_id,
                $employee_no,
                null,
                ['error' => $error_msg],
                'FAILED',
                $error_msg
            );
            return [
                'success' => false,
                'message' => 'Error processing QR code: ' . $error_msg
            ];
        }
    }

    public function getStatus($employee_id)
    {
        $record = $this->attendanceModel->getTodayAttendance($employee_id);

        if (!$record) {
            return [
                'status' => 'NOT_STARTED',
                'time_in' => null,
                'time_out' => null,
                'duration' => null
            ];
        }

        $status = 'TIME_IN_ONLY';
        if (!empty($record['time_out'])) {
            $status = 'COMPLETED';
        } elseif (empty($record['time_in'])) {
            $status = 'NOT_STARTED';
        }

        return [
            'status' => $status,
            'time_in' => $record['time_in'],
            'time_out' => $record['time_out'],
            'duration' => $status === 'COMPLETED' ? Helper::calculateDuration($record['time_in'], $record['time_out']) : null,
            'method' => $record['recorded_by'] ?? 'MANUAL'
        ];
    }
}
