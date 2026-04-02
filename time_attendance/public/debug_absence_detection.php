<?php
/**
 * Debug Script for Absence & Late Detection
 * Check if auto-detection is working and why table shows no records
 */

require_once "../app/config/Database.php";
require_once "../app/models/AbsenceLateMgmt.php";

$database = new Database();
$conn = $database->getConnection();

$today = date('Y-m-d');
$startDate = $today;
$endDate = $today;

echo "<h2>Debugging Absence Detection - $today</h2>\n";
echo "<hr>\n";

// 1. Check if today is a holiday
echo "<h3>1. Check if Today is a Holiday</h3>\n";
$query = "SELECT * FROM ta_holidays WHERE holiday_date = :today";
$stmt = $conn->prepare($query);
$stmt->bindParam(':today', $today);
$stmt->execute();
$holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($holidays) > 0) {
    echo "<p style='color: orange;'><strong>⚠️ TODAY IS A HOLIDAY!</strong></p>\n";
    foreach ($holidays as $h) {
        echo "Holiday: " . $h['holiday_name'] . " (Type: " . $h['holiday_type'] . ")\n<br>";
    }
} else {
    echo "<p style='color: green;'>✓ Today is NOT a holiday</p>\n";
}

echo "<hr>\n";

// 2. Check active employees count
echo "<h3>2. Active Employees</h3>\n";
$query = "SELECT COUNT(*) as total FROM employees WHERE employment_status = 'Active'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<p>Total Active Employees: <strong>" . $result['total'] . "</strong></p>\n";

echo "<hr>\n";

// 3. Check employees with approved leave today
echo "<h3>3. Employees on Approved Leave Today</h3>\n";
$query = "SELECT e.employee_id, e.full_name, lr.leave_type_id, lr.reason 
          FROM employees e
          JOIN leave_requests lr ON e.employee_id = lr.employee_id
          WHERE :today BETWEEN lr.start_date AND lr.end_date 
          AND lr.approval_status = 'APPROVED'";
$stmt = $conn->prepare($query);
$stmt->bindParam(':today', $today);
$stmt->execute();
$onLeave = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($onLeave) > 0) {
    echo "<p>Employees on Approved Leave Today: <strong>" . count($onLeave) . "</strong></p>\n";
    foreach ($onLeave as $emp) {
        echo "- " . $emp['full_name'] . "\n<br>";
    }
} else {
    echo "<p style='color: blue;'>No employees on approved leave today</p>\n";
}

echo "<hr>\n";

// 4. Check employees with shift assignment
echo "<h3>4. Employees with Shift Assignment Today</h3>\n";
$query = "SELECT e.employee_id, e.full_name, s.shift_name, s.start_time, s.end_time
          FROM employees e
          JOIN ta_employee_shifts es ON e.employee_id = es.employee_id
          JOIN ta_shifts s ON es.shift_id = s.shift_id
          WHERE es.effective_from <= :today 
          AND (es.effective_to IS NULL OR es.effective_to >= :today)
          AND es.is_active = 1
          AND e.employment_status = 'Active'";
$stmt = $conn->prepare($query);
$stmt->bindParam(':today', $today);
$stmt->execute();
$withShift = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Employees with Shift Assignment: <strong>" . count($withShift) . "</strong></p>\n";
if (count($withShift) > 0) {
    foreach ($withShift as $emp) {
        echo "- " . $emp['full_name'] . " | Shift: " . $emp['shift_name'] . " (" . $emp['start_time'] . " - " . $emp['end_time'] . ")\n<br>";
    }
} else {
    echo "<p style='color: red;'>⚠️ No employees with shift assignment found!</p>\n";
}

echo "<hr>\n";

// 5. Check attendance records
echo "<h3>5. Attendance Records Today</h3>\n";
$query = "SELECT a.attendance_id, e.full_name, a.time_in, a.time_out
          FROM ta_attendance a
          JOIN employees e ON a.employee_id = e.employee_id
          WHERE CAST(a.time_in AS DATE) = :today";
$stmt = $conn->prepare($query);
$stmt->bindParam(':today', $today);
$stmt->execute();
$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Total Attendance Records: <strong>" . count($attendance) . "</strong></p>\n";
if (count($attendance) > 0) {
    foreach ($attendance as $att) {
        echo "- " . $att['full_name'] . " | Time In: " . $att['time_in'] . " | Time Out: " . $att['time_out'] . "\n<br>";
    }
} else {
    echo "<p style='color: red;'>No attendance records found today</p>\n";
}

echo "<hr>\n";

// 6. Test the detection method
echo "<h3>6. Running detectAbsentAndLateEmployees()</h3>\n";
$absenceLateMgmt = new AbsenceLateMgmt();
$results = $absenceLateMgmt->detectAbsentAndLateEmployees($today, $today);

echo "<p>Detection Results: <strong>" . count($results) . " records</strong></p>\n";
if (count($results) > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>\n";
    echo "<tr style='background: #003d82; color: white;'><th>Employee</th><th>Department</th><th>Date</th><th>Status</th><th>Time In</th><th>Start Time</th><th>Minutes Late</th></tr>\n";
    foreach ($results as $r) {
        $statusColor = '';
        switch ($r['status']) {
            case 'ABSENT': $statusColor = 'background: #ffebee;'; break;
            case 'LATE': $statusColor = 'background: #fff3e0;'; break;
            case 'ON_TIME': $statusColor = 'background: #e8f5e9;'; break;
        }
        echo "<tr><td>" . $r['full_name'] . "</td><td>" . $r['department'] . "</td><td>" . $r['check_date'] . "</td><td style='" . $statusColor . "'>" . $r['status'] . "</td><td>" . ($r['time_in'] ? $r['time_in'] : 'N/A') . "</td><td>" . ($r['start_time'] ? $r['start_time'] : 'N/A') . "</td><td>" . ($r['minutes_late'] ?? '-') . "</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<p style='color: red;'><strong>⚠️ No detection results - something is wrong!</strong></p>\n";
}

echo "<hr>\n";

// 7. Check SQL query manually
echo "<h3>7. Raw SQL Query Debug</h3>\n";
$date = $today;
$query = "
    SELECT 
        e.employee_id,
        e.full_name,
        e.department,
        CAST(:date AS DATE) as check_date,
        CASE 
            WHEN a.attendance_id IS NULL THEN 'ABSENT'
            WHEN TIME(a.time_in) > TIME(s.start_time) THEN 'LATE'
            ELSE 'ON_TIME'
        END as status,
        a.time_in,
        a.time_out,
        s.start_time,
        s.end_time,
        CASE 
            WHEN a.attendance_id IS NULL THEN NULL
            ELSE TIMESTAMPDIFF(MINUTE, s.start_time, TIME(a.time_in))
        END as minutes_late,
        CASE 
            WHEN lr.leave_request_id IS NOT NULL THEN 'APPROVED_LEAVE'
            WHEN r.is_excused = 1 THEN r.excuse_type
            ELSE NULL
        END as excuse_type,
        r.reason as excuse_reason,
        r.excuse_status
    FROM employees e
    LEFT JOIN ta_shifts s ON e.employee_id = s.employee_id 
        AND s.effective_from <= :date 
        AND (s.effective_to IS NULL OR s.effective_to >= :date)
        AND s.is_active = 1
    LEFT JOIN ta_employee_shifts es ON e.employee_id = es.employee_id 
        AND es.effective_from <= :date 
        AND (es.effective_to IS NULL OR es.effective_to >= :date)
        AND es.is_active = 1
    LEFT JOIN ta_shifts s2 ON es.shift_id = s2.shift_id
    LEFT JOIN ta_attendance a ON e.employee_id = a.employee_id 
        AND CAST(a.time_in AS DATE) = :date
    LEFT JOIN ta_absence_late_records r ON e.employee_id = r.employee_id 
        AND CAST(r.absence_date AS DATE) = :date
    LEFT JOIN leave_requests lr ON e.employee_id = lr.employee_id 
        AND :date BETWEEN lr.start_date AND lr.end_date 
        AND lr.approval_status = 'APPROVED'
    WHERE e.employment_status = 'Active'
        AND e.employee_id NOT IN (
            SELECT DISTINCT employee_id FROM leave_requests 
            WHERE :date BETWEEN start_date AND end_date 
            AND approval_status = 'APPROVED'
        )
    ORDER BY e.full_name ASC
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':date', $date);
$stmt->execute();
$debugResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Raw SQL Results: <strong>" . count($debugResults) . " records</strong></p>\n";
echo "<p><small>If this is 0, the query has an issue. If it's greater than 0, the detection should work.</small></p>\n";

?>
