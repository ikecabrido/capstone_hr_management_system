<?php
/**
 * Attendance Confirmation API Endpoint
 * QR Scanning Handler - Direct Database Implementation
 * 
 * Avoids class conflicts by not loading admin portal classes
 * Implements attendance logic directly here
 */

// Suppress warnings from being output in JSON response
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Set timezone
date_default_timezone_set('Asia/Manila');

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$projectRoot = dirname(dirname(__DIR__));

try {
    // Log request for debugging
    error_log('[ATTENDANCE-CONFIRM] Request received');
    error_log('[ATTENDANCE-CONFIRM] POST: ' . json_encode($_POST));
    error_log('[ATTENDANCE-CONFIRM] SESSION user_id: ' . ($_SESSION['user_id'] ?? 'NULL'));
    
    // Get user ID from session
    $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;
    
    if (!$userId) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'User not authenticated. Please log in first.'
        ]);
        error_log('[ATTENDANCE-CONFIRM] ERROR: User not authenticated');
        exit;
    }
    
    // Get action from POST
    $action = $_POST['action'] ?? null;
    
    if (!$action || !in_array($action, ['time_in', 'time_out'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        error_log('[ATTENDANCE-CONFIRM] ERROR: Invalid action - ' . ($action ?? 'NULL'));
        exit;
    }
    
    // Get database connection using employee portal's database class
    $dbPath = $projectRoot . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'database.php';
    
    if (!file_exists($dbPath)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database configuration not found'
        ]);
        error_log('[ATTENDANCE-CONFIRM] ERROR: Database file not found at ' . $dbPath);
        exit;
    }
    
    require_once $dbPath;
    
    $database = Database::getInstance();
    $connection = $database->getConnection();
    
    if (!$connection) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        error_log('[ATTENDANCE-CONFIRM] ERROR: Could not get database connection');
        exit;
    }
    
    // Get employee ID from user_id
    $query = "SELECT employee_id FROM employees WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->execute([$userId]);
    $employeeResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$employeeResult) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Employee record not found'
        ]);
        error_log('[ATTENDANCE-CONFIRM] ERROR: No employee found for user_id: ' . $userId);
        exit;
    }
    
    $employee_id = $employeeResult['employee_id'];
    $method = $_POST['method'] ?? 'QR';
    
    error_log('[ATTENDANCE-CONFIRM] Processing: action=' . $action . ', employee=' . $employee_id . ', method=' . $method);
    
    // ========== ATTENDANCE LOGIC (Unified) ==========
    // Get today's date
    $today = date('Y-m-d');
    $currentTime = date('H:i:s');
    
    // Check if employee has any record today
    $checkQuery = "SELECT * FROM ta_attendance WHERE employee_id = ? AND DATE(attendance_date) = ?";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->execute([$employee_id, $today]);
    $attendanceRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    error_log('[ATTENDANCE-CONFIRM] Attendance record found: ' . ($attendanceRecord ? 'YES' : 'NO'));
    
    if ($action === 'time_in') {
        // TIME IN LOGIC
        
        // Check if holiday
        $holidayQuery = "SELECT * FROM ta_holidays WHERE DATE(holiday_date) = ? AND is_active = 1";
        $holidayStmt = $connection->prepare($holidayQuery);
        $holidayStmt->execute([$today]);
        $holiday = $holidayStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($holiday) {
            $holidayName = $holiday['holiday_name'] ?? $holiday['name'] ?? 'Holiday';
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Cannot time in on a holiday: ' . $holidayName
            ]);
            error_log('[ATTENDANCE-CONFIRM] Holiday detected: ' . $holidayName);
            exit;
        }
        
        // Check for duplicate time_in
        if ($attendanceRecord && !empty($attendanceRecord['time_in'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Already timed in at ' . $attendanceRecord['time_in']
            ]);
            error_log('[ATTENDANCE-CONFIRM] Duplicate time_in detected at ' . $attendanceRecord['time_in']);
            exit;
        }
        
        // If no record exists, create one
        if (!$attendanceRecord) {
            $insertQuery = "INSERT INTO ta_attendance (employee_id, attendance_date, time_in, status, is_approved, recorded_by, method, notes) 
                           VALUES (?, ?, ?, 'PENDING_APPROVAL', 0, ?, ?, 'Time in via QR')";
            $insertStmt = $connection->prepare($insertQuery);
            $insertStmt->execute([$employee_id, $today . ' ' . $currentTime, $currentTime, $userId, $method]);
            
            $attendanceId = $connection->lastInsertId();
            error_log('[ATTENDANCE-CONFIRM] New attendance record created: ' . $attendanceId);
        } else {
            // Update existing record with time_in
            $updateQuery = "UPDATE ta_attendance SET time_in = ?, status = 'PENDING_APPROVAL', recorded_by = ?, method = ?, notes = 'Time in via QR' 
                           WHERE id = ?";
            $updateStmt = $connection->prepare($updateQuery);
            $updateStmt->execute([$currentTime, $userId, $method, $attendanceRecord['id']]);
            
            $attendanceId = $attendanceRecord['id'];
            error_log('[ATTENDANCE-CONFIRM] Attendance record updated: ' . $attendanceId);
        }
        
        // Create audit log
        $auditQuery = "INSERT INTO audit_logs (user_id, action, module, description, timestamp) 
                      VALUES (?, 'TIME_IN_SUCCESS', 'attendance', ?, NOW())";
        $auditStmt = $connection->prepare($auditQuery);
        $auditStmt->execute([$userId, 'Employee ' . $employee_id . ' timed in at ' . $currentTime . ' via ' . $method]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Time in recorded at ' . date('H:i', strtotime($currentTime)),
            'action' => 'time_in',
            'time' => $currentTime,
            'status' => 'PENDING_APPROVAL'
        ]);
        
    } elseif ($action === 'time_out') {
        // TIME OUT LOGIC
        
        if (!$attendanceRecord || empty($attendanceRecord['time_in'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'No time in record found. Please time in first.'
            ]);
            error_log('[ATTENDANCE-CONFIRM] Cannot time out: no time_in found');
            exit;
        }
        
        // Check for duplicate time_out
        if (!empty($attendanceRecord['time_out'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Already timed out at ' . $attendanceRecord['time_out']
            ]);
            error_log('[ATTENDANCE-CONFIRM] Duplicate time_out detected at ' . $attendanceRecord['time_out']);
            exit;
        }
        
        // Calculate duration
        $timeIn = strtotime($attendanceRecord['time_in']);
        $timeOut = strtotime($currentTime);
        $durationSeconds = $timeOut - $timeIn;
        $durationHours = round($durationSeconds / 3600, 2);
        
        // Update record with time_out
        $updateQuery = "UPDATE ta_attendance SET time_out = ?, duration = ?, status = 'PENDING_APPROVAL', recorded_by = ?, method = ?, notes = 'Time out via QR' 
                       WHERE id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        $updateStmt->execute([$currentTime, $durationHours, $userId, $method, $attendanceRecord['id']]);
        
        error_log('[ATTENDANCE-CONFIRM] Time out recorded: ' . $currentTime . ', duration: ' . $durationHours . 'h');
        
        // Create audit log
        $auditQuery = "INSERT INTO audit_logs (user_id, action, module, description, timestamp) 
                      VALUES (?, 'TIME_OUT_SUCCESS', 'attendance', ?, NOW())";
        $auditStmt = $connection->prepare($auditQuery);
        $auditStmt->execute([$userId, 'Employee ' . $employee_id . ' timed out at ' . $currentTime . ' via ' . $method]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Time out recorded at ' . date('H:i', strtotime($currentTime)),
            'action' => 'time_out',
            'time' => $currentTime,
            'duration' => $durationHours . ' hours',
            'status' => 'PENDING_APPROVAL'
        ]);
    }
    
    // Clear QR pending flag
    if (isset($_SESSION['qr_pending'])) {
        unset($_SESSION['qr_pending']);
    }
    
} catch (PDOException $e) {
    error_log('[ATTENDANCE-CONFIRM] Database Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    
} catch (Exception $e) {
    error_log('[ATTENDANCE-CONFIRM] Exception: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>



