<?php
/**
 * Attendance Model for Time & Attendance System
 * Handles all attendance-related database operations
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/ShiftValidator.php';

class Attendance
{
    private $conn;
    private $table = "ta_attendance";
    private $shiftValidator;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->shiftValidator = new ShiftValidator();
    }

    /**
     * Get today's attendance record for an employee
     */
    public function getTodayAttendance($employee_id)
    {
        $query = "SELECT * FROM $this->table 
                  WHERE employee_id = :employee_id 
                  AND attendance_date = CURDATE() 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Record Time In
     * VALIDATION: Check if employee has shift assigned before allowing time in
     * FIXED: Check if record exists for today, update if yes, insert if no (prevents duplicates)
     */
    public function timeIn($employee_id, $method)
    {
        // Validate shift assignment FIRST
        $shiftAssignment = $this->shiftValidator->hasShiftAssignedToday($employee_id);
        
        if (!$shiftAssignment) {
            return [
                'success' => false,
                'message' => 'No shift assigned for today. Please contact HR to assign a shift before clocking in.'
            ];
        }

        // First, check if a record already exists for today
        $check_query = "SELECT attendance_id FROM {$this->table} 
                       WHERE employee_id = :employee_id 
                       AND DATE(attendance_date) = CURDATE() 
                       LIMIT 1";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':employee_id', $employee_id);
        $check_stmt->execute();
        $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Record exists, update the time_in
            $query = "UPDATE {$this->table} 
                     SET time_in = NOW(),
                         recorded_by = :method,
                         status = 'PENDING_APPROVAL',
                         is_approved = 0,
                         approved_by = NULL,
                         updated_at = NOW()
                     WHERE attendance_id = :attendance_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':attendance_id', $existing['attendance_id']);
            $stmt->bindParam(':method', $method);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Time in recorded successfully',
                    'attendance_id' => $existing['attendance_id'],
                    'time_in' => date('Y-m-d H:i:s')
                ];
            }
            return ['success' => false, 'message' => 'Failed to record time in'];
        } else {
            // No record exists, insert new one
            $query = "INSERT INTO {$this->table} 
                     (employee_id, time_in, attendance_date, recorded_by, status)
                     VALUES (:employee_id, NOW(), CURDATE(), :method, 'PENDING_APPROVAL')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':method', $method);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Time in recorded successfully',
                    'attendance_id' => $this->conn->lastInsertId(),
                    'time_in' => date('Y-m-d H:i:s')
                ];
            }
            return ['success' => false, 'message' => 'Failed to record time in'];
        }
    }

    /**
     * Record Time Out
     * VALIDATION: Check if employee has shift assigned before allowing time out
     */
    public function timeOut($employee_id, $attendance_id = null)
    {
        // Validate shift assignment
        $shiftAssignment = $this->shiftValidator->hasShiftAssignedToday($employee_id);
        
        if (!$shiftAssignment) {
            return [
                'success' => false,
                'message' => 'No shift assigned for today. Cannot record time out without an assigned shift.'
            ];
        }

        // If no attendance_id provided, get today's attendance record
        if (!$attendance_id) {
            $check_query = "SELECT attendance_id FROM {$this->table} 
                           WHERE employee_id = :employee_id 
                           AND DATE(attendance_date) = CURDATE() 
                           LIMIT 1";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':employee_id', $employee_id);
            $check_stmt->execute();
            $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'No time in record found for today. Please clock in first.'
                ];
            }
            $attendance_id = $result['attendance_id'];
        }

        $query = "UPDATE $this->table 
                  SET time_out = NOW(),
                      updated_at = NOW()
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id);

        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Time out recorded successfully',
                'attendance_id' => $attendance_id,
                'time_out' => date('Y-m-d H:i:s')
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to record time out'
        ];
    }

    /**
     * Get attendance records for date range
     */
    public function getByDateRange($start_date, $end_date, $employee_id = null, $limit = 500, $offset = 0)
    {
        $query = "SELECT a.*, e.full_name, e.department, e.position
                  FROM $this->table a
                  JOIN employees e ON a.employee_id = e.employee_id
                  WHERE a.attendance_date BETWEEN :start_date AND :end_date";

        if (!is_null($employee_id)) {
            $query .= " AND a.employee_id = :employee_id";
        }

        $query .= " ORDER BY a.attendance_date DESC, a.created_at DESC
                   LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);

        if (!is_null($employee_id)) {
            $stmt->bindParam(':employee_id', $employee_id);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get today's attendance summary (for dashboard)
     * FIXED: Exclude holidays from absent count
     */
    public function getTodaySummary()
    {
        // Check if today is a holiday
        require_once __DIR__ . '/../helpers/HolidayHelper.php';
        require_once __DIR__ . '/../models/Holiday.php';
        
        $isHolidayToday = false;
        try {
            \App\Helpers\HolidayHelper::init($this->conn);
            $isHolidayToday = \App\Helpers\HolidayHelper::isHoliday(date('Y-m-d'));
        } catch (Exception $e) {
            // If holiday check fails, continue without it
            $isHolidayToday = false;
        }

        $query = "SELECT 
                    COUNT(*) as total_records,
                    SUM(CASE WHEN time_in IS NOT NULL THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN time_in IS NULL THEN 1 ELSE 0 END) as absent_count
                  FROM $this->table
                  WHERE attendance_date = CURDATE()";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If today is a holiday, count all absences as holidays instead
        if ($isHolidayToday) {
            // Get active employee count for context
            $empQuery = "SELECT COUNT(*) as active_count FROM employees WHERE employment_status = 'Active'";
            $empStmt = $this->conn->prepare($empQuery);
            $empStmt->execute();
            $empResult = $empStmt->fetch(PDO::FETCH_ASSOC);
            
            $result['holiday_count'] = $empResult['active_count'];
            $result['absent_count'] = 0;
            $result['is_holiday'] = true;
        } else {
            $result['holiday_count'] = 0;
            $result['is_holiday'] = false;
        }

        return $result;
    }

    /**
     * Get attendance status for all employees today
     * FIXED: Check if today is a holiday and include holiday status
     */
    public function getTodayAllEmployees($limit = 100, $offset = 0)
    {
        // Check if today is a holiday
        require_once __DIR__ . '/../helpers/HolidayHelper.php';
        require_once __DIR__ . '/../models/Holiday.php';
        
        $isHolidayToday = false;
        $holidayInfo = null;
        try {
            \App\Helpers\HolidayHelper::init($this->conn);
            $isHolidayToday = \App\Helpers\HolidayHelper::isHoliday(date('Y-m-d'));
            if ($isHolidayToday) {
                $holidayInfo = \App\Helpers\HolidayHelper::getHolidayByDate(date('Y-m-d'));
            }
        } catch (Exception $e) {
            // If holiday check fails, continue without it
            $isHolidayToday = false;
        }

        $query = "SELECT a.*, e.full_name, e.department, e.position
                  FROM $this->table a
                  RIGHT JOIN employees e ON a.employee_id = e.employee_id 
                    AND a.attendance_date = CURDATE()
                  WHERE e.employment_status = 'Active'
                  ORDER BY e.full_name
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add holiday information to each record if today is a holiday
        if ($isHolidayToday) {
            foreach ($records as &$record) {
                $record['is_holiday_today'] = true;
                $record['holiday_info'] = $holidayInfo;
                // If no attendance record exists and today is a holiday, mark with holiday flag
                if ($record['time_in'] === null && $record['status'] === null) {
                    $record['status'] = 'HOLIDAY';
                    $record['notes'] = 'Holiday - ' . ($holidayInfo['name'] ?? 'Public Holiday');
                }
            }
        }

        return $records;
    }

    /**
     * Get attendance history for a specific employee
     */
    public function getEmployeeHistory($employee_id, $limit = 30, $offset = 0)
    {
        $query = "SELECT * FROM $this->table
                  WHERE employee_id = :employee_id
                  ORDER BY attendance_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals($limit = 50, $offset = 0)
    {
        $query = "SELECT a.*, e.full_name, e.department
                  FROM $this->table a
                  JOIN employees e ON a.employee_id = e.employee_id
                  WHERE a.is_approved = 0
                  ORDER BY a.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Approve attendance record
     */
    public function approve($attendance_id, $approved_by, $remarks = "")
    {
        $query = "UPDATE $this->table 
                  SET is_approved = 1, 
                      approved_by = :approved_by,
                      approval_remarks = :remarks,
                      approved_at = NOW()
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id);
        $stmt->bindParam(':approved_by', $approved_by);
        $stmt->bindParam(':remarks', $remarks);

        return $stmt->execute();
    }

    /**
     * Update attendance status
     */
    public function updateStatus($attendance_id, $status)
    {
        $query = "UPDATE $this->table 
                  SET status = :status
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    /**
     * Update attendance record with hours data
     */
    public function updateHours($attendance_id, $hoursData)
    {
        $query = "UPDATE $this->table 
                  SET total_hours_worked = :total_hours,
                      regular_hours = :regular_hours,
                      overtime_hours = :overtime_hours
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id);
        $stmt->bindParam(':total_hours', $hoursData['total_hours']);
        $stmt->bindParam(':regular_hours', $hoursData['regular_hours']);
        $stmt->bindParam(':overtime_hours', $hoursData['overtime_hours']);

        return $stmt->execute();
    }

    /**
     * Check if a date is a holiday
     */
    public function isHoliday($date)
    {
        $query = "SELECT is_working_day FROM ta_holidays 
                  WHERE holiday_date = :date 
                  AND year = YEAR(:date) 
                  AND is_working_day = 0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * Get holiday information for a date
     */
    public function getHolidayInfo($date)
    {
        $query = "SELECT holiday_id, holiday_name, description, is_working_day 
                  FROM ta_holidays 
                  WHERE holiday_date = :date 
                  AND year = YEAR(:date)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all holidays for a year
     */
    public function getHolidaysByYear($year = null)
    {
        $year = $year ?: date('Y');
        
        $query = "SELECT holiday_date, holiday_name, description, is_working_day 
                  FROM ta_holidays 
                  WHERE year = :year 
                  ORDER BY holiday_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
