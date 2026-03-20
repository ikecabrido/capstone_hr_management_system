<?php
/**
 * Attendance Model for Time & Attendance System
 * Handles all attendance-related database operations
 */

require_once __DIR__ . '/../config/Database.php';

class Attendance
{
    private $conn;
    private $table = "attendance";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
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
     */
    public function timeIn($employee_id, $method)
    {
        $query = "INSERT INTO $this->table 
                  (employee_id, time_in, attendance_date, recorded_by)
                  VALUES (:employee_id, NOW(), CURDATE(), :method)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':method', $method);

        return $stmt->execute();
    }

    /**
     * Record Time Out
     */
    public function timeOut($attendance_id)
    {
        $query = "UPDATE $this->table 
                  SET time_out = NOW()
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id);

        return $stmt->execute();
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
     */
    public function getTodaySummary()
    {
        $query = "SELECT 
                    COUNT(*) as total_records,
                    SUM(CASE WHEN time_in IS NOT NULL THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN time_in IS NULL THEN 1 ELSE 0 END) as absent_count
                  FROM $this->table
                  WHERE attendance_date = CURDATE()";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get attendance status for all employees today
     */
    public function getTodayAllEmployees($limit = 100, $offset = 0)
    {
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $query = "SELECT is_working_day FROM holidays 
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
                  FROM holidays 
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
                  FROM holidays 
                  WHERE year = :year 
                  ORDER BY holiday_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
