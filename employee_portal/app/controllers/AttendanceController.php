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

            // Only allow POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: index.php?url=dashboard");
                exit;
            }

            $user_id = Session::get('user_id');
            $employee_no = $_POST['employee_no'] ?? null;
            $method = 'MANUAL';
            $todayDate = date('Y-m-d');

            if (!$employee_no) {
                Session::set('error', 'Employee not found.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            $existingRecord = $this->attendanceModel->getTodayAttendance($employee_no);

            // ✅ Holiday check
            if ($this->attendanceModel->isHoliday($todayDate)) {

                $holiday = $this->attendanceModel->getHolidayInfo($todayDate);

                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Holiday'],
                    'FAILED',
                    'Cannot time in on holiday: ' . ($holiday['name'] ?? 'Unknown')
                );

                Session::set('error', 'Today is a holiday (' . ($holiday['name'] ?? 'Unknown') . ')');

                header("Location: index.php?url=dashboard");
                exit;
            }

            // ✅ Already timed in
            if ($existingRecord && !empty($existingRecord['time_in'])) {

                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Already timed in'],
                    'FAILED'
                );

                Session::set('error', 'Already timed in at ' . Helper::formatTime($existingRecord['time_in']));

                header("Location: index.php?url=dashboard");
                exit;
            }

            // ✅ Insert Time In
            if ($this->attendanceModel->timeIn($employee_no, $method)) {

                $record = $this->attendanceModel->getTodayAttendance($employee_no);
                $status = Helper::determineStatus($record['time_in']);

                $this->auditLog->log(
                    'TIME_IN_SUCCESS',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['method' => $method, 'status' => $status],
                    'SUCCESS'
                );

                Session::set('success', 'Time In recorded at ' . Helper::formatTime($record['time_in']));
            } else {

                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'DB error'],
                    'FAILED'
                );

                Session::set('error', 'Failed to record time in.');
            }

            // ✅ FINAL REDIRECT (always reached)
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
        try {
            Session::start();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: index.php?url=dashboard");
                exit;
            }

            $user_id = Session::get('user_id');
            $employee_no = $_POST['employee_no'] ?? null;

            if (!$employee_no) {
                Session::set('error', 'Employee not found.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            $record = $this->attendanceModel->getTodayAttendance($employee_no);

            if (!$record) {
                $this->auditLog->log(
                    'TIME_OUT_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'No time in record'],
                    'FAILED'
                );

                Session::set('error', 'Please record Time In first.');
                header("Location: index.php?url=dashboard");
                exit;
            }

            if (!empty($record['time_out'])) {
                $this->auditLog->log(
                    'TIME_OUT_FAILED',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['reason' => 'Already timed out'],
                    'FAILED'
                );

                Session::set('error', 'Already timed out at ' . Helper::formatTime($record['time_out']));
                header("Location: index.php?url=dashboard");
                exit;
            }

            if ($this->attendanceModel->timeOut($record['attendance_id'])) {

                $updatedRecord = $this->attendanceModel->getTodayAttendance($employee_no);

                $duration  = Helper::calculateDuration($updatedRecord['time_in'], $updatedRecord['time_out']);
                $hoursData = Helper::calculateHours($updatedRecord['time_in'], $updatedRecord['time_out'], 8);

                $this->attendanceModel->updateHours($record['attendance_id'], $hoursData);
                $employee = $this->employeeModel->findByUserId($employee_no);

                $this->auditLog->log(
                    'TIME_OUT_SUCCESS',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    [
                        'duration' => $duration,
                        'hours' => $hoursData
                    ],
                    'SUCCESS'
                );

                Session::set(
                    'success',
                    'Time Out at ' . Helper::formatTime($updatedRecord['time_out']) .
                        ' | Total Hours: ' . $hoursData['total_hours']
                );
            } else {

                $this->auditLog->log(
                    'TIME_OUT_FAILED',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['reason' => 'Database error'],
                    'FAILED'
                );

                Session::set('error', 'Failed to record time out.');
            }

            header("Location: index.php?url=dashboard");
            exit;
        } catch (Exception $e) {

            error_log("TimeOut Error: " . $e->getMessage());

            $this->auditLog->log(
                'TIME_OUT_ERROR',
                $user_id ?? null,
                $employee_no ?? null,
                null,
                ['error' => $e->getMessage()],
                'FAILED'
            );

            Session::set('error', 'An error occurred. Please contact HR.');

            header("Location: index.php?url=dashboard");
            exit;
        }
    }

    /**
     * Process QR attendance (Smart - handles both Time In and Time Out)
     * Validates token and records time in or time out based on current status
     * 
     * @param int $employee_no - Employee ID
     * @param string $token - QR token
     * @return array - Response array
     */
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

            // Mark token as used
            $this->qrHelper->markUsed($token, $employee_no);

            // Check if employee has timed in today
            $todayRecord = $this->attendanceModel->getTodayAttendance($employee_no);

            // Smart decision: if already timed in, do time out; otherwise do time in
            if ($todayRecord && !empty($todayRecord['time_in']) && empty($todayRecord['time_out'])) {
                // Employee already timed in, so do TIME OUT
                $result = $this->timeOut($employee_no, 'QR');
            } else if (!$todayRecord || empty($todayRecord['time_in'])) {
                // Employee hasn't timed in yet, so do TIME IN
                $result = $this->timeIn($employee_no, 'QR');
            } else {
                // Already timed out
                $result = [
                    'success' => false,
                    'message' => 'You have already timed out today at ' . Helper::formatTime($todayRecord['time_out'])
                ];
            }

            if ($result['success']) {
                $this->auditLog->log(
                    'QR_SCAN_SUCCESS',
                    $user_id,
                    $employee_no,
                    null,
                    ['token_id' => $tokenData['token_id'], 'action' => isset($result['time_in']) ? 'TIME_IN' : 'TIME_OUT'],
                    'SUCCESS'
                );
            }

            return $result;
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

    /**
     * Get today's attendance record for employee
     * 
     * @param int $employee_no - Employee ID
     * @return array|null - Attendance record or null
     */
    public function getTodayRecord($employee_no)
    {
        return $this->attendanceModel->getTodayAttendance($employee_no);
    }

    /**
     * Get attendance status for display
     * 
     * @param int $employee_no - Employee ID
     * @return array - Status information
     */
    public function getStatus($employee_no)
    {

        $record = $this->getTodayRecord($employee_no);
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
