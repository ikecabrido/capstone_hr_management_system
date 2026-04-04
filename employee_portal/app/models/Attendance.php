<?php
require_once __DIR__ . '/../config/Database.php';

class Attendance
{
    private $conn;
    private $table = "ta_attendance";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getTodayAttendance($employee_id)
    {
        $query = "SELECT * FROM $this->table 
                  WHERE employee_no = :employee_id 
                  AND attendance_date = CURDATE() 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function timeIn($employee_id, $method)
    {
        $query = "INSERT INTO $this->table 
                  (employee_no, time_in, attendance_date, recorded_by)
                  VALUES (:employee_no, NOW(), CURDATE(), :method)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_id);
        $stmt->bindParam(':method', $method);

        return $stmt->execute();
    }

    public function timeOut($attendance_id)
    {
        $query = "UPDATE $this->table 
                  SET time_out = NOW()
                  WHERE attendance_id = :attendance_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attendance_id', $attendance_id);

        return $stmt->execute();
    }

    public function getByDateRange($start_date, $end_date, $employee_no = null, $limit = 500, $offset = 0)
    {
        $query = "SELECT a.*, e.full_name, e.department, e.position
                  FROM $this->table a
                  JOIN employees e ON a.employee_no = e.employee_no
                  WHERE a.attendance_date BETWEEN :start_date AND :end_date";

        if (!is_null($employee_no)) {
            $query .= " AND a.employee_no = :employee_no";
        }

        $query .= " ORDER BY a.attendance_date DESC, a.created_at DESC
                   LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);

        if (!is_null($employee_no)) {
            $stmt->bindParam(':employee_no', $employee_no);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

    public function getTodayAllEmployees($limit = 100, $offset = 0)
    {
        $query = "SELECT a.*, e.full_name, e.department, e.position
                  FROM $this->table a
                  RIGHT JOIN employees e ON a.employee_no = e.employee_no 
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

    public function getEmployeeHistory($employee_no, $limit = 30, $offset = 0)
    {
        $query = "SELECT * FROM $this->table
                  WHERE employee_no = :employee_no
                  ORDER BY attendance_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingApprovals($limit = 50, $offset = 0)
    {
        $query = "SELECT a.*, e.full_name, e.department
                  FROM $this->table a
                  JOIN employees e ON a.employee_no = e.employee_no
                  WHERE a.is_approved = 0
                  ORDER BY a.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

    public function isHoliday($date)
    {
        $query = "SELECT id FROM ta_holidays 
              WHERE is_active = 1
              AND (
                    holiday_date = :date
                    OR (is_recurring = 1 AND DATE_FORMAT(holiday_date, '%m-%d') = DATE_FORMAT(:date, '%m-%d'))
                  )
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function getHolidayInfo($date)
    {
        $query = "SELECT id, name, description, category, is_recurring
              FROM ta_holidays 
              WHERE is_active = 1
              AND (
                    holiday_date = :date
                    OR (is_recurring = 1 AND DATE_FORMAT(holiday_date, '%m-%d') = DATE_FORMAT(:date, '%m-%d'))
                  )
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getHolidaysByYear($year = null)
    {
        $year = $year ?: date('Y');

        $query = "SELECT id, name, holiday_date, description, category, is_recurring
              FROM ta_holidays 
              WHERE is_active = 1
              AND YEAR(holiday_date) = :year
              ORDER BY holiday_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyAttendance($employee_id, $month = null, $year = null)
    {
        $month = $month ?? date('m'); 
        $year = $year ?? date('Y');  

        $query = "SELECT *,
                     TIME_TO_SEC(TIMEDIFF(time_out, time_in)) / 3600 AS total_hours_worked
              FROM ta_attendance
              WHERE employee_no = :employee_id
                AND MONTH(attendance_date) = :month
                AND YEAR(attendance_date) = :year
              ORDER BY attendance_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':employee_id' => $employee_id,
            ':month' => $month,
            ':year' => $year
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
