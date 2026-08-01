<?php
/**
 * Enhanced Absence & Late Detection Cron Trigger (Updated for Holidays)
 * This should be called daily to detect absences and late arrivals
 * NOW SUPPORTS: HOLIDAY_WORKED (double pay) and HOLIDAY_ABSENT tracking
 * Can be triggered via: /time_attendance/app/cron/detect_absences_and_late.php
 */

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../../auth/database.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../services/AttendanceValidationService.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$attendanceModel = new Attendance();
$validationService = new \App\Services\AttendanceValidationService();

$results = [
    'success' => false,
    'message' => '',
    'absences' => [],
    'late_arrivals' => [],
    'holiday_worked' => [],
    'holiday_absences' => [],
    'waiting_for_shift' => [],
    'weekend_employees' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    $date = $_GET['date'] ?? date('Y-m-d');
    $dayOfWeek = date('w', strtotime($date)); // 0=Sunday, 6=Saturday

    // STEP 0: Check if today is a holiday
    $holiday_check = $conn->prepare("SELECT id, name FROM ta_holidays WHERE holiday_date = :date AND is_active = 1 LIMIT 1");
    $holiday_check->bindParam(':date', $date);
    $holiday_check->execute();
    $is_holiday = $holiday_check->rowCount() > 0;
    $holiday_info = $holiday_check->fetch(\PDO::FETCH_ASSOC);

    // STEP 1: Detect Holiday Employees who WORKED (HOLIDAY_WORKED - for double pay)
    if ($is_holiday && !empty($holiday_info)) {
        $query = "SELECT 
                    a.attendance_id,
                    a.employee_id,
                    a.time_in,
                    e.full_name,
                    e.department
                  FROM ta_attendance a
                  JOIN employees e ON a.employee_id = e.employee_id
                  WHERE a.attendance_date = :date
                  AND a.time_in IS NOT NULL
                  ORDER BY e.full_name";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        $holiday_workers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($holiday_workers as $worker) {
            // Update status to HOLIDAY_WORKED
            $update_query = "UPDATE ta_attendance SET status = 'HOLIDAY_WORKED' WHERE attendance_id = :attendance_id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bindParam(':attendance_id', $worker['attendance_id'], \PDO::PARAM_INT);
            $update_stmt->execute();
            
            $results['holiday_worked'][] = [
                'employee_id' => $worker['employee_id'],
                'name' => $worker['full_name'],
                'department' => $worker['department'],
                'time_in' => $worker['time_in'],
                'holiday_name' => $holiday_info['name'],
                'double_pay_eligible' => true
            ];
        }
    }

    // STEP 2: Detect Holiday Absences (employees who didn't work on holiday)
    if ($is_holiday && !empty($holiday_info)) {
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
                    'double_pay_eligible' => false
                ];
            }
        }
    }

    // STEP 3: Detect Regular Absences (only on non-holiday working days)
    if (!$is_holiday && $dayOfWeek != 0) {
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
    }

    // STEP 4: Detect Late Arrivals
    $late_arrivals = $validationService->detectLateArrivals($date);
    foreach ($late_arrivals as $late) {
        // Update attendance record with LATE status
        $query = "UPDATE ta_attendance SET status = 'LATE', minutes_late = :minutes_late 
                  WHERE attendance_id = :attendance_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':attendance_id', $late['attendance_id'], \PDO::PARAM_INT);
        $stmt->bindParam(':minutes_late', $late['minutes_late'], \PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $results['late_arrivals'][] = [
                'employee_id' => $late['employee_id'],
                'name' => $late['name'],
                'department' => $late['department'],
                'time_in' => $late['time_in'],
                'shift_start' => $late['shift_start'],
                'minutes_late' => $late['minutes_late']
            ];
        }
    }

    // STEP 5: Detect Employees Waiting for Shift Assignment
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
    
    $waiting_employees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($waiting_employees as $employee) {
        $results['waiting_for_shift'][] = [
            'employee_id' => $employee['employee_id'],
            'name' => $employee['full_name'],
            'department' => $employee['department'],
            'reason' => 'No shift assigned'
        ];
    }

    // STEP 6: Mark Weekend Attendance
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
        'date_checked' => $date,
        'is_holiday' => $is_holiday,
        'holiday_name' => $holiday_info['name'] ?? null,
        'holiday_worked_count' => count($results['holiday_worked']),
        'holiday_absent_count' => count($results['holiday_absences']),
        'regular_absences_detected' => count($results['absences']),
        'late_arrivals_detected' => count($results['late_arrivals']),
        'waiting_for_shift_count' => count($results['waiting_for_shift']),
        'total_detections' => count($results['holiday_worked']) + count($results['holiday_absences']) + 
                              count($results['absences']) + count($results['late_arrivals'])
    ];

} catch (Exception $e) {
    $results['success'] = false;
    $results['message'] = 'Error: ' . $e->getMessage();
    $results['error'] = $e->getMessage();
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
