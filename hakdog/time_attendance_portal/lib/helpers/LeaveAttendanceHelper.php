<?php

namespace TimeAttendancePortal\Helpers;

use TimeAttendancePortal\Config\Database;
use PDO;

/**
 * Leave-Attendance Integration Helper
 * 
 * Handles:
 * - Checking approved leave for attendance dates
 * - Marking attendance as ON_LEAVE
 * - Deducting leave balances
 * - Processing daily leave records
 * - Preventing overlapping leaves
 */
class LeaveAttendanceHelper
{
    private $conn;
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Check if employee has approved leave on a specific date
     * 
     * @param int $employee_id
     * @param string $date (Y-m-d format)
     * @return array|null Leave request if exists, null otherwise
     */
    public function getApprovedLeaveForDate($employee_id, $date)
    {
        $query = "SELECT lr.*, lt.leave_type_name, lt.is_deductible
                  FROM ta_leave_requests lr
                  JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id
                  WHERE lr.employee_id = ?
                  AND lr.status = 'Approved'
                  AND ? BETWEEN lr.start_date AND lr.end_date
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if employee has overlapping approved leave
     * (Used to prevent conflicting leave requests)
     * 
     * @param int $employee_id
     * @param string $start_date (Y-m-d format)
     * @param string $end_date (Y-m-d format)
     * @param int $exclude_leave_id (Optional - to exclude current request)
     * @return bool True if overlap exists
     */
    public function hasOverlappingLeave($employee_id, $start_date, $end_date, $exclude_leave_id = null)
    {
        $query = "SELECT COUNT(*) as count FROM ta_leave_requests
                  WHERE employee_id = ?
                  AND status IN ('Approved', 'Pending')
                  AND start_date <= ?
                  AND end_date >= ?";

        $params = [$employee_id, $end_date, $start_date];

        if ($exclude_leave_id) {
            $query .= " AND leave_request_id != ?";
            $params[] = $exclude_leave_id;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Check if a specific date is a holiday
     * 
     * @param string $date (Y-m-d format)
     * @return bool
     */
    public function isHoliday($date)
    {
        $query = "SELECT COUNT(*) as count FROM ta_holidays 
                  WHERE holiday_date = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Get the day of week (to exclude weekends if applicable)
     * 
     * @param string $date (Y-m-d format)
     * @return int 0-6 (0=Sunday, 6=Saturday)
     */
    public function getDayOfWeek($date)
    {
        return (int) date('w', strtotime($date));
    }

    /**
     * Deduct leave balance for an approved leave request
     * Loops through each day of leave, skipping holidays
     * 
     * @param int $leave_request_id
     * @param int $employee_id
     * @param int $leave_type_id
     * @param string $start_date
     * @param string $end_date
     * @return bool Success/failure
     */
    public function deductLeaveBalance($leave_request_id, $employee_id, $leave_type_id, $start_date, $end_date)
    {
        try {
            $this->conn->beginTransaction();

            $days_to_deduct = 0;
            $current_date = new \DateTime($start_date);
            $end_date_obj = new \DateTime($end_date);
            $end_date_obj->modify('+1 day'); // Include end date

            // Loop through each day
            while ($current_date < $end_date_obj) {
                $date_str = $current_date->format('Y-m-d');
                
                // Skip if holiday
                if ($this->isHoliday($date_str)) {
                    $this->recordDailyLeave($leave_request_id, $employee_id, $leave_type_id, $date_str, true);
                    $current_date->modify('+1 day');
                    continue;
                }

                // Record daily leave
                $this->recordDailyLeave($leave_request_id, $employee_id, $leave_type_id, $date_str, false);
                $days_to_deduct++;
                $current_date->modify('+1 day');
            }

            // Deduct balance if there are days to deduct
            if ($days_to_deduct > 0) {
                $this->deductFromBalance($employee_id, $leave_type_id, $days_to_deduct);
            }

            // Mark balance as deducted in leave request
            $query = "UPDATE ta_leave_requests SET balance_deducted = 1 WHERE leave_request_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$leave_request_id]);

            $this->conn->commit();
            return true;

        } catch (\Exception $e) {
            $this->conn->rollBack();
            error_log("Error deducting leave balance: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Record a single day of leave in ta_leave_daily_records
     * 
     * @param int $leave_request_id
     * @param int $employee_id
     * @param int $leave_type_id
     * @param string $date (Y-m-d format)
     * @param bool $is_holiday
     * @return bool
     */
    private function recordDailyLeave($leave_request_id, $employee_id, $leave_type_id, $date, $is_holiday = false)
    {
        $query = "INSERT IGNORE INTO ta_leave_daily_records 
                  (leave_request_id, employee_id, leave_type_id, leave_date, is_holiday, balance_deducted)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $leave_request_id,
            $employee_id,
            $leave_type_id,
            $date,
            $is_holiday ? 1 : 0,
            !$is_holiday ? 1 : 0
        ]);
    }

    /**
     * Deduct from leave balance
     * 
     * @param int $employee_id
     * @param int $leave_type_id
     * @param int $days_to_deduct
     * @return bool
     */
    private function deductFromBalance($employee_id, $leave_type_id, $days_to_deduct)
    {
        $current_year = date('Y');
        
        $query = "UPDATE ta_leave_balances
                  SET used_balance = used_balance + ?,
                      remaining_balance = remaining_balance - ?
                  WHERE employee_id = ?
                  AND leave_type_id = ?
                  AND year = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$days_to_deduct, $days_to_deduct, $employee_id, $leave_type_id, $current_year]);
    }

    /**
     * Update attendance record to mark as ON_LEAVE
     * Called when processing attendance for a date with approved leave
     * 
     * @param int $attendance_id
     * @param int $leave_request_id
     * @return bool
     */
    public function markAttendanceAsLeave($attendance_id, $leave_request_id)
    {
        $query = "UPDATE ta_attendance
                  SET status = 'ON_LEAVE',
                      leave_request_id = ?
                  WHERE attendance_id = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$leave_request_id, $attendance_id]);
    }

    /**
     * Create or update absence record for excused leave
     * 
     * @param int $employee_id
     * @param string $absence_date (Y-m-d format)
     * @param int $leave_request_id
     * @return bool
     */
    public function recordLeaveAsExcusedAbsence($employee_id, $absence_date, $leave_request_id)
    {
        // Check if record already exists
        $check_query = "SELECT absence_id FROM ta_absence_late_records
                       WHERE employee_id = ? AND absence_date = ? AND leave_request_id = ?";
        $stmt = $this->conn->prepare($check_query);
        $stmt->execute([$employee_id, $absence_date, $leave_request_id]);

        if ($stmt->fetch()) {
            return true; // Already recorded
        }

        // Insert new record
        $query = "INSERT INTO ta_absence_late_records 
                  (employee_id, absence_date, type, is_excused, excuse_status, excuse_type, leave_request_id)
                  VALUES (?, ?, 'ABSENT', 1, 'APPROVED', 'APPROVED_LEAVE', ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$employee_id, $absence_date, $leave_request_id]);
    }

    /**
     * Get leave balance for an employee
     * 
     * @param int $employee_id
     * @param int $leave_type_id (Optional - if null, returns all leave types)
     * @param int $year (Optional - defaults to current year)
     * @return array
     */
    public function getLeaveBalance($employee_id, $leave_type_id = null, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT lb.*, lt.leave_type_name, lt.is_deductible
                  FROM ta_leave_balances lb
                  JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id
                  WHERE lb.employee_id = ?
                  AND lb.year = ?";

        $params = [$employee_id, $year];

        if ($leave_type_id) {
            $query .= " AND lb.leave_type_id = ?";
            $params[] = $leave_type_id;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if leave balance is sufficient
     * 
     * @param int $employee_id
     * @param int $leave_type_id
     * @param int $days_needed
     * @param int $year
     * @return bool
     */
    public function hasSufficientBalance($employee_id, $leave_type_id, $days_needed, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT remaining_balance FROM ta_leave_balances
                  WHERE employee_id = ?
                  AND leave_type_id = ?
                  AND year = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $leave_type_id, $year]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return $result['remaining_balance'] >= $days_needed;
    }

    /**
     * Calculate working days between two dates (excluding weekends/holidays)
     * 
     * @param string $start_date (Y-m-d format)
     * @param string $end_date (Y-m-d format)
     * @return int
     */
    public function calculateWorkingDays($start_date, $end_date)
    {
        $working_days = 0;
        $current_date = new \DateTime($start_date);
        $end_date_obj = new \DateTime($end_date);
        $end_date_obj->modify('+1 day');

        while ($current_date < $end_date_obj) {
            $date_str = $current_date->format('Y-m-d');
            
            // Skip weekends (0=Sunday, 6=Saturday)
            $day_of_week = (int) $current_date->format('w');
            if ($day_of_week != 0 && $day_of_week != 6) {
                // Skip holidays
                if (!$this->isHoliday($date_str)) {
                    $working_days++;
                }
            }

            $current_date->modify('+1 day');
        }

        return $working_days;
    }
}
