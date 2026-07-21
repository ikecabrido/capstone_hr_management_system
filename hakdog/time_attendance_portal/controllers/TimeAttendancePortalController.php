<?php

/**
 * Time Attendance Portal Controller
 * Handles all routing and logic for the time and attendance module
 * This controller is self-contained within the time_attendance_portal folder
 */

// Set Philippines timezone globally for all operations
date_default_timezone_set('Asia/Manila');

class TimeAttendancePortalController
{
    /**
     * Display the main time and attendance dashboard
     */
    public function index()
    {
        // Set PHP timezone to Philippines (UTC+8) 
        date_default_timezone_set('Asia/Manila');
        
        // Set error reporting to help debug issues
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        
        try {
            // Start session FIRST before anything else
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // IMPORTANT: Do all PHP processing here BEFORE layout.php outputs HTML
            // This prevents "headers already sent" errors
            
            // Check authentication - supports both session formats
            $user_id = null;
            if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
                $user_id = $_SESSION['user']['id'];
            } elseif (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
            }
            
            // Redirect to login if not authenticated
            if (!$user_id) {
                header("Location: ../../index.php");
                exit;
            }
            
            // Load database and required classes AFTER session check
            if (!class_exists('Database')) {
                require_once dirname(dirname(dirname(__DIR__))) . "/auth/database.php";
            }
            $portalBasePath = dirname(__DIR__);
            require_once $portalBasePath . "/lib/core/Session.php";
            require_once $portalBasePath . "/lib/helpers/AttendanceTracker.php";
            require_once $portalBasePath . "/lib/helpers/Helper.php";
            require_once $portalBasePath . "/lib/models/Attendance.php";
            
            // Initialize database connection
            $conn = Database::getInstance()->getConnection();
            
            // Get employee details from database
            $stmt = $conn->prepare("
                SELECT * FROM employees 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$employee) {
                header("Location: ../../index.php?error=employee_not_found");
                exit;
            }
            
            $employee_id = (int)$employee['employee_id'];
            
            // ============================================================================
            // HELPER FUNCTIONS (used by controller logic)
            // ============================================================================
            
            // Function to get today's attendance status
            $getTodayAttendanceStatus = function($conn, $employee_id) {
                // Set timezone for consistency
                date_default_timezone_set('Asia/Manila');
                $today = date('Y-m-d');
                
                // Log for debugging
                error_log("getTodayAttendanceStatus called: employee_id=$employee_id, today=$today");
                
                $stmt = $conn->prepare("
                    SELECT time_in, time_out FROM ta_attendance 
                    WHERE employee_id = ? AND DATE(attendance_date) = ? 
                    LIMIT 1
                ");
                $stmt->execute([$employee_id, $today]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                error_log("Query result: " . json_encode($result));
                
                if ($result) {
                    $duration = null;
                    if ($result['time_in'] && $result['time_out']) {
                        $start = strtotime($result['time_in']);
                        $end = strtotime($result['time_out']);
                        $diff = $end - $start;
                        $hours = floor($diff / 3600);
                        $mins = floor(($diff % 3600) / 60);
                        $duration = sprintf("%d:%02d", $hours, $mins);
                    }
                    $returnData = [
                        'time_in' => $result['time_in'],
                        'time_out' => $result['time_out'],
                        'duration' => $duration
                    ];
                    error_log("Returning status: " . json_encode($returnData));
                    return $returnData;
                }
                return ['time_in' => null, 'time_out' => null, 'duration' => null];
            };
            
            // Function to format time
            $formatTime = function($time) {
                if (empty($time)) return '--:--';
                return date('H:i', strtotime($time));
            };
            
            // Function to format date
            $formatDate = function($datetime) {
                if (empty($datetime)) return '--';
                return date('M d, Y', strtotime($datetime));
            };
            
            // Function to calculate working days
            $calculateWorkingDays = function($startDate, $endDate) {
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($start, $interval, $end->modify('+1 day'));
                $workingDays = 0;
                foreach ($period as $date) {
                    if ($date->format('N') < 6) { // N = 1-7 (Monday-Sunday)
                        $workingDays++;
                    }
                }
                return $workingDays;
            };
            
            // ============================================================================
            // Initialize AttendanceTracker for dual recording support
            // ============================================================================
            $attendanceTracker = new AttendanceTracker($conn);
            
            // ============================================================================
            // Initialize message variables
            // ============================================================================
            $message = "";
            $messageType = ""; // success, error, info, warning
            
            // Check for QR success message
            if (isset($_SESSION['qr_success'])) {
                $message = $_SESSION['qr_success'];
                $messageType = "success";
                unset($_SESSION['qr_success']);
            }
            
            // Check for QR error message
            if (isset($_SESSION['qr_error'])) {
                $message = $_SESSION['qr_error'];
                $messageType = "error";
                unset($_SESSION['qr_error']);
            }
            
            // ============================================================================
            // Handle form submission for time in/out
            // ============================================================================
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                try {
                    error_log('[TIME ATTENDANCE] ===== POST REQUEST RECEIVED =====');
                    error_log('[TIME ATTENDANCE] POST data: ' . json_encode($_POST));
                    error_log('[TIME ATTENDANCE] REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
                    error_log('[TIME ATTENDANCE] QUERY_STRING: ' . ($_SERVER['QUERY_STRING'] ?? 'NONE'));
                    
                    $action = trim($_POST['action'] ?? '');
                    $isQRConfirmation = isset($_POST['qr_confirm']) && $_POST['qr_confirm'] === 'true';
                    
                    error_log('[TIME ATTENDANCE] Action: ' . $action . ', IsQR: ' . ($isQRConfirmation ? 'yes' : 'no'));
                    error_log('[TIME ATTENDANCE] User ID: ' . $user_id . ', Employee ID: ' . $employee_id);
                    
                    // Check for QR pending data
                    $qr_pending = null;
                    if (isset($_SESSION['qr_pending'])) {
                        $qr_pending = $_SESSION['qr_pending'];
                        unset($_SESSION['qr_pending']);
                    }

                
                if ($action === 'time_in') {
                    error_log('[TIME IN] Recording attendance for employee: ' . $employee_id);
                    
                    // Query employee's current shift
                    $today = date('Y-m-d');
                    $shiftQuery = $conn->prepare("
                        SELECT shift_id FROM ta_employee_shifts 
                        WHERE employee_id = ? 
                        AND effective_from <= ? 
                        AND (effective_to IS NULL OR effective_to >= ?)
                        AND is_active = 1
                        ORDER BY effective_from DESC 
                        LIMIT 1
                    ");
                    $shiftQuery->execute([$employee_id, $today, $today]);
                    $shiftRecord = $shiftQuery->fetch(PDO::FETCH_ASSOC);
                    $shift_id = $shiftRecord['shift_id'] ?? null;
                    error_log('[TIME IN] Shift ID for employee: ' . ($shift_id ?? 'NULL'));
                    
                    $result = $attendanceTracker->recordAttendance(
                        $employee_id,
                        date('Y-m-d'),
                        date('Y-m-d H:i:s'),
                        null,
                        'MANUAL',
                        $shift_id
                    );
                    error_log('[TIME IN] Result: ' . ($result['success'] ? 'success' : 'failed'));
                    if ($result['success']) {
                        $message = "Time in recorded successfully!";
                        $messageType = "success";
                        
                        if ($isQRConfirmation) {
                            error_log('[TIME IN QR] Sending JSON response');
                            // Clear QR pending session for next scan
                            if (isset($_SESSION['qr_pending'])) {
                                unset($_SESSION['qr_pending']);
                            }
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode([
                                'success' => true,
                                'message' => $message
                            ]);
                            exit;
                        }
                        
                        $_SESSION['show_time_in_confirm'] = true;
                        $_SESSION['time_in_recorded'] = date('H:i:s');
                        $_SESSION['time_in_date'] = date('l, F j, Y');
                    } else {
                        $message = $result['message'] ?? "Failed to record time in";
                        $messageType = "error";
                        
                        if ($isQRConfirmation) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => false,
                                'message' => $message
                            ]);
                            exit;
                        }
                    }
                } elseif ($action === 'time_out') {
                    error_log('[TIME OUT] Recording time out for employee: ' . $employee_id);
                    // Get today's record to update time_out
                    $todayAttendance = $conn->prepare("
                        SELECT attendance_id FROM ta_attendance 
                        WHERE employee_id = ? AND DATE(attendance_date) = CURDATE() 
                        LIMIT 1
                    ");
                    $todayAttendance->execute([$employee_id]);
                    $record = $todayAttendance->fetch(PDO::FETCH_ASSOC);
                    
                    error_log('[TIME OUT] Record found: ' . ($record ? 'yes' : 'no'));
                    
                    if ($record) {
                        $stmt = $conn->prepare("
                            UPDATE ta_attendance 
                            SET time_out = NOW()
                            WHERE attendance_id = ?
                        ");
                        if ($stmt->execute([$record['attendance_id']])) {
                            // Fetch the updated record to get the actual time_out value from database
                            $checkStmt = $conn->prepare("SELECT time_out FROM ta_attendance WHERE attendance_id = ?");
                            $checkStmt->execute([$record['attendance_id']]);
                            $updatedRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
                            error_log('[TIME OUT] Updated record: ' . json_encode($updatedRecord));
                            
                            $message = "Time out recorded successfully!";
                            $messageType = "success";
                            
                            if ($isQRConfirmation) {
                                error_log('[TIME OUT QR] Sending JSON response');
                                // Clear QR pending session for next scan
                                if (isset($_SESSION['qr_pending'])) {
                                    unset($_SESSION['qr_pending']);
                                }
                                header('Content-Type: application/json; charset=utf-8');
                                echo json_encode([
                                    'success' => true,
                                    'message' => $message
                                ]);
                                exit;
                            }
                            
                            $_SESSION['show_time_out_confirm'] = true;
                            $_SESSION['time_out_recorded'] = date('H:i:s', strtotime($updatedRecord['time_out']));
                            $_SESSION['time_out_date'] = date('l, F j, Y');
                        } else {
                            error_log('[TIME OUT] Failed to update time out');
                            $message = "Failed to record time out";
                            $messageType = "error";
                            
                            if ($isQRConfirmation) {
                                error_log('[TIME OUT ERROR] Sending JSON error response');
                                header('Content-Type: application/json; charset=utf-8');
                                echo json_encode([
                                    'success' => false,
                                    'message' => $message
                                ]);
                                exit;
                            }
                        }
                    } else {
                        error_log('[TIME OUT] No record found for today');
                        $message = "No time in record found for today";
                        $messageType = "error";
                        
                        if ($isQRConfirmation) {
                            error_log('[TIME OUT NO RECORD] Sending JSON error response');
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode([
                                'success' => false,
                                'message' => $message
                            ]);
                            exit;
                        }
                    }
                } elseif ($action === 'submit_leave') {
                    // Handle leave request submission via AJAX
                    header('Content-Type: application/json');
                    
                    $leave_type_id = trim($_POST['leave_type_id'] ?? '');
                    $start_date = trim($_POST['start_date'] ?? '');
                    $end_date = trim($_POST['end_date'] ?? '');
                    $details = trim($_POST['details'] ?? '');
                    
                    // Validate
                    if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($details)) {
                        echo json_encode(['success' => false, 'message' => 'All fields are required']);
                        exit;
                    }
                    
                    if (strtotime($start_date) > strtotime($end_date)) {
                        echo json_encode(['success' => false, 'message' => 'Start date must be before end date']);
                        exit;
                    }
                    
                    if (strtotime($start_date) < strtotime('today')) {
                        echo json_encode(['success' => false, 'message' => 'Cannot submit leave for past dates']);
                        exit;
                    }
                    
                    try {
                        $insert_query = "
                            INSERT INTO ta_leave_requests 
                            (employee_id, leave_type_id, start_date, end_date, reason, status, created_at) 
                            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
                        ";
                        $insert_stmt = $conn->prepare($insert_query);
                        $result = $insert_stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $reason]);
                        
                        if ($result) {
                            echo json_encode(['success' => true, 'message' => 'Leave request submitted successfully']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error submitting request']);
                        }
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                    }
                    exit;
                }
                } catch (Exception $postEx) {
                    error_log('[TIME ATTENDANCE POST] Exception caught: ' . $postEx->getMessage());
                    error_log('[TIME ATTENDANCE POST] File: ' . $postEx->getFile() . ':' . $postEx->getLine());
                    
                    // For QR confirmations, return JSON error
                    if (isset($isQRConfirmation) && $isQRConfirmation) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Server error: ' . $postEx->getMessage()
                        ]);
                        exit;
                    }
                }
            }
            
            // ============================================================================
            // Get attendance period - from hire date to today
            // ============================================================================
            $query_employee = "SELECT date_hired FROM employees WHERE employee_id = ?";
            $stmt_emp = $conn->prepare($query_employee);
            $stmt_emp->execute([$employee_id]);
            $emp_data = $stmt_emp->fetch(PDO::FETCH_ASSOC);
            $attendance_start = $emp_data['date_hired'] ?? date('Y-01-01');
            $attendance_end = date('Y-m-d'); // Use today as end date
            
            // Get current month data for display purposes
            $current_month_start = date('Y-m-01');
            $current_month_end = date('Y-m-t');
            
            // ============================================================================
            // Get attendance for all time (from hire to today)
            // ============================================================================
            $query = "
                SELECT * FROM ta_attendance 
                WHERE employee_id = ? AND attendance_date BETWEEN ? AND ? 
                ORDER BY time_in DESC
            ";
            $stmt = $conn->prepare($query);
            $stmt->execute([$employee_id, $attendance_start, $attendance_end]);
            $monthly_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Filter attendance for current month only
            $current_month_attendance = array_filter($monthly_attendance, function($record) use ($current_month_start, $current_month_end) {
                $record_date = date('Y-m-d', strtotime($record['time_in']));
                return $record_date >= $current_month_start && $record_date <= $current_month_end;
            });
            
            // ============================================================================
            // Calculate monthly stats
            // ============================================================================
            $present_count = 0;
            $late_count = 0;
            $absent_count = 0;
            $total_hours = 0;
            $total_overtime = 0;
            
            foreach ($current_month_attendance as $record) {
                $total_hours += $record['total_hours_worked'] ?? 0;
                $total_overtime += $record['overtime_hours'] ?? 0;
                
                if ($record['status'] === 'ON_TIME' || $record['status'] === 'EARLY') {
                    $present_count++;
                } elseif ($record['status'] === 'LATE') {
                    $late_count++;
                }
            }
            
            // ============================================================================
            // Get last 6 months data
            // ============================================================================
            $six_months_ago = date('Y-m-d', strtotime('-6 months'));
            $query_six = "
                SELECT DATE(time_in) as date, status, total_hours_worked 
                FROM ta_attendance 
                WHERE employee_id = ? AND attendance_date >= ? 
                ORDER BY time_in DESC
            ";
            $stmt_six = $conn->prepare($query_six);
            $stmt_six->execute([$employee_id, $six_months_ago]);
            $six_months_data = $stmt_six->fetchAll(PDO::FETCH_ASSOC);
            
            // ============================================================================
            // Get leave balance
            // ============================================================================
            $current_year = date('Y');
            $query_balance = "
                SELECT lb.*, lt.leave_type_name 
                FROM ta_leave_balances lb 
                JOIN ta_leave_types lt ON lb.leave_type_id = lt.leave_type_id 
                WHERE lb.employee_id = ? AND lb.year = ?
            ";
            $stmt_balance = $conn->prepare($query_balance);
            $stmt_balance->execute([$employee_id, $current_year]);
            $leave_balances = $stmt_balance->fetchAll(PDO::FETCH_ASSOC);
            
            // ============================================================================
            // Calculate attendance percentage
            // ============================================================================
            $working_days = $calculateWorkingDays($current_month_start, $current_month_end);
            $attendance_percentage = $working_days > 0 ? ($present_count / $working_days) * 100 : 0;
            
            // ============================================================================
            // Get today's status
            // ============================================================================
            $statusInfo = $getTodayAttendanceStatus($conn, $employee_id);
            
            // ============================================================================
            // Get leave requests history
            // ============================================================================
            $query_requests = "
                SELECT lr.*, lt.leave_type_name 
                FROM ta_leave_requests lr 
                JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id 
                WHERE lr.employee_id = ? 
                ORDER BY lr.date_submitted DESC 
                LIMIT 10
            ";
            $stmt_requests = $conn->prepare($query_requests);
            $stmt_requests->execute([$employee_id]);
            $leave_requests = $stmt_requests->fetchAll(PDO::FETCH_ASSOC);
            
            // ============================================================================
            // Get all leave types for form
            // ============================================================================
            $query_types = "SELECT * FROM ta_leave_types";
            $stmt_types = $conn->prepare($query_types);
            $stmt_types->execute();
            $leave_types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);
            
            // Get today's assigned shift (optional)
            $today_shift = null;
            
            // ============================================================================
            // HANDLE AJAX REQUESTS (must be before layout.php is included)
            // ============================================================================
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'submit_leave') {
                header('Content-Type: application/json');
                
                $leave_type_id = trim($_POST['leave_type_id'] ?? '');
                $start_date = trim($_POST['start_date'] ?? '');
                $end_date = trim($_POST['end_date'] ?? '');
                $details = trim($_POST['details'] ?? '');
                
                // Validate input fields
                if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($details)) {
                    echo json_encode(['success' => false, 'message' => 'All fields are required']);
                    exit;
                }
                
                if (strtotime($start_date) > strtotime($end_date)) {
                    echo json_encode(['success' => false, 'message' => 'Start date must be before end date']);
                    exit;
                }
                
                if (strtotime($start_date) < strtotime('today')) {
                    echo json_encode(['success' => false, 'message' => 'Cannot submit leave for past dates']);
                    exit;
                }
                
                try {
                    // ==================== BALANCE DEDUCTION LOGIC ====================
                    
                    // Step 1: Get leave type details
                    $leave_type_query = "SELECT * FROM ta_leave_types WHERE leave_type_id = ?";
                    $leave_type_stmt = $conn->prepare($leave_type_query);
                    $leave_type_stmt->execute([$leave_type_id]);
                    $leave_type = $leave_type_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$leave_type) {
                        echo json_encode(['success' => false, 'message' => 'Invalid leave type selected']);
                        exit;
                    }
                    
                    // Step 2: Calculate days requested (inclusive of both start and end date)
                    $start_timestamp = strtotime($start_date);
                    $end_timestamp = strtotime($end_date);
                    $days_requested = floor(($end_timestamp - $start_timestamp) / 86400) + 1;
                    
                    // Step 3: If leave type is deductible, validate balance
                    if ($leave_type['is_deductible'] == 1) {
                        $current_year = date('Y');
                        
                        // Get current leave balance
                        $balance_query = "SELECT * FROM ta_leave_balances 
                                         WHERE employee_id = ? AND leave_type_id = ? AND year = ?";
                        $balance_stmt = $conn->prepare($balance_query);
                        $balance_stmt->execute([$employee_id, $leave_type_id, $current_year]);
                        $balance = $balance_stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // If no balance record exists, create one with default allocation
                        if (!$balance) {
                            $create_balance_query = "INSERT INTO ta_leave_balances 
                                                    (employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance)
                                                    VALUES (?, ?, ?, ?, ?, ?)";
                            $create_balance_stmt = $conn->prepare($create_balance_query);
                            $opening = $leave_type['days_per_year'];
                            $create_balance_stmt->execute([$employee_id, $leave_type_id, $current_year, $opening, 0, $opening]);
                            
                            // Fetch the newly created balance
                            $balance_stmt->execute([$employee_id, $leave_type_id, $current_year]);
                            $balance = $balance_stmt->fetch(PDO::FETCH_ASSOC);
                        }
                        
                        // Check if sufficient balance
                        if ($balance['remaining_balance'] < $days_requested) {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Insufficient ' . $leave_type['leave_type_name'] . ' balance. Available: ' . number_format($balance['remaining_balance'], 2) . ' days, Requested: ' . $days_requested . ' days'
                            ]);
                            exit;
                        }
                    }
                    
                    // Step 4: Insert leave request
                    $insert_query = "INSERT INTO ta_leave_requests 
                                    (employee_id, leave_type_id, start_date, end_date, details, status, date_submitted) 
                                    VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
                    $insert_stmt = $conn->prepare($insert_query);
                    $result = $insert_stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $details]);
                    
                    if ($result) {
                        $leave_request_id = $conn->lastInsertId();
                        
                        // Step 5: If deductible, deduct the balance immediately upon submission
                        if ($leave_type['is_deductible'] == 1) {
                            $deduct_query = "UPDATE ta_leave_balances 
                                            SET used_balance = used_balance + ?,
                                                remaining_balance = remaining_balance - ?,
                                                updated_at = NOW()
                                            WHERE employee_id = ? AND leave_type_id = ? AND year = ?";
                            $deduct_stmt = $conn->prepare($deduct_query);
                            $deduct_stmt->execute([$days_requested, $days_requested, $employee_id, $leave_type_id, date('Y')]);
                        }
                        
                        // Step 6: Create daily leave records for each day of leave
                        $current_date = $start_timestamp;
                        $holiday_count = 0;
                        
                        while ($current_date <= $end_timestamp) {
                            $date_str = date('Y-m-d', $current_date);
                            
                            // Check if this date is a holiday
                            $holiday_query = "SELECT holiday_id FROM ta_holidays 
                                             WHERE holiday_date = ? AND is_active = 1";
                            $holiday_stmt = $conn->prepare($holiday_query);
                            $holiday_stmt->execute([$date_str]);
                            $is_holiday = $holiday_stmt->fetch() ? 1 : 0;
                            
                            // Create daily record
                            $daily_query = "INSERT INTO ta_leave_daily_records 
                                           (leave_request_id, employee_id, leave_date, leave_type_id, is_holiday, balance_deducted)
                                           VALUES (?, ?, ?, ?, ?, 1)";
                            $daily_stmt = $conn->prepare($daily_query);
                            $daily_stmt->execute([$leave_request_id, $employee_id, $date_str, $leave_type_id, $is_holiday]);
                            
                            if ($is_holiday) {
                                $holiday_count++;
                            }
                            
                            $current_date += 86400; // Add 1 day
                        }
                        
                        // Prepare response with balance info
                        $response = [
                            'success' => true,
                            'message' => 'Leave request submitted successfully',
                            'leave_request_id' => $leave_request_id,
                            'leave_type' => $leave_type['leave_type_name'],
                            'days_requested' => $days_requested,
                            'holiday_count' => $holiday_count,
                            'working_days' => $days_requested - $holiday_count
                        ];
                        
                        // Add balance info if deductible
                        if ($leave_type['is_deductible'] == 1) {
                            $updated_balance_stmt = $conn->prepare(
                                "SELECT remaining_balance FROM ta_leave_balances 
                                 WHERE employee_id = ? AND leave_type_id = ? AND year = ?"
                            );
                            $updated_balance_stmt->execute([$employee_id, $leave_type_id, date('Y')]);
                            $updated_balance = $updated_balance_stmt->fetch(PDO::FETCH_ASSOC);
                            $response['new_balance'] = $updated_balance['remaining_balance'];
                            $response['is_deductible'] = true;
                        } else {
                            $response['is_deductible'] = false;
                        }
                        
                        echo json_encode($response);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error submitting leave request']);
                    }
                    
                    // ==================== END BALANCE DEDUCTION LOGIC ====================
                    
                } catch (Exception $e) {
                    error_log("Leave submission error: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                }
                exit;
            }
            
            // Now set up the display
            $title = "Time & Attendance";
            $content = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'employee_dashboard.php';
            
        } catch (Exception $e) {
            // Set error display
            $title = "Error";
            $content = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'error-content.php';
            $error_message = $e->getMessage();
        }
        
        // NOW include layout.php after all PHP processing is done
        require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'layout.php';
    }

    /**
     * Get attendance data via API
     */
    public function getAttendanceData()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            if (!class_exists('Database')) {
                require_once dirname(dirname(dirname(__DIR__))) . "/auth/database.php";
            }
            
            $conn = Database::getInstance()->getConnection();
            
            $user_id = $_SESSION['user_id'];
            
            // Get employee using user_id foreign key
            $stmt = $conn->prepare("
                SELECT employee_id FROM employees 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$employee) {
                echo json_encode(['success' => false, 'message' => 'Employee record not found']);
                exit;
            }
            
            $employee_id = $employee['employee_id'];
            
            // Fetch attendance data
            $stmt = $conn->prepare("
                SELECT * FROM ta_attendance 
                WHERE employee_id = ? 
                ORDER BY attendance_date DESC 
                LIMIT 30
            ");
            $stmt->execute([$employee_id]);
            $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'employee_id' => $employee_id,
                'records' => $attendanceRecords
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error fetching attendance data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get leave balance data via API
     */
    public function getLeaveBalance()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            if (!class_exists('Database')) {
                require_once dirname(dirname(dirname(__DIR__))) . "/auth/database.php";
            }
            
            $conn = Database::getInstance()->getConnection();
            
            $user_id = $_SESSION['user_id'];
            
            // Get employee using user_id foreign key
            $stmt = $conn->prepare("
                SELECT employee_id FROM employees 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$employee) {
                echo json_encode(['success' => false, 'message' => 'Employee record not found']);
                exit;
            }
            
            $employee_id = $employee['employee_id'];
            
            // Fetch leave balance
            $stmt = $conn->prepare("
                SELECT * FROM employee_leave_balance 
                WHERE employee_id = ?
            ");
            $stmt->execute([$employee_id]);
            $leaveBalance = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'employee_id' => $employee_id,
                'leave_balance' => $leaveBalance
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error fetching leave balance: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Record time in
     */
    public function recordTimeIn()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            if (!class_exists('Database')) {
                require_once dirname(dirname(dirname(__DIR__))) . "/auth/database.php";
            }
            
            $conn = Database::getInstance()->getConnection();
            
            $user_id = $_SESSION['user_id'];
            
            // Get employee using user_id foreign key
            $stmt = $conn->prepare("
                SELECT employee_id FROM employees 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$employee) {
                echo json_encode(['success' => false, 'message' => 'Employee record not found']);
                exit;
            }
            
            $employee_id = $employee['employee_id'];
            $currentTime = date('Y-m-d H:i:s');
            $currentDate = date('Y-m-d');
            
            // Check if already timed in today
            $stmt = $conn->prepare("
                SELECT id FROM ta_attendance 
                WHERE employee_id = ? AND DATE(attendance_date) = ?
            ");
            $stmt->execute([$employee_id, $currentDate]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Already timed in today']);
                exit;
            }
            
            // Record time in
            $stmt = $conn->prepare("
                INSERT INTO ta_attendance (employee_id, attendance_date, time_in) 
                VALUES (?, ?, ?)
            ");
            
            if ($stmt->execute([$employee_id, $currentDate, $currentTime])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Time in recorded successfully',
                    'time_in' => $currentTime
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to record time in']);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Record time out
     */
    public function recordTimeOut()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        try {
            if (!class_exists('Database')) {
                require_once dirname(dirname(dirname(__DIR__))) . "/auth/database.php";
            }
            
            $conn = Database::getInstance()->getConnection();
            
            $user_id = $_SESSION['user_id'];
            
            // Get employee using user_id foreign key
            $stmt = $conn->prepare("
                SELECT employee_id FROM employees 
                WHERE user_id = ?
            ");
            $stmt->execute([$user_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$employee) {
                echo json_encode(['success' => false, 'message' => 'Employee record not found']);
                exit;
            }
            
            $employee_id = $employee['employee_id'];
            $currentTime = date('Y-m-d H:i:s');
            $currentDate = date('Y-m-d');
            
            // Update time out
            $stmt = $conn->prepare("
                UPDATE ta_attendance 
                SET time_out = ? 
                WHERE employee_id = ? AND DATE(attendance_date) = ?
            ");
            
            if ($stmt->execute([$currentTime, $employee_id, $currentDate])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Time out recorded successfully',
                    'time_out' => $currentTime
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to record time out']);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle QR attendance processing after login
     * Called when user logs in via QR code
     */
    public function handleQRAttendance()
    {
        date_default_timezone_set('Asia/Manila');
        
        // Create debug log file for troubleshooting
        $debugLog = dirname(dirname(dirname(__DIR__))) . '/qr_debug.log';
        
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] handleQRAttendance() called\n", FILE_APPEND);

            // Check if QR token exists in session
            if (!isset($_SESSION['qr_token'])) {
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] No QR token in session, calling index()\n", FILE_APPEND);
                // No QR token, just go to regular time attendance
                $this->index();
                exit;
            }

            // Get the QR token
            $qr_token = $_SESSION['qr_token'];
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] QR token found: " . substr($qr_token, 0, 20) . "...\n", FILE_APPEND);
            // Don't unset the token yet - keep it for next scan if needed

            // Load database (using require_once to prevent redeclaration)
            if (!class_exists('Database')) {
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Loading Database class\n", FILE_APPEND);
                require_once dirname(dirname(dirname(__DIR__))) . "/auth/database.php";
            }
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Getting database connection\n", FILE_APPEND);
            $conn = Database::getInstance()->getConnection();
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Database connection successful\n", FILE_APPEND);

            // Get user ID from session
            $user_id = null;
            if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
                $user_id = $_SESSION['user']['id'];
            } elseif (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
            }

            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] User ID: " . ($user_id ? $user_id : 'NOT FOUND') . "\n", FILE_APPEND);

            if (!$user_id) {
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] ERROR: No user ID in session\n", FILE_APPEND);
                header("Location: index.php");
                exit;
            }

            // Get employee details
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Querying employee record for user_id=$user_id\n", FILE_APPEND);
            $stmt = $conn->prepare("SELECT * FROM employees WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Employee found: " . ($employee ? $employee['employee_id'] : 'NO') . "\n", FILE_APPEND);

            if (!$employee) {
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] ERROR: Employee not found for user_id=$user_id\n", FILE_APPEND);
                header("Location: index.php?error=employee_not_found");
                exit;
            }

            $employee_id = $employee['employee_id'];
            $today = date('Y-m-d');
            $now = date('Y-m-d H:i:s');

            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Employee ID: $employee_id, Today: $today\n", FILE_APPEND);

            // Check if there's already a record for today
            $checkQuery = "SELECT attendance_id, time_in, time_out FROM ta_attendance 
                           WHERE employee_id = ? AND DATE(attendance_date) = ?";
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Executing query: " . str_replace('?', "'$employee_id' OR '$today'", $checkQuery) . "\n", FILE_APPEND);
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$employee_id, $today]);
            $record = $checkStmt->fetch(PDO::FETCH_ASSOC);

            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Record found: " . ($record ? 'YES' : 'NO') . "\n", FILE_APPEND);

            $action_type = '';
            $message = '';

            if ($record) {
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Existing record - time_in: " . ($record['time_in'] ?? 'NULL') . ", time_out: " . ($record['time_out'] ?? 'NULL') . "\n", FILE_APPEND);
                if (empty($record['time_in'])) {
                    $action_type = 'time_in';
                    $message = 'Time In';
                    file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Action: TIME IN\n", FILE_APPEND);

                } elseif (empty($record['time_out'])) {
                    $action_type = 'time_out';
                    $message = 'Time Out';
                } else {
                    // Already completed
                    file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] ERROR: Attendance already completed today\n", FILE_APPEND);
                    $_SESSION['qr_error'] = 'Attendance already recorded for today';
                    header("Location: index.php?url=time-attendance");
                    exit;
                }
            } else {
                // New record
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] New attendance record - Action: TIME IN\n", FILE_APPEND);
                $action_type = 'time_in';
                $message = 'Time In';
            }

            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Storing QR pending data - Action: $action_type, Message: $message\n", FILE_APPEND);

            // Store QR pending data in session
            $_SESSION['qr_pending'] = [
                'employee_id' => $employee_id,
                'action_type' => $action_type,
                'message' => $message,
                'current_time' => date('H:i:s'),
                'full_name' => $employee['full_name'] ?? 'Employee',
                'token' => $qr_token
            ];

            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] QR pending data stored successfully\n", FILE_APPEND);
            file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] Redirecting to: index.php?url=time-attendance&qr_confirm=1\n", FILE_APPEND);

            // Redirect to time attendance which will show the confirmation modal
            // Use relative path to ensure proper redirect within the same application
            // Note: Session data must be preserved across redirect
            header("Location: index.php?url=time-attendance&qr_confirm=1");
            exit;

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $errorCode = $e->getCode();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
            
            $logEntry = "[" . date('Y-m-d H:i:s') . "] EXCEPTION: " . $errorMsg . " (Code: $errorCode, File: $errorFile, Line: $errorLine)\n";
            file_put_contents($debugLog, $logEntry, FILE_APPEND);
            error_log($logEntry);
            
            // Try to redirect with error message
            header("Location: index.php?error=exception&msg=" . urlencode($errorMsg . " (Line $errorLine)"));
            exit;
        }
    }
}
