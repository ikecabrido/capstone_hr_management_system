<?php
/**
 * Enhanced Absence & Late Detection Cron Trigger
 * This should be called daily to detect absences and late arrivals
 * Can be triggered via: /time_attendance/app/cron/detect_absences_and_late.php
 */

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$baseDir = dirname(__FILE__);
require_once $baseDir . '/../../auth/database.php';
require_once $baseDir . '/../models/Attendance.php';
require_once $baseDir . '/../services/AttendanceValidationService.php';
require_once $baseDir . '/../helpers/HolidayHelper.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$attendanceModel = new Attendance();
$validationService = new \App\Services\AttendanceValidationService();

$results = [
    'success' => false,
    'message' => '',
    'absences' => [],
    'late_arrivals' => [],
    'waiting_for_shift' => [],
    'weekend_employees' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    $date = $_GET['date'] ?? date('Y-m-d');
    $dayOfWeek = date('w', strtotime($date)); // 0=Sunday, 6=Saturday

    // STEP 1: Check if today is a holiday
    $holiday_query = "SELECT id, name FROM {$conn->query("SELECT 'ta_holidays' as table_name")->fetch()['table_name']} 
                      WHERE holiday_date = :date AND is_active = 1 LIMIT 1";
    $holiday_check = $conn->prepare("SELECT id, name FROM ta_holidays WHERE holiday_date = :date AND is_active = 1 LIMIT 1");
    $holiday_check->bindParam(':date', $date);
    $holiday_check->execute();
    $is_holiday = $holiday_check->rowCount() > 0;
    $holiday_info = $holiday_check->fetch(\PDO::FETCH_ASSOC);

    // STEP 2: Detect Holiday Absences (if it's a holiday)
    if ($is_holiday) {
        $query = "SELECT DISTINCT
                    e.employee_id,
                    e.full_name,
                    e.department
                  FROM employees e
                  JOIN ta_employee_shifts es ON e.employee_id = es.employee_id
                  WHERE es.is_active = 1
                  AND es.effective_from <= :date
                  AND (es.effective_to IS NULL OR es.effective_to >= :date)
                  AND e.employment_status = 'Active'
                  AND e.employee_id NOT IN (
                    SELECT DISTINCT employee_id FROM ta_attendance
                    WHERE attendance_date = :date
                    AND time_in IS NOT NULL
                  )";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        $holiday_absentees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($holiday_absentees as $employee) {
            $created = $attendanceModel->markAbsent($employee['employee_id'], $date, 'Holiday absence detected by cron');
            if ($created) {
                $results['holiday_absences'][] = [
                    'employee_id' => $employee['employee_id'],
                    'name' => $employee['full_name'],
                    'department' => $employee['department'],
                    'holiday_name' => $holiday_info['name'],
                    'record_id' => null
                ];
            }
        }
    }

    // STEP 3: Detect Absences (employees with no time-in on regular working days)
    $absences = $validationService->detectAbsences($date);
    foreach ($absences as $absence) {
        $created = $attendanceModel->markAbsent($absence['employee_id'], $date, 'Absence detected by cron');
        if ($created) {
            $results['absences'][] = [
                'employee_id' => $absence['employee_id'],
                'name' => $absence['full_name'],
                'department' => $absence['department'],
                'shift_start' => $absence['start_time'],
                'shift_end' => $absence['end_time']
            ];
        }
    }

    // STEP 4: Detect Late Arrivals
    $late_arrivals = $validationService->detectLateArrivals($date);
    foreach ($late_arrivals as $late) {
        $attendanceModel->updateStatus($late['attendance_id'], 'LATE', $late['minutes_late']);
        $results['late_arrivals'][] = [
            'employee_id' => $late['employee_id'],
            'name' => $late['name'],
            'department' => $late['department'],
            'time_in' => $late['time_in'],
            'shift_start' => $late['shift_start'],
            'minutes_late' => $late['minutes_late']
        ];
    }

    // STEP 3: Detect Employees Waiting for Shift Assignment
    $query = "SELECT DISTINCT
                e.employee_id,
                e.full_name,
                e.department
              FROM employees e
              WHERE e.employment_status = 'Active'
              AND e.employee_id NOT IN (
                SELECT DISTINCT employee_id FROM ta_employee_shifts
                WHERE is_active = 1
                AND effective_from <= :date
                AND (effective_to IS NULL OR effective_to >= :date)
              )";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    
    $waiting_employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($waiting_employees as $employee) {
        $results['waiting_for_shift'][] = [
            'employee_id' => $employee['employee_id'],
            'name' => $employee['full_name'],
            'department' => $employee['department'],
            'reason' => 'No shift assigned'
        ];
    }

    // STEP 4: Mark Weekend Attendance
    if ($dayOfWeek == 0) { // Sunday
        $query = "UPDATE ta_attendance SET status = 'WEEKEND' 
                  WHERE attendance_date = :date AND status IS NULL";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        $results['weekend_employees'][] = [
            'date' => $date,
            'message' => 'Sunday (Weekend) - No attendance expected'
        ];
    }

    $results['success'] = true;
    $results['message'] = 'Absence and late detection completed successfully';
    $results['summary'] = [
        'absences_detected' => count($results['absences']),
        'late_arrivals_detected' => count($results['late_arrivals']),
        'waiting_for_shift_count' => count($results['waiting_for_shift']),
        'date_checked' => $date
    ];

} catch (Exception $e) {
    $results['success'] = false;
    $results['message'] = 'Error: ' . $e->getMessage();
    $results['error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
