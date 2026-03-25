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
            $user_id = Session::get('user_id');
            $employee_no = $_POST['employee_no'];
            $method = 'MANUAL';
            $todayDate = date('Y-m-d');
            $timeIn = $_POST['time_in'] ?? null;
            $existingRecord = $this->attendanceModel->getTodayAttendance($employee_no);

            //check if post
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "index.php?url=employee-documents-index"));
                exit;
            }

            //check if theres a holiday
            if ($this->attendanceModel->isHoliday($todayDate)) {

                $holiday = $this->attendanceModel->getHolidayInfo($todayDate);
                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Holiday'],
                    'FAILED',
                    'Cannot time in on holiday: ' . $holiday['holiday_name']
                );
                return [
                    'success' => false,
                    'message' => 'Today is a holiday (' . $holiday['holiday_name'] . '). No attendance recording required.',
                    'holiday_name' => $holiday['holiday_name']
                ];
            }

            //check existing record
            if ($existingRecord && !empty($existingRecord['time_in'])) {
                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Already timed in'],
                    'FAILED',
                    'Employee already has time in record for today'
                );
                return [
                    'success' => false,
                    'message' => 'You have already timed in today at ' . Helper::formatTime($existingRecord['time_in'])
                ];
            }

            //storing data
            if ($this->attendanceModel->timeIn($employee_no, $method)) {
                $record = $this->attendanceModel->getTodayAttendance($employee_no);

                $status = Helper::determineStatus($record['time_in']);
                $employee = $this->employeeModel->getById($employee_no);

                $this->auditLog->log(
                    'TIME_IN_SUCCESS',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['method' => $method, 'status' => $status],
                    'SUCCESS'
                );

                return [
                    'success' => true,
                    'message' => 'Time In recorded successfully at ' . Helper::formatTime($record['time_in']),
                    'employee_name' => $employee['full_name'],
                    'time_in' => $record['time_in'],
                    'status' => $status,
                    header("Location: index.php?url=dashboard")
                ];
            } else {
                $this->auditLog->log(
                    'TIME_IN_FAILED',
                    $user_id,
                    $employee_no,
                    null,
                    ['reason' => 'Database error'],
                    'FAILED',
                    'Failed to insert time in record'
                );
                return [
                    'success' => false,
                    'message' => 'Failed to record time in. Please try again.'
                ];
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage() ?: "Something went wrong while submitting.";
        }
    }

    public function timeOut()
    {
        Session::start();
        $user_id = Session::get('user_id');
        $employee_no = $_POST['employee_no'];
        $method = 'MANUAL';
        $record = $this->attendanceModel->getTodayAttendance($employee_no);

        //Check Record
        if (!$record) {
            $this->auditLog->log(
                'TIME_OUT_FAILED',
                $user_id,
                $employee_no,
                null,
                ['reason' => 'No time in record'],
                'FAILED',
                'No attendance record found for today'
            );
            return [
                'success' => false,
                'message' => 'Please record Time In first.'
            ];
        }

        //check if already time out
        if (!empty($record['time_out'])) {
            $this->auditLog->log(
                'TIME_OUT_FAILED',
                $user_id,
                $employee_no,
                $record['attendance_id'],
                ['reason' => 'Already timed out'],
                'FAILED',
                'Employee already has time out record'
            );
            return [
                'success' => false,
                'message' => 'You have already timed out today at ' . Helper::formatTime($record['time_out'])
            ];
        }
        try {
            // Update time out
            if ($this->attendanceModel->timeOut($record['attendance_id'])) {
                $updatedRecord = $this->attendanceModel->getTodayAttendance($employee_no);

                $duration = Helper::calculateDuration($updatedRecord['time_in'], $updatedRecord['time_out']);
                $hoursData = Helper::calculateHours($updatedRecord['time_in'], $updatedRecord['time_out'], 8);

                $this->attendanceModel->updateHours($record['attendance_id'], $hoursData);

                $employee = $this->employeeModel->getById($employee_no);

                $this->auditLog->log(
                    'TIME_OUT_SUCCESS',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['duration' => $duration, 'hours' => $hoursData],
                    'SUCCESS'
                );

                return [
                    'success' => true,
                    'message' => 'Time Out recorded successfully at ' . Helper::formatTime($updatedRecord['time_out']),
                    'employee_name' => $employee['full_name'],
                    'time_out' => $updatedRecord['time_out'],
                    'duration' => $duration,
                    'total_hours' => $hoursData['total_hours'],
                    'regular_hours' => $hoursData['regular_hours'],
                    'overtime_hours' => $hoursData['overtime_hours'],
                    header("Location: index.php?url=dashboard")
                ];
            } else {
                $this->auditLog->log(
                    'TIME_OUT_FAILED',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['reason' => 'Database error'],
                    'FAILED',
                    'Failed to update time out'
                );
                return [
                    'success' => false,
                    'message' => 'Failed to record time out. Please try again.'
                ];
            }
        } catch (Exception $e) {
            error_log("TimeOut Error: " . $e->getMessage());
            $this->auditLog->log(
                'TIME_OUT_ERROR',
                $user_id,
                $employee_no,
                null,
                ['error' => $e->getMessage()],
                'FAILED',
                $e->getMessage()
            );
            return [
                'success' => false,
                'message' => 'An error occurred. Please contact HR.'
            ];
        }




        var_dump($record);
        die;





        try {
            // Update time out
            if ($this->attendanceModel->timeOut($record['attendance_id'])) {
                // Get updated record
                $updatedRecord = $this->attendanceModel->getTodayAttendance($employee_no);

                // Calculate duration and hours
                $duration = Helper::calculateDuration($updatedRecord['time_in'], $updatedRecord['time_out']);
                $hoursData = Helper::calculateHours($updatedRecord['time_in'], $updatedRecord['time_out'], 8);

                // Update attendance record with hours data
                $this->attendanceModel->updateHours($record['attendance_id'], $hoursData);

                // Get employee name
                $employee = $this->employeeModel->getById($employee_no);

                // Log success
                $this->auditLog->log(
                    'TIME_OUT_SUCCESS',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['duration' => $duration, 'hours' => $hoursData],
                    'SUCCESS'
                );

                return [
                    'success' => true,
                    'message' => 'Time Out recorded successfully at ' . Helper::formatTime($updatedRecord['time_out']),
                    'employee_name' => $employee['full_name'],
                    'time_out' => $updatedRecord['time_out'],
                    'duration' => $duration,
                    'total_hours' => $hoursData['total_hours'],
                    'regular_hours' => $hoursData['regular_hours'],
                    'overtime_hours' => $hoursData['overtime_hours']
                ];
            } else {
                $this->auditLog->log(
                    'TIME_OUT_FAILED',
                    $user_id,
                    $employee_no,
                    $record['attendance_id'],
                    ['reason' => 'Database error'],
                    'FAILED',
                    'Failed to update time out'
                );
                return [
                    'success' => false,
                    'message' => 'Failed to record time out. Please try again.'
                ];
            }
        } catch (Exception $e) {
            error_log("TimeOut Error: " . $e->getMessage());
            $this->auditLog->log(
                'TIME_OUT_ERROR',
                $user_id,
                $employee_no,
                null,
                ['error' => $e->getMessage()],
                'FAILED',
                $e->getMessage()
            );
            return [
                'success' => false,
                'message' => 'An error occurred. Please contact HR.'
            ];
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
