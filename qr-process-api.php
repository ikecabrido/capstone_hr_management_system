<?php
/**
 * QR Attendance Process API - Root Level
 * Handles QR confirmation submission from employee portal
 * This is a fallback endpoint
 */

// Log access
@mkdir(__DIR__ . '/time_attendance/logs', 0777, true);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/time_attendance/logs/qr-process-root.log');
ini_set('display_errors', 1);

// Log immediately at start
file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - START - Root API called\n", FILE_APPEND);

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Method not allowed');
    }

    // Get and validate JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Input received\n", FILE_APPEND);
    
    if (!$input) {
        http_response_code(400);
        throw new Exception('Invalid JSON input');
    }

    $employee_id = $input['employee_id'] ?? null;
    $token = $input['token'] ?? null;
    $preferredAction = $input['action'] ?? null;

    if (!$employee_id || !$token) {
        http_response_code(400);
        throw new Exception('Missing employee_id or token');
    }

    // Validate token format
    if (strlen($token) !== 64) {
        http_response_code(400);
        throw new Exception('Invalid token format: ' . strlen($token));
    }

    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Input validated\n", FILE_APPEND);

    // Connect to database
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Requiring database.php from: " . __DIR__ . '/auth/database.php' . "\n", FILE_APPEND);
    require_once __DIR__ . '/auth/database.php';
    
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Getting database instance\n", FILE_APPEND);
    $db = Database::getInstance()->getConnection();
    
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Database connected successfully\n", FILE_APPEND);

    // Validate token exists, is not expired, and hasn't been used
    $query = "SELECT token_id, generated_for_date, expires_at, used, used_by FROM ta_attendance_tokens WHERE token = ? LIMIT 1";
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Preparing query\n", FILE_APPEND);
    
    $stmt = $db->prepare($query);
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Executing query with token\n", FILE_APPEND);
    
    $stmt->execute([$token]);
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Query executed\n", FILE_APPEND);
    
    $token_record = $stmt->fetch();
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Token fetched: " . ($token_record ? 'found' : 'not found') . "\n", FILE_APPEND);

    if (!$token_record) {
        http_response_code(404);
        throw new Exception('Token not found');
    }

    // Check if token has expired
    if (new DateTime($token_record['expires_at']) < new DateTime()) {
        http_response_code(400);
        throw new Exception('Token has expired');
    }

    // Check if token has already been used
    if ($token_record['used']) {
        http_response_code(400);
        throw new Exception('Token has already been used');
    }

    // Verify employee exists
    $query = "SELECT employee_id, full_name FROM employees WHERE employee_id = ? LIMIT 1";
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Verifying employee\n", FILE_APPEND);
    
    $stmt = $db->prepare($query);
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch();
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Employee: " . ($employee ? 'found' : 'not found') . "\n", FILE_APPEND);

    if (!$employee) {
        http_response_code(404);
        throw new Exception('Employee not found');
    }

    // Get current date and time
    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $current_date = $now->format('Y-m-d');
    $current_time = $now->format('Y-m-d H:i:s');

    // Determine if this is a time_in or time_out based on explicit action if provided.
    $query = "SELECT attendance_id, time_in, time_out FROM ta_attendance 
              WHERE employee_id = ? AND attendance_date = ? 
              ORDER BY (time_in IS NOT NULL AND (time_out IS NULL OR time_out = '0000-00-00 00:00:00')) DESC,
                       created_at DESC, attendance_id DESC
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$employee_id, $current_date]);
    $attendance_record = $stmt->fetch();

    $requestedAction = strtoupper(trim((string) ($preferredAction ?? '')));
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Requested action: " . ($requestedAction ?: 'NONE') . "\n", FILE_APPEND);
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Attendance record: " . json_encode([
        'found' => (bool) $attendance_record,
        'time_in' => $attendance_record['time_in'] ?? null,
        'time_out' => $attendance_record['time_out'] ?? null
    ]) . "\n", FILE_APPEND);

    if (!in_array($requestedAction, ['TIME_IN', 'TIME_OUT'], true)) {
        if ($attendance_record && !empty($attendance_record['time_in']) && empty($attendance_record['time_out'])) {
            $requestedAction = 'TIME_OUT';
        } elseif ($attendance_record && !empty($attendance_record['time_out'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'You have already timed out today at ' . $attendance_record['time_out'],
                'data' => ['action' => 'COMPLETED']
            ]);
            exit;
        } else {
            $requestedAction = 'TIME_IN';
        }
    }

    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Resolved action: " . $requestedAction . "\n", FILE_APPEND);

    if ($requestedAction === 'TIME_OUT') {
        if (!$attendance_record || empty($attendance_record['time_in']) || !empty($attendance_record['time_out'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'No active time-in record found for today.',
                'data' => ['action' => 'TIME_OUT']
            ]);
            exit;
        }

        $action = 'TIME_OUT';
        $attendance_id = $attendance_record['attendance_id'];

        $timeInTimestamp = strtotime($attendance_record['time_in']);
        $elapsedSeconds = time() - $timeInTimestamp;
        $minimumTimeOutSeconds = 10800;
        if ($elapsedSeconds < $minimumTimeOutSeconds) {
            $remainingSeconds = $minimumTimeOutSeconds - $elapsedSeconds;
            $hours = floor($remainingSeconds / 3600);
            $minutes = floor(($remainingSeconds % 3600) / 60);
            $seconds = $remainingSeconds % 60;
            $timeParts = [];
            if ($hours > 0) {
                $timeParts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
            }
            if ($minutes > 0) {
                $timeParts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
            }
            if ($seconds > 0 && count($timeParts) < 2) {
                $timeParts[] = $seconds . ' second' . ($seconds > 1 ? 's' : '');
            }

            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'You can’t time out right now. Try again after ' . (implode(' ', $timeParts) ?: 'a moment') . '.',
                'data' => ['action' => 'TIME_OUT']
            ]);
            exit;
        }

        $query = "UPDATE ta_attendance SET time_out = ?, recorded_by = 'QR', updated_at = NOW() WHERE attendance_id = ? AND (time_out IS NULL OR time_out = '0000-00-00 00:00:00')";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $current_time,
            $attendance_id
        ]);

        if (!$result) {
            throw new Exception('Failed to update attendance record');
        }

        $message = 'Time out recorded successfully';
    } else {
        if ($attendance_record && !empty($attendance_record['time_in'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'You have already timed in today.',
                'data' => ['action' => 'TIME_IN']
            ]);
            exit;
        }

        $action = 'TIME_IN';

        // Get employee's current active shift
        $query = "SELECT shift_id FROM ta_employee_shifts WHERE employee_id = ? AND is_active = 1 LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$employee_id]);
        $shift = $stmt->fetch();

        $shift_id = $shift ? $shift['shift_id'] : null;

        if ($attendance_record) {
            $attendance_id = $attendance_record['attendance_id'];
            $query = "UPDATE ta_attendance SET time_in = ?, recorded_by = 'QR', status = 'PRESENT', updated_at = NOW() WHERE attendance_id = ? AND (time_in IS NULL OR time_in = '0000-00-00 00:00:00')";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                $current_time,
                $attendance_id
            ]);
        } else {
            $query = "INSERT INTO ta_attendance (employee_id, shift_id, attendance_date, time_in, recorded_by, status, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, 'QR', 'PRESENT', NOW(), NOW())";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                $employee_id,
                $shift_id,
                $current_date,
                $current_time
            ]);

            $attendance_id = $db->lastInsertId();
        }

        if (!$result) {
            throw new Exception('Failed to record attendance');
        }

        $message = 'Time in recorded successfully';
    }

    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Performed action: " . $action . " | message: " . $message . "\n", FILE_APPEND);

    // Mark token as used
    $query = "UPDATE ta_attendance_tokens SET used = 1, used_by = ?, used_at = NOW() WHERE token = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $employee_id,
        $token
    ]);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'action' => $action,
            'employee_name' => $employee['full_name'],
            'timestamp' => $current_time,
            'date' => $current_date
        ]
    ]);

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    $statusCode = http_response_code();
    if ($statusCode < 400) {
        $statusCode = 500;
    }
    http_response_code($statusCode);
    error_log('QR Process Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 'QR_PROCESS_ERROR',
        'trace' => $e->getTraceAsString()
    ]);
} catch (Throwable $e) {
    file_put_contents(__DIR__ . '/time_attendance/logs/qr-access-root.log', date('Y-m-d H:i:s') . " - Throwable: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    error_log('QR Process Fatal Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $e->getMessage(),
        'error_code' => 'FATAL_ERROR'
    ]);
}
