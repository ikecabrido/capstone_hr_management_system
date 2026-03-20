<?php
/**
 * Get Employee Schedule API
 * Returns shifts and attendance for a specific employee and date range
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/EmployeeShift.php';
require_once __DIR__ . '/../models/Attendance.php';

try {
    $employee_id = $_GET['employee_id'] ?? null;
    $start_date = $_GET['start_date'] ?? null;
    $end_date = $_GET['end_date'] ?? null;

    if (!$employee_id) {
        throw new Exception('Employee ID is required');
    }

    if (!$start_date || !$end_date) {
        throw new Exception('Date range is required');
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Get employee info
    $employee_query = "SELECT * FROM employees WHERE employee_id = ? AND employment_status = 'Active'";
    $stmt = $conn->prepare($employee_query);
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        throw new Exception('Employee not found');
    }

    // Get current employee shift assignment
    $shift_query = "SELECT es.*, s.* 
                    FROM employee_shifts es
                    JOIN shifts s ON es.shift_id = s.shift_id
                    WHERE es.employee_id = ? AND es.is_active = 1
                    LIMIT 1";
    
    $stmt = $conn->prepare($shift_query);
    $stmt->execute([$employee_id]);
    $current_shift = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get attendance records for the date range
    $attendance_query = "SELECT * FROM attendance 
                        WHERE employee_id = ? 
                        AND attendance_date BETWEEN ? AND ?
                        ORDER BY attendance_date ASC";
    
    $stmt = $conn->prepare($attendance_query);
    $stmt->execute([$employee_id, $start_date, $end_date]);
    $attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all available shifts for reference
    $shifts_query = "SELECT * FROM shifts WHERE is_active = 1 ORDER BY start_time";
    $stmt = $conn->prepare($shifts_query);
    $stmt->execute();
    $available_shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build schedule data
    $schedule_data = [];
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $date) {
        $date_str = $date->format('Y-m-d');
        $day_data = [
            'date' => $date_str,
            'day_name' => $date->format('l'),
            'shift' => $current_shift,
            'attendance' => null
        ];

        // Find attendance for this date
        foreach ($attendance_records as $record) {
            if ($record['attendance_date'] === $date_str) {
                $day_data['attendance'] = $record;
                break;
            }
        }

        $schedule_data[] = $day_data;
    }

    echo json_encode([
        'success' => true,
        'employee' => $employee,
        'current_shift' => $current_shift,
        'available_shifts' => $available_shifts,
        'schedule' => $schedule_data
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
