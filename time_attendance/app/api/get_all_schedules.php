<?php
/**
 * Get All Employees Schedules API
 * Returns shifts and attendance for all employees for a date range
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';

try {
    $start_date = $_GET['start_date'] ?? null;
    $end_date = $_GET['end_date'] ?? null;

    if (!$start_date || !$end_date) {
        throw new Exception('Date range is required');
    }

    $db = new Database();
    $conn = $db->getConnection();

    // Get all active employees
    $employees_query = "SELECT employee_id, full_name FROM employees WHERE employment_status = 'Active' ORDER BY full_name";
    $stmt = $conn->prepare($employees_query);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get attendance records for the date range
    $attendance_query = "SELECT a.*, e.full_name
                        FROM attendance a
                        JOIN employees e ON a.employee_id = e.employee_id
                        WHERE a.attendance_date BETWEEN ? AND ?
                        ORDER BY a.attendance_date, e.full_name";
    
    $stmt = $conn->prepare($attendance_query);
    $stmt->execute([$start_date, $end_date]);
    $attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get employee shifts
    $shifts_query = "SELECT es.*, s.shift_name, s.start_time, s.end_time, e.full_name
                    FROM employee_shifts es
                    JOIN shifts s ON es.shift_id = s.shift_id
                    JOIN employees e ON es.employee_id = e.employee_id
                    WHERE es.is_active = 1";
    
    $stmt = $conn->prepare($shifts_query);
    $stmt->execute();
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build schedule data with events for all employees
    $schedule_data = [];
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $date) {
        $date_str = $date->format('Y-m-d');
        
        // Get attendance for this day
        $day_attendance = array_filter($attendance_records, function($att) use ($date_str) {
            return $att['attendance_date'] === $date_str;
        });

        // Create events for each attendance record
        foreach ($day_attendance as $att) {
            $employee_name = $att['full_name'];
            
            // Determine status
            $status = 'present';
            if (empty($att['time_in'])) {
                $status = 'absent';
            } elseif ($att['time_in']) {
                $time_in = new DateTime($att['time_in']);
                if ($time_in->format('H') > 9) {
                    $status = 'late';
                }
            }

            $schedule_data[] = [
                'id' => 'att_' . $att['attendance_id'],
                'title' => $employee_name . ' (' . ucfirst($status) . ')',
                'start' => $att['attendance_date'] . 'T' . ($att['time_in'] ? date('H:i', strtotime($att['time_in'])) : '00:00'),
                'end' => $att['attendance_date'] . 'T' . ($att['time_out'] ? date('H:i', strtotime($att['time_out'])) : '23:59'),
                'className' => $status . '-event',
                'extendedProps' => [
                    'employee_id' => $att['employee_id'],
                    'employee_name' => $employee_name,
                    'status' => $status,
                    'time_in' => $att['time_in'],
                    'time_out' => $att['time_out']
                ]
            ];
        }
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $schedule_data,
        'message' => 'Schedules retrieved successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
