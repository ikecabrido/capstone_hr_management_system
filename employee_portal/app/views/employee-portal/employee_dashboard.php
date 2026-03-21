<?php
/**
 * Employee Personal Dashboard
 * Shows individual employee attendance stats, leave balance, and performance
 */

require_once "../app/config/Database.php";
require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/AttendanceController.php";
require_once "../app/models/Employee.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Leave.php";
require_once "../app/models/EmployeeShift.php";
require_once "../app/models/Shift.php";
require_once "../app/helpers/Helper.php";
require_once "../app/core/Session.php";

Session::start();

// Check authentication
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

$user_id = AuthController::getCurrentUserId();
$employeeModel = new Employee();
$attendanceModel = new Attendance();
$leaveModel = new Leave();
$db = new Database();
$conn = $db->getConnection();
$employeeShiftModel = new EmployeeShift($conn);
$attendanceController = new AttendanceController();

// Get employee details
$employee = $employeeModel->getByUserId($user_id);
if (!is_array($employee) || !isset($employee['employee_id'])) {
    header("Location: ../../login_form.php");
    exit;
}
$employee_id = $employee['employee_id'];

// Get today's attendance status
$statusInfo = $attendanceController->getStatus($employee_id);

// Initialize message variables
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

// Handle form submission for time in/out
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = trim($_POST['action'] ?? '');

    if ($action === 'time_in') {
        $result = $attendanceController->timeIn($employee_id, 'MANUAL');
        if ($result['success']) {
            $message = $result['message'];
            $messageType = "success";
            // Set session flag to show time in confirmation
            $_SESSION['show_time_in_confirm'] = true;
            // Refresh status
            $statusInfo = $attendanceController->getStatus($employee_id);
        } else {
            $message = $result['message'];
            $messageType = "error";
        }
    } elseif ($action === 'time_out') {
        $result = $attendanceController->timeOut($employee_id);
        if ($result['success']) {
            $message = $result['message'];
            $messageType = "success";
            // Set session flag to show time out confirmation
            $_SESSION['show_time_out_confirm'] = true;
            // Refresh status
            $statusInfo = $attendanceController->getStatus($employee_id);
        } else {
            $message = $result['message'];
            $messageType = "error";
        }
    }
}

// Get current month data
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

// Get attendance for this month
$query = "SELECT * FROM attendance 
          WHERE employee_id = ? AND DATE(time_in) BETWEEN ? AND ?
          ORDER BY time_in DESC";
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare($query);
$stmt->execute([$employee_id, $current_month_start, $current_month_end]);
$monthly_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate monthly stats
$present_count = 0;
$late_count = 0;
$absent_count = 0;
$total_hours = 0;
$total_overtime = 0;

foreach ($monthly_attendance as $record) {
    $total_hours += $record['total_hours_worked'] ?? 0;
    $total_overtime += $record['overtime_hours'] ?? 0;
    
    if ($record['status'] === 'ON_TIME' || $record['status'] === 'EARLY') {
        $present_count++;
    } elseif ($record['status'] === 'LATE') {
        $late_count++;
    }
}

// Get last 6 months data
$six_months_ago = date('Y-m-d', strtotime('-6 months'));
$query_six = "SELECT DATE(time_in) as date, status, total_hours_worked 
              FROM attendance 
              WHERE employee_id = ? AND DATE(time_in) >= ?
              ORDER BY time_in DESC";
$stmt_six = $conn->prepare($query_six);
$stmt_six->execute([$employee_id, $six_months_ago]);
$six_months_data = $stmt_six->fetchAll(PDO::FETCH_ASSOC);

// Get leave balance
$current_year = date('Y');
$query_balance = "SELECT lb.*, lt.leave_type_name 
                  FROM leave_balances lb
                  JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
                  WHERE lb.employee_id = ? AND lb.year = ?";
$stmt_balance = $conn->prepare($query_balance);
$stmt_balance->execute([$employee_id, $current_year]);
$leave_balances = $stmt_balance->fetchAll(PDO::FETCH_ASSOC);

// Calculate attendance percentage
$working_days = Helper::calculateWorkingDays($current_month_start, $current_month_end);
$attendance_percentage = $working_days > 0 ? ($present_count / $working_days) * 100 : 0;

// Get today's assigned shift
$today = date('Y-m-d');
$query_shift = "SELECT es.*, s.shift_name, s.start_time, s.end_time, s.break_duration 
                FROM employee_shifts es
                JOIN shifts s ON es.shift_id = s.shift_id
                WHERE es.employee_id = ? AND es.is_active = 1 AND (es.effective_to IS NULL OR es.effective_to >= ?)
                AND ? BETWEEN es.effective_from AND COALESCE(es.effective_to, ?)";
$stmt_shift = $conn->prepare($query_shift);
$stmt_shift->execute([$employee_id, $today, $today]);
$today_shift = $stmt_shift->fetch(PDO::FETCH_ASSOC);

// Get leave requests history
$query_requests = "SELECT lr.*, lt.leave_type_name 
                   FROM leave_requests lr
                   JOIN leave_types lt ON lr.leave_type_id = lt.leave_type_id
                   WHERE lr.employee_id = ?
                   ORDER BY lr.date_submitted DESC LIMIT 10";
$stmt_requests = $conn->prepare($query_requests);
$stmt_requests->execute([$employee_id]);
$leave_requests = $stmt_requests->fetchAll(PDO::FETCH_ASSOC);

// Get all leave types for form
$query_types = "SELECT * FROM leave_types WHERE is_active = 1";
$stmt_types = $conn->prepare($query_types);
$stmt_types->execute();
$leave_types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

// Handle leave request submission via AJAX
$leave_request_response = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'submit_leave') {
    header('Content-Type: application/json');
    
    $leave_type_id = trim($_POST['leave_type_id'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    
    // Validate
    if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($reason)) {
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
        $insert_query = "INSERT INTO leave_requests (employee_id, leave_type_id, start_date, end_date, reason, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/employeeDashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/mobile-responsive.js" defer></script>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="menu-toggle" id="menuToggle" style="display: none;">☰</button>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>message
    
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div class="live-clock" id="liveClock">00:00:00</div>
        </div>
        <div class="content-wrapper">
            <h1>Dashboard</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($employee['full_name']); ?></strong>!</p>

            <!-- Messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <span class="alert-icon"><?php echo $messageType === 'success' ? '✔' : '✘'; ?></span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <?php 
            // Show confirmation modals if needed
            if (isset($_SESSION['show_time_in_confirm'])): 
                unset($_SESSION['show_time_in_confirm']);
            ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showTimeInConfirmation();
                    });
                </script>
            <?php endif; ?>

            <?php 
            if (isset($_SESSION['show_time_out_confirm'])): 
                unset($_SESSION['show_time_out_confirm']);
            ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showTimeOutConfirmation();
                    });
                </script>
            <?php endif; ?>

            <!-- Time In/Out Action Section -->
            <div class="time-action-section">
                <div class="time-action-header">
                    <h3> Time In/Out</h3>
                    <span><?php echo date('l, F j, Y'); ?></span>
                </div>
                
                <div class="time-status">
                    <div class="time-status-item">
                        <div class="time-status-label">Time In</div>
                        <div class="time-status-value">
                            <?php echo (!empty($statusInfo['time_in']) ? Helper::formatTime($statusInfo['time_in']) : '--:--'); ?>
                        </div>
                    </div>

                    <div class="time-status-item">
                        <div class="time-status-label">Time Out</div>
                        <div class="time-status-value">
                            <?php echo (!empty($statusInfo['time_out']) ? Helper::formatTime($statusInfo['time_out']) : '--:--'); ?>
                        </div>
                    </div>

                    <div class="time-status-item">
                        <div class="time-status-label">Duration</div>
                        <div class="time-status-value">
                            <?php echo (!empty($statusInfo['duration']) ? $statusInfo['duration'] : '--'); ?>
                        </div>
                    </div>
                </div>

                <form method="POST" class="time-action-buttons">
                    <?php if (empty($statusInfo['time_in'])): ?>
                        <button type="submit" name="action" value="time_in" class="btn-time-action btn-time-in">
                            Timed In
                        </button>
                        <span style="align-self: center; opacity: 0.9;"> Waiting for Time in</span>
                    <?php elseif (empty($statusInfo['time_out'])): ?>
                        <button type="submit" name="action" value="time_out" class="btn-time-action btn-time-out">
                            Timed Out
                        </button>
                        <span style="align-self: center; opacity: 0.9;"> Already timed in</span>
                    <?php else: ?>
                        <button type="submit" name="action" value="time_in" class="btn-time-action" disabled>
                            Time In Completed
                        </button>
                        <button type="submit" name="action" value="time_out" class="btn-time-action" disabled>
                            Time Out Completed
                        </button>
                    <?php endif; ?>
                </form>

                <!-- QR Code Scanner Option -->
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="qr_scanner.php" class="btn-time-action" style="background: #667eea; color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-qrcode"></i> Scan QR Code
                    </a>
                    <?php if (AuthController::hasRole('time')): ?>
                        <a href="qr_display_kiosk.php" class="btn-time-action" style="background: #27ae60; color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-tv"></i> Display Kiosk
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="dashboard-grid">
                <div class="card present">
                    <h3>Present This Month</h3>
                    <div class="card-value"><?php echo $present_count; ?></div>
                    <div class="card-unit">days</div>
                </div>
                
                <div class="card late">
                    <h3>Late Arrivals</h3>
                    <div class="card-value"><?php echo $late_count; ?></div>
                    <div class="card-unit">times</div>
                </div>
                
                <div class="card hours">
                    <h3>Hours Worked</h3>
                    <div class="card-value"><?php echo number_format($total_hours, 1); ?></div>
                    <div class="card-unit">hours</div>
                </div>
                
                <div class="card overtime">
                    <h3>Overtime Hours</h3>
                    <div class="card-value"><?php echo number_format($total_overtime, 1); ?></div>
                    <div class="card-unit">hours</div>
                </div>
            </div>

            <!-- Today's Shift Schedule Card -->
            <div class="dashboard-grid">
                <div class="card shift-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 style="color: white; margin-top: 0;">📅 Today's Shift</h3>
                    <?php if ($today_shift): ?>
                        <div style="margin-top: 15px;">
                            <p style="font-size: 18px; font-weight: bold; margin: 10px 0;">
                                <?php echo htmlspecialchars($today_shift['shift_name']); ?>
                            </p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
                                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 6px; text-align: center;">
                                    <p style="margin: 0; font-size: 12px; opacity: 0.9;">Start Time</p>
                                    <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">
                                        <?php echo date('h:i A', strtotime($today_shift['start_time'])); ?>
                                    </p>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 6px; text-align: center;">
                                    <p style="margin: 0; font-size: 12px; opacity: 0.9;">End Time</p>
                                    <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">
                                        <?php echo date('h:i A', strtotime($today_shift['end_time'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 6px; margin-top: 10px; text-align: center;">
                                <p style="margin: 0; font-size: 12px; opacity: 0.9;">Break Duration</p>
                                <p style="margin: 5px 0 0 0; font-weight: bold;">
                                    <?php echo htmlspecialchars($today_shift['break_duration']); ?>
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p style="color: rgba(255,255,255,0.9); font-style: italic; margin-top: 15px;">ℹ️ No shift assigned for today</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attendance Percentage Chart -->
            <div class="chart-container">
                <h3>Attendance Percentage This Month</h3>
                <div style="position: relative; width: 100%; height: 100%; flex: 1;">
                    <canvas id="attendanceChart" width="800" height="400"></canvas>
                </div>
            </div>

            <!-- 6-Month Trend Chart -->
            <div class="chart-container">
                <h3>6-Month Attendance Trend</h3>
                <div style="position: relative; width: 100%; height: 100%; flex: 1;">
                    <canvas id="trendChart" width="800" height="400"></canvas>
                </div>
            </div>

            <!-- Leave Balance -->
            <div class="leave-balance-section">
                <div class="leave-balance-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Leave Balance</h2>
                    <button onclick="openLeaveModal()" class="btn-primary" style="padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                        ➕ Request Leave
                    </button>
                </div>
                
                <?php if (!empty($leave_balances)): ?>
                    <div class="leave-balance-container">
                        <?php foreach ($leave_balances as $balance): ?>
                            <div class="leave-balance-card">
                                <div class="leave-type-name">
                                    <?php echo htmlspecialchars($balance['leave_type_name']); ?>
                                </div>
                                
                                <div class="leave-stats">
                                    <div class="stat">
                                        <div class="stat-value"><?php echo $balance['total_days']; ?></div>
                                        <div class="stat-label">Total</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value" style="color: #e74c3c;"><?php echo $balance['used_days']; ?></div>
                                        <div class="stat-label">Used</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value" style="color: #27ae60;"><?php echo $balance['remaining_days']; ?></div>
                                        <div class="stat-label">Remaining</div>
                                    </div>
                                </div>
                                
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($balance['used_days'] / $balance['total_days']) * 100; ?>%;"></div>
                                </div>
                                <div class="progress-label">
                                    <span><?php echo round(($balance['used_days'] / $balance['total_days']) * 100); ?>% Used</span>
                                    <span><?php echo round(($balance['remaining_days'] / $balance['total_days']) * 100); ?>% Available</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: white; padding: 30px; border-radius: 12px; text-align: center; color: #666; border: 2px solid #e8eef7;">
                        <p style="margin: 0; font-size: 15px;">ℹ️ No leave balance information available for this year.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Attendance -->
            <h2 style="margin-top: 40px;"> Recent Attendance</h2>
            <div class="attendance-record">
                <?php if (!empty($monthly_attendance)): ?>
                    <?php foreach (array_slice($monthly_attendance, 0, 10) as $record): ?>
                        <div class="record-item <?php echo strtolower($record['status']); ?>">
                            <strong><?php echo Helper::formatDate($record['time_in']); ?></strong><br>
                            In: <?php echo Helper::formatTime($record['time_in']); ?><br>
                            Out: <?php echo !empty($record['time_out']) ? Helper::formatTime($record['time_out']) : 'Not yet'; ?><br>
                            Status: <span style="color: <?php echo $record['status'] === 'ON_TIME' ? '#27ae60' : '#f39c12'; ?>;">
                                <strong><?php echo $record['status']; ?></strong>
                            </span><br>
                            Hours: <?php echo number_format($record['total_hours_worked'] ?? 0, 2); ?>h
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No attendance records this month.</p>
                <?php endif; ?>
            </div>

            <!-- Leave Requests History -->
            <h2 style="margin-top: 40px;">📋 My Leave Requests</h2>
            <div class="leave-requests-section">
                <?php if (!empty($leave_requests)): ?>
                    <div style="display: grid; gap: 12px;">
                        <?php foreach ($leave_requests as $req): ?>
                            <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid <?php 
                                echo $req['status'] === 'Pending' ? '#f39c12' : 
                                     ($req['status'] === 'Approved' || $req['status'] === 'Final-Approved' ? '#27ae60' : '#e74c3c'); 
                            ?>; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 8px 0; color: #333;">
                                            <?php echo htmlspecialchars($req['leave_type_name']); ?>
                                        </h4>
                                        <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">
                                            <strong>Dates:</strong> <?php echo date('M d, Y', strtotime($req['start_date'])); ?> - <?php echo date('M d, Y', strtotime($req['end_date'])); ?>
                                        </p>
                                        <p style="margin: 0 0 5px 0; color: #666; font-size: 13px;">
                                            <strong>Reason:</strong> <?php echo htmlspecialchars(substr($req['reason'], 0, 60)) . (strlen($req['reason']) > 60 ? '...' : ''); ?>
                                        </p>
                                        <p style="margin: 0; color: #999; font-size: 12px;">
                                            Submitted: <?php echo date('M d, Y h:i A', strtotime($req['created_at'])); ?>
                                        </p>
                                    </div>
                                    <span style="background: <?php 
                                        echo $req['status'] === 'Pending' ? '#fff3cd' : 
                                             ($req['status'] === 'Approved' || $req['status'] === 'Final-Approved' ? '#d4edda' : '#f8d7da'); 
                                    ?>; color: <?php 
                                        echo $req['status'] === 'Pending' ? '#856404' : 
                                             ($req['status'] === 'Approved' || $req['status'] === 'Final-Approved' ? '#155724' : '#721c24'); 
                                    ?>; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; white-space: nowrap;">
                                        <?php echo htmlspecialchars($req['status']); ?>
                                    </span>
                                </div>
                                <?php if (!empty($req['remarks'])): ?>
                                    <p style="margin: 8px 0 0 0; padding-top: 8px; border-top: 1px solid #eee; color: #666; font-size: 12px;">
                                        <strong>Remarks:</strong> <?php echo htmlspecialchars($req['remarks']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; color: #999; border: 1px solid #eee;">
                        <p>📭 No leave requests yet</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php
    // Calculate 6-month trend data for charts
    $trend_data = [];
    $trend_labels = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $trend_labels[] = date('M Y', strtotime($month . '-01'));
        
        $count = 0;
        foreach ($six_months_data as $record) {
            if (substr($record['date'], 0, 7) === $month && ($record['status'] === 'ON_TIME' || $record['status'] === 'EARLY')) {
                $count++;
            }
        }
        $trend_data[] = $count;
    }
    ?>

    <script>
        // Wait for DOM to be fully loaded and Chart.js to be available
        function initializeCharts() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                setTimeout(initializeCharts, 100);
                return;
            }

            // Check if dark mode is enabled using localStorage (handles both boolean and string)
            const darkModeValue = localStorage.getItem('darkMode');
            const isDarkMode = darkModeValue === 'true' || darkModeValue === true || document.body.classList.contains('dark-mode');
            const textColor = isDarkMode ? '#e0e0e0' : '#333333';
            const gridColor = isDarkMode ? '#404040' : '#e0e0e0';
            const legendColor = isDarkMode ? '#ffffff' : '#333333';
            const axisColor = isDarkMode ? '#ffffff' : '#333333';
            
            console.log('Initializing charts...');
            console.log('Chart.js available:', typeof Chart !== 'undefined');
            console.log('Dark Mode:', isDarkMode);

            // Custom plugin to force legend text color in dark mode
            const legendColorPlugin = {
                id: 'legendColor',
                afterDraw(chart) {
                    if (isDarkMode && chart.legend) {
                        const ctx = chart.ctx;
                        ctx.fillStyle = '#ffffff';
                    }
                }
            };

        // Attendance Percentage Chart
            try {
                const canvas1 = document.getElementById('attendanceChart');
                if (!canvas1) throw new Error('Canvas element attendanceChart not found');
                
                const ctx1 = canvas1.getContext('2d');
                const attendanceChart = new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: ['Present', 'Late', 'Absent'],
                        datasets: [{
                            data: [<?php echo $present_count; ?>, <?php echo $late_count; ?>, <?php echo max(0, $working_days - $present_count - $late_count); ?>],
                            backgroundColor: ['#27ae60', '#f39c12', '#e74c3c'],
                            borderColor: isDarkMode ? ['#1a6d42', '#b37d0f', '#a02a2a'] : ['#229954', '#d68910', '#c0392b'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 750
                        },
                        plugins: {
                            legend: { 
                                position: 'bottom',
                                labels: {
                                    color: legendColor,
                                    font: { size: 14, weight: 'bold' },
                                    padding: 15,
                                    boxWidth: 16,
                                    boxHeight: 16,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                enabled: true,
                                titleColor: isDarkMode ? '#e0e0e0' : '#333333',
                                bodyColor: isDarkMode ? '#e0e0e0' : '#333333',
                                backgroundColor: isDarkMode ? '#404040' : '#ffffff',
                                borderColor: isDarkMode ? '#606060' : '#cccccc',
                                borderWidth: 1,
                                padding: 10,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 13 }
                            }
                        }
                    },
                    plugins: [legendColorPlugin]
                });
                console.log('Attendance chart created successfully');
            } catch (e) {
                console.error('Error creating attendance chart:', e);
            }

            // 6-Month Trend Chart
            try {
                const canvas2 = document.getElementById('trendChart');
                if (!canvas2) throw new Error('Canvas element trendChart not found');
                
                const ctx2 = canvas2.getContext('2d');
                const trendChart = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($trend_labels); ?>,
                        datasets: [{
                            label: 'Days Present',
                            data: <?php echo json_encode($trend_data); ?>,
                            borderColor: isDarkMode ? '#5DADE2' : '#3498db',
                            backgroundColor: isDarkMode ? 'rgba(93, 173, 226, 0.15)' : 'rgba(52, 152, 219, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: isDarkMode ? '#5DADE2' : '#3498db',
                            pointBorderColor: isDarkMode ? '#e0e0e0' : '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 750
                        },
                        plugins: {
                            legend: { 
                                display: true,
                                labels: {
                                    color: legendColor,
                                    font: { size: 14, weight: 'bold' },
                                    padding: 15,
                                    boxWidth: 16,
                                    boxHeight: 16,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                enabled: true,
                                titleColor: isDarkMode ? '#e0e0e0' : '#333333',
                                bodyColor: isDarkMode ? '#e0e0e0' : '#333333',
                                backgroundColor: isDarkMode ? '#404040' : '#ffffff',
                                borderColor: isDarkMode ? '#606060' : '#cccccc',
                                borderWidth: 1,
                                padding: 10,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 13 }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                ticks: {
                                    color: textColor,
                                    font: { size: 12, weight: 'bold' },
                                    stepSize: 1
                                },
                                grid: {
                                    color: gridColor,
                                    drawBorder: true,
                                    borderColor: isDarkMode ? '#505050' : '#cccccc'
                                }
                            },
                            x: {
                                ticks: {
                                    color: axisColor,
                                    font: { size: 12, weight: 'bold' }
                                },
                                grid: {
                                    color: gridColor,
                                    drawBorder: true,
                                    borderColor: isDarkMode ? '#505050' : '#cccccc'
                                }
                            }
                        }
                    },
                    plugins: [legendColorPlugin]
                });
                console.log('Trend chart created successfully');
            } catch (e) {
                console.error('Error creating trend chart:', e);
            }

            // Listen for dark mode toggle and update charts
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    // Reload page after a short delay to let dark mode apply
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                });
            }
        }

        // Initialize charts when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCharts);
        } else {
            initializeCharts();
        }
    </script>

    <script>
        function exportToExcel() {
            console.log('Export button clicked!');
            // Show the modal
            const modal = document.getElementById('exportModal');
            if (modal) {
                modal.style.display = 'flex';
                console.log('Modal should be visible now');
                // Load preview
                loadPreview();
            } else {
                console.error('Modal element not found');
                alert('Error: Modal not found. Please refresh the page.');
            }
        }

        function loadPreview() {
            console.log('Loading preview...');
            fetch('export_dashboard.php?format=preview')
                .then(response => {
                    console.log('Fetch response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Got preview data:', data);
                    displayExportPreview(data);
                })
                .catch(error => {
                    console.error('Preview fetch error:', error);
                    document.getElementById('previewContent').innerHTML = '<p style="color: #e74c3c; text-align: center;"><strong>Error:</strong> ' + error.message + '</p>';
                });
        }

        function displayExportPreview(data) {
            console.log('Displaying preview');
            let html = '';
            
            // Employee Info
            html += '<div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">';
            html += '<h4 style="margin: 0 0 10px 0; color: #003d82;">👤 Employee Information</h4>';
            html += `<p><strong>Name:</strong> ${data.employee_name}</p>`;
            html += `<p><strong>ID:</strong> ${data.employee_id}</p>`;
            html += `<p><strong>Department:</strong> ${data.department}</p>`;
            html += `<p><strong>Position:</strong> ${data.position}</p>`;
            html += '</div>';
            
            // Monthly Statistics
            html += '<div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">';
            html += '<h4 style="margin: 0 0 10px 0; color: #003d82;">📊 Monthly Statistics (' + data.month_year + ')</h4>';
            html += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';
            html += `<p><strong>✓ Present:</strong> ${data.present_count} days</p>`;
            html += `<p><strong>⏱ Late:</strong> ${data.late_count} days</p>`;
            html += `<p><strong>⏰ Total Hours:</strong> ${data.total_hours}h</p>`;
            html += `<p><strong>⚡ Overtime:</strong> ${data.total_overtime}h</p>`;
            html += '</div>';
            html += '</div>';
            
            // Attendance Records Summary
            html += '<div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">';
            html += '<h4 style="margin: 0 0 10px 0; color: #003d82;">📋 Attendance Records</h4>';
            html += `<p><strong>Records:</strong> ${data.attendance_count} records</p>`;
            html += '</div>';
            
            // Leave Information
            if (data.leave_count > 0) {
                html += '<div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">';
                html += '<h4 style="margin: 0 0 10px 0; color: #003d82;">🏖️ Leave Information</h4>';
                html += `<p><strong>Requests:</strong> ${data.leave_count} records</p>`;
                html += `<p><strong>Balance Entries:</strong> ${data.balance_count}</p>`;
                html += '</div>';
            }
            
            // File Details
            html += '<div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 15px; border-radius: 6px; border-left: 4px solid #0066cc;">';
            html += '<p style="margin: 0; font-size: 13px; line-height: 1.6;">';
            html += '📁 <strong>Excel File</strong><br>';
            html += '💾 Approx. size: ~' + data.file_size_estimate + ' KB<br>';
            html += '🕒 Generated: ' + data.current_date;
            html += '</p>';
            html += '</div>';
            
            document.getElementById('previewContent').innerHTML = html;
        }

        function confirmExport() {
            console.log('Download confirmed');
            // Hide modal
            document.getElementById('exportModal').style.display = 'none';
            // Start download
            window.location.href = 'export_dashboard.php?format=excel';
        }

        function cancelExport() {
            console.log('Export cancelled');
            document.getElementById('exportModal').style.display = 'none';
        }

        // Setup modal events
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, setting up modal events');
            const modal = document.getElementById('exportModal');
            if (!modal) {
                console.warn('Modal not found on page load');
                return;
            }
            
            // Click outside to close
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    cancelExport();
                }
            });
            
            // Escape key to close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    cancelExport();
                }
            });
        });


        // Live Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>

    <!-- Preloader Management Script -->
    <script>
        // Show preloader when navigating to a link
        document.addEventListener('DOMContentLoaded', function() {
            const preloader = document.querySelector('.preloader');
            
            // Hide preloader after page load (with delay to make it visible)
            setTimeout(() => {
                if (preloader) {
                    preloader.style.display = 'none';
                }
            }, 800); // Show for 800ms

            // Show preloader on navigation links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't show preloader for logout or external links
                    const href = this.getAttribute('href');
                    if (href && !href.includes('logout') && !href.startsWith('javascript')) {
                        if (preloader) {
                            preloader.style.display = 'flex';
                            // Auto-hide after navigation loads
                            setTimeout(() => {
                                preloader.style.display = 'none';
                            }, 800);
                        }
                    }
                });
            });
        });
    </script>

    <!-- Leave Request Modal -->
    <div id="leaveRequestModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #333; font-size: 22px;">📝 Request Leave</h2>
                <button onclick="closeLeaveModal()" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #999; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
            </div>
            
            <form id="leaveRequestForm" onsubmit="submitLeaveRequest(event)" style="display: grid; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Leave Type*</label>
                    <select name="leave_type_id" required style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit;">
                        <option value="">-- Select Leave Type --</option>
                        <?php foreach ($leave_types as $type): ?>
                            <option value="<?php echo $type['leave_type_id']; ?>">
                                <?php echo htmlspecialchars($type['leave_type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Start Date*</label>
                    <input type="date" name="start_date" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">End Date*</label>
                    <input type="date" name="end_date" required min="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Reason for Leave*</label>
                    <textarea name="reason" required placeholder="Explain why you need this leave..." style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; min-height: 100px; font-family: inherit; box-sizing: border-box;"></textarea>
                </div>

                <div id="leaveMessage" style="display: none; padding: 12px; border-radius: 6px; margin-bottom: 10px; font-size: 14px;"></div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
                    <button type="button" onclick="closeLeaveModal()" style="padding: 12px 24px; background: #f0f0f0; color: #333; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s;">Cancel</button>
                    <button type="submit" id="submitLeaveBtn" style="padding: 12px 24px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.3s;">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Time In Confirmation Modal -->
    <div id="timeInConfirmModal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center;">
        <div style="background: white; padding: 40px; border-radius: 16px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 12px 48px rgba(0, 0, 0, 0.3);">
            <div style="font-size: 64px; margin-bottom: 20px;">✓</div>
            <h2 style="margin: 0 0 20px 0; color: #27ae60; font-size: 24px;">Time In Confirmation</h2>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: left;">
                <p style="margin: 0 0 12px 0; color: #666;">
                    <strong>Employee:</strong><br>
                    <span style="font-size: 16px; color: #333;" id="confirmEmployeeName"><?php echo htmlspecialchars($employee['full_name']); ?></span>
                </p>
                <p style="margin: 0 0 12px 0; color: #666;">
                    <strong>Date:</strong><br>
                    <span style="font-size: 16px; color: #333;" id="confirmDate"></span>
                </p>
                <p style="margin: 0; color: #666;">
                    <strong>Time:</strong><br>
                    <span style="font-size: 16px; color: #333;" id="confirmTime"></span>
                </p>
            </div>

            <p style="color: #27ae60; font-size: 14px; margin-bottom: 20px;">✓ Successfully timed in!</p>

            <button onclick="closeTimeInConfirm()" style="width: 100%; padding: 14px; background: #27ae60; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s;" onmouseover="this.style.background='#229954'" onmouseout="this.style.background='#27ae60'">OK</button>
        </div>
    </div>

    <!-- Time Out Confirmation Modal -->
    <div id="timeOutConfirmModal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center;">
        <div style="background: white; padding: 40px; border-radius: 16px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 12px 48px rgba(0, 0, 0, 0.3);">
            <div style="font-size: 64px; margin-bottom: 20px;">✓</div>
            <h2 style="margin: 0 0 20px 0; color: #e67e22; font-size: 24px;">Time Out Confirmation</h2>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: left;">
                <p style="margin: 0 0 12px 0; color: #666;">
                    <strong>Employee:</strong><br>
                    <span style="font-size: 16px; color: #333;" id="confirmTimeOutEmployeeName"><?php echo htmlspecialchars($employee['full_name']); ?></span>
                </p>
                <p style="margin: 0 0 12px 0; color: #666;">
                    <strong>Date:</strong><br>
                    <span style="font-size: 16px; color: #333;" id="confirmTimeOutDate"></span>
                </p>
                <p style="margin: 0; color: #666;">
                    <strong>Time:</strong><br>
                    <span style="font-size: 16px; color: #333;" id="confirmTimeOutTime"></span>
                </p>
            </div>

            <p style="color: #e67e22; font-size: 14px; margin-bottom: 20px;">✓ Successfully timed out!</p>

            <button onclick="closeTimeOutConfirm()" style="width: 100%; padding: 14px; background: #e67e22; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s;" onmouseover="this.style.background='#d35400'" onmouseout="this.style.background='#e67e22'">OK</button>
        </div>
    </div>

    <script>
        // Leave Request Modal Functions
        function openLeaveModal() {
            document.getElementById('leaveRequestModal').style.display = 'flex';
        }

        function closeLeaveModal() {
            document.getElementById('leaveRequestModal').style.display = 'none';
            document.getElementById('leaveRequestForm').reset();
            document.getElementById('leaveMessage').style.display = 'none';
        }

        function submitLeaveRequest(event) {
            event.preventDefault();
            
            const form = document.getElementById('leaveRequestForm');
            const messageDiv = document.getElementById('leaveMessage');
            const submitBtn = document.getElementById('submitLeaveBtn');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Submitting...';
            
            const formData = new FormData(form);
            formData.append('action', 'submit_leave');
            
            fetch('employee_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#d4edda';
                    messageDiv.style.color = '#155724';
                    messageDiv.textContent = '✓ ' + data.message;
                    
                    // Reset form after 2 seconds and close
                    setTimeout(() => {
                        form.reset();
                        closeLeaveModal();
                        location.reload(); // Reload to show new request
                    }, 2000);
                } else {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.textContent = '✘ ' + data.message;
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Request';
                }
            })
            .catch(error => {
                messageDiv.style.display = 'block';
                messageDiv.style.background = '#f8d7da';
                messageDiv.style.color = '#721c24';
                messageDiv.textContent = '✘ Error: ' + error.message;
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Request';
            });
        }

        // Time In Confirmation Modal Functions
        function showTimeInConfirmation() {
            const now = new Date();
            const date = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            
            document.getElementById('confirmDate').textContent = date;
            document.getElementById('confirmTime').textContent = time;
            document.getElementById('timeInConfirmModal').style.display = 'flex';
        }

        function closeTimeInConfirm() {
            document.getElementById('timeInConfirmModal').style.display = 'none';
            location.reload(); // Reload dashboard
        }

        // Time Out Confirmation Modal Functions
        function showTimeOutConfirmation() {
            const now = new Date();
            const date = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            
            document.getElementById('confirmTimeOutDate').textContent = date;
            document.getElementById('confirmTimeOutTime').textContent = time;
            document.getElementById('timeOutConfirmModal').style.display = 'flex';
        }

        function closeTimeOutConfirm() {
            document.getElementById('timeOutConfirmModal').style.display = 'none';
            location.reload(); // Reload dashboard
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const leaveModal = document.getElementById('leaveRequestModal');
            if (event.target === leaveModal) {
                closeLeaveModal();
            }
        });
    </script>
    <div id="exportModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); justify-content: center; align-items: center;">
        <div style="background: var(--bg-primary); padding: 30px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: var(--text-primary); font-size: 20px;">📊 Export Dashboard Data</h2>
                <button onclick="cancelExport()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-secondary); padding: 0; width: 30px; height: 30px;">×</button>
            </div>
            
            <div id="previewContent" style="margin-bottom: 20px; color: var(--text-primary);">
                <p style="text-align: center; color: var(--text-secondary);">Loading preview...</p>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button onclick="cancelExport()" style="padding: 10px 20px; background: var(--light-bg); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s;" onmouseover="this.style.background='#ddd';" onmouseout="this.style.background='var(--light-bg)';">Cancel</button>
                <button onclick="confirmExport()" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s;" onmouseover="this.style.background='#004fa3';" onmouseout="this.style.background='#0066cc';">⬇ Download Excel</button>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // Show menu toggle on mobile
            function checkMenuToggle() {
                if (window.innerWidth <= 768) {
                    menuToggle.style.display = 'block';
                } else {
                    menuToggle.style.display = 'none';
                    if (sidebar) sidebar.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                }
            }

            // Toggle sidebar
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar) sidebar.classList.toggle('active');
                if (overlay) overlay.classList.toggle('active');
            });

            // Close sidebar when overlay is clicked
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (sidebar) sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            // Close sidebar when a menu item is clicked
            const menuItems = document.querySelectorAll('.sidebar a');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (sidebar) sidebar.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                });
            });

            // Close sidebar when clicking on main content
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.addEventListener('click', function() {
                    if (sidebar && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                        if (overlay) overlay.classList.remove('active');
                    }
                });
            }

            // Check on load
            checkMenuToggle();

            // Check on resize
            window.addEventListener('resize', checkMenuToggle);
        });
    </script>
</body>
</html>
