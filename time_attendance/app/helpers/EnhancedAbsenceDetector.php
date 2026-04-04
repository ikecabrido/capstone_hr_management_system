<?php
/**
 * Enhanced Absence & Late Detector
 * Automatically detects employee absences and late arrivals
 * Marks attendance status in real-time
 */

namespace App\Helpers;

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/HolidayHelper.php';
require_once __DIR__ . '/../models/UnexpectedHoliday.php';

class EnhancedAbsenceDetector
{
    private $conn;
    private $attendance_table = "ta_attendance";
    private $employees_table = "employees";
    private $shifts_table = "ta_shifts";
    private $shift_assignments_table = "ta_shift_assignments";
    private $absence_late_table = "ta_absence_late_records";
    private $late_threshold_minutes = 30; // 30 minutes late threshold
    private $unexpectedHolidayModel;

    public function __construct()
    {
        $db = new \Database();
        $this->conn = $db->getConnection();
        $this->unexpectedHolidayModel = new \UnexpectedHoliday();
    }

    /**
     * Detect and mark LATE arrivals for today
     * Run this multiple times a day (every hour recommended)
     */
    public function detectAndMarkLateToday()
    {
        $today = date('Y-m-d');
        
        // Check if today is a holiday or weekend
        if (!$this->isWorkingDay($today)) {
            return ['status' => 'skipped', 'reason' => 'Holiday or weekend'];
        }

        // Find all employees who checked in late today
        $query = "SELECT 
                    a.attendance_id,
                    a.employee_id,
                    a.time_in,
                    a.status,
                    e.full_name,
                    e.department,
                    s.shift_id,
                    s.start_time
                  FROM {$this->attendance_table} a
                  JOIN {$this->employees_table} e ON a.employee_id = e.employee_id
                  LEFT JOIN {$this->shift_assignments_table} sa ON a.employee_id = sa.employee_id
                    AND sa.effective_from <= :today
                    AND (sa.effective_to IS NULL OR sa.effective_to >= :today)
                  LEFT JOIN {$this->shifts_table} s ON sa.shift_id = s.shift_id
                  WHERE a.attendance_date = :today
                  AND a.time_in IS NOT NULL
                  AND a.status NOT IN ('LATE', 'HOLIDAY', 'LEAVE')
                  AND s.shift_id IS NOT NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $results = [];
        foreach ($records as $record) {
            $minutesLate = $this->getMinutesLate($record['time_in'], $record['start_time']);

            // If 30 minutes or more late, mark as LATE
            if ($minutesLate >= $this->late_threshold_minutes) {
                $this->updateAttendanceStatus(
                    $record['attendance_id'],
                    'LATE',
                    "Auto-detected late arrival ({$minutesLate} minutes late)"
                );

                $results[] = [
                    'employee_id' => $record['employee_id'],
                    'name' => $record['full_name'],
                    'department' => $record['department'],
                    'minutes_late' => $minutesLate,
                    'action' => 'Marked as LATE'
                ];
            } elseif ($minutesLate > 0 && $minutesLate < $this->late_threshold_minutes) {
                // Less than 30 minutes late, mark as PRESENT but with notes
                $this->updateAttendanceStatus(
                    $record['attendance_id'],
                    'PRESENT',
                    "Arrived {$minutesLate} minutes late (within tolerance)"
                );
            }
        }

        return [
            'status' => 'completed',
            'date' => $today,
            'late_count' => count($results),
            'results' => $results
        ];
    }

    /**
     * Detect and mark ABSENCE for employees who haven't checked in by end of shift
     * Run this once daily at end of business day (e.g., 6 PM)
     */
    public function detectAndMarkAbsenceToday()
    {
        $today = date('Y-m-d');
        
        // Check if today is a holiday or weekend
        if (!$this->isWorkingDay($today)) {
            return ['status' => 'skipped', 'reason' => 'Holiday or weekend'];
        }

        // Get all active employees who should have worked today
        $query = "SELECT DISTINCT
                    e.employee_id,
                    e.full_name,
                    e.department,
                    s.shift_id,
                    s.end_time
                  FROM {$this->employees_table} e
                  LEFT JOIN {$this->shift_assignments_table} sa ON e.employee_id = sa.employee_id
                    AND sa.effective_from <= :today
                    AND (sa.effective_to IS NULL OR sa.effective_to >= :today)
                  LEFT JOIN {$this->shifts_table} s ON sa.shift_id = s.shift_id
                  WHERE e.employment_status = 'Active'
                  AND (sa.shift_id IS NOT NULL OR sa.shift_id IS NULL)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $results = [];
        foreach ($employees as $employee) {
            // Check if employee has attendance record for today
            $attendanceQuery = "SELECT attendance_id, status, time_in FROM {$this->attendance_table}
                               WHERE employee_id = :employee_id AND attendance_date = :today LIMIT 1";
            
            $attStmt = $this->conn->prepare($attendanceQuery);
            $attStmt->bindParam(':employee_id', $employee['employee_id'], \PDO::PARAM_INT);
            $attStmt->bindParam(':today', $today);
            $attStmt->execute();
            $attendance = $attStmt->fetch(\PDO::FETCH_ASSOC);

            // If no record and should have worked, create ABSENT record
            if (!$attendance) {
                // Create new absence record
                $this->createAbsenceRecord(
                    $employee['employee_id'],
                    $today,
                    'Auto-detected absence - No check-in by end of shift'
                );

                $results[] = [
                    'employee_id' => $employee['employee_id'],
                    'name' => $employee['full_name'],
                    'department' => $employee['department'],
                    'action' => 'Marked as ABSENT'
                ];
            }
        }

        return [
            'status' => 'completed',
            'date' => $today,
            'absence_count' => count($results),
            'results' => $results
        ];
    }

    /**
     * Update attendance status
     */
    private function updateAttendanceStatus($attendance_id, $status, $notes = '')
    {
        $query = "UPDATE {$this->attendance_table}
                  SET status = :status, notes = CONCAT(IFNULL(notes, ''), '\n', :notes)
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':attendance_id', $attendance_id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Create absence record
     */
    private function createAbsenceRecord($employee_id, $absence_date, $notes = '')
    {
        $insertQuery = "INSERT INTO {$this->attendance_table}
                        (employee_id, attendance_date, status, notes, created_at)
                        VALUES (:employee_id, :absence_date, 'ABSENT', :notes, NOW())";

        $insertStmt = $this->conn->prepare($insertQuery);
        $insertStmt->bindParam(':employee_id', $employee_id, \PDO::PARAM_INT);
        $insertStmt->bindParam(':absence_date', $absence_date);
        $insertStmt->bindParam(':notes', $notes);

        return $insertStmt->execute();
    }

    /**
     * Calculate minutes late
     */
    private function getMinutesLate($timeIn, $startTime)
    {
        if (!$timeIn || !$startTime) {
            return 0;
        }

        try {
            $inTime = new \DateTime($timeIn);
            $startDateTime = new \DateTime(date('Y-m-d') . ' ' . $startTime);

            if ($inTime <= $startDateTime) {
                return 0; // On time or early
            }

            $interval = $startDateTime->diff($inTime);
            return $interval->h * 60 + $interval->i;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Check if date is a working day (not weekend or holiday)
     */
    private function isWorkingDay($date)
    {
        // Check if weekend
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0 || $dayOfWeek == 6) { // Sunday or Saturday
            return false;
        }

        // Check if standard holiday
        try {
            HolidayHelper::init($this->conn);
            if (HolidayHelper::isHoliday($date)) {
                return false;
            }
        } catch (\Exception $e) {
            // If holiday check fails, assume it's a working day
        }

        // Check if unexpected/emergency holiday (municipal, state crisis, etc.)
        try {
            if ($this->unexpectedHolidayModel->isUnexpectedHoliday($date)) {
                return false; // Skip detection - it's an unexpected holiday
            }
        } catch (\Exception $e) {
            // If check fails, assume it's a working day
        }

        return true;
    }

    /**
     * Get today's attendance summary (for dashboard)
     * Includes auto-detected absences and late arrivals
     */
    public function getTodayAttendanceSummary()
    {
        $today = date('Y-m-d');
        
        $query = "SELECT 
                    COUNT(*) as total_employees,
                    SUM(CASE WHEN status = 'PRESENT' THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN status = 'LATE' THEN 1 ELSE 0 END) as late_count,
                    SUM(CASE WHEN status = 'ABSENT' THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN status = 'HOLIDAY' THEN 1 ELSE 0 END) as holiday_count,
                    SUM(CASE WHEN status = 'LEAVE' THEN 1 ELSE 0 END) as leave_count
                  FROM {$this->attendance_table} a
                  JOIN {$this->employees_table} e ON a.employee_id = e.employee_id
                  WHERE a.attendance_date = :today
                  AND e.employment_status = 'Active'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get today's all employees with status (for dashboard)
     */
    public function getTodayAllEmployeesWithStatus($limit = 100, $offset = 0)
    {
        $today = date('Y-m-d');

        $query = "SELECT 
                    COALESCE(a.attendance_id, 0) as attendance_id,
                    e.employee_id,
                    e.full_name,
                    e.department,
                    e.position,
                    COALESCE(a.time_in, '-') as time_in,
                    COALESCE(a.time_out, '-') as time_out,
                    COALESCE(a.status, 'ABSENT') as status,
                    COALESCE(a.notes, '') as notes,
                    a.attendance_date
                  FROM {$this->employees_table} e
                  LEFT JOIN {$this->attendance_table} a ON e.employee_id = a.employee_id 
                    AND a.attendance_date = :today
                  WHERE e.employment_status = 'Active'
                  ORDER BY e.full_name
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
