<?php
/**
 * Employee Personal Dashboard
 * Shows individual employee attendance stats, leave balance, and performance
 * 
 * NOTE: All PHP processing (authentication, database queries, etc.) is now handled
 * by TimeAttendancePortalController->index() BEFORE this file is included.
 * This file now only contains HTML display code.
 * 
 * Variables available from controller:
 * - $employee: Employee record from database
 * - $employee_id: Employee ID
 * - $conn: Database connection
 * - $statusInfo: Today's attendance status
 * - $leave_types: Available leave types
 * - $working_days, $present_count, $late_count: Monthly stats
 */

// Set Philippines timezone for consistency
date_default_timezone_set('Asia/Manila');

// Variables are now pre-set by the controller, no need to query them again
?>

<?php
// Initialize AttendanceTracker for dual recording support
$attendanceTracker = new AttendanceTracker($conn);

// Function to get today's attendance status (including approval status)
function getTodayAttendanceStatus($conn, $employee_id) {
    // Use explicit date comparison to handle timezone issues
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT time_in, time_out, status, is_approved 
        FROM ta_attendance 
        WHERE employee_id = ? AND DATE(attendance_date) = ?
        LIMIT 1
    ");
    $stmt->execute([$employee_id, $today]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Determine approval display status
        $approvalStatus = 'Pending Approval';
        $approvalClass = 'badge-warning';
        
        if ($result['is_approved'] == 1) {
            $approvalStatus = 'Approved';
            $approvalClass = 'badge-success';
        } elseif ($result['status'] === 'PENDING_APPROVAL') {
            $approvalStatus = 'Awaiting Admin Approval';
            $approvalClass = 'badge-info';
        }
        
        return [
            'time_in' => $result['time_in'],
            'time_out' => $result['time_out'],
            'status' => $result['status'],
            'is_approved' => $result['is_approved'],
            'approval_status' => $approvalStatus,
            'approval_class' => $approvalClass,
            'duration' => ($result['time_in'] && $result['time_out']) ? 
                calculateDuration($result['time_in'], $result['time_out']) : null
        ];
    }
    
    return ['time_in' => null, 'time_out' => null, 'status' => null, 'is_approved' => 0, 
            'approval_status' => 'Not Recorded', 'approval_class' => 'badge-secondary', 'duration' => null];
}

function calculateDuration($timeIn, $timeOut) {
    $start = strtotime($timeIn);
    $end = strtotime($timeOut);
    $diff = $end - $start;
    $hours = floor($diff / 3600);
    $mins = floor(($diff % 3600) / 60);
    return sprintf("%d:%02d", $hours, $mins);
}

// Helper functions to replace Helper:: calls
function formatTime($time) {
    if (empty($time)) return '--:--';
    return date('H:i', strtotime($time));
}

function formatDate($datetime) {
    if (empty($datetime)) return '--';
    return date('M d, Y', strtotime($datetime));
}

function calculateWorkingDays($startDate, $endDate) {
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
}

// Get today's status
$statusInfo = getTodayAttendanceStatus($conn, $employee_id);


// Initialize message variables
$message = "";
$messageType = ""; // success, error, info, warning

// Check for error parameter
if (isset($_GET['error'])) {
    $message = $_GET['error'];
    $messageType = "error";
    
    // If there's an error message parameter, append it
    if (isset($_GET['msg'])) {
        $message .= ': ' . urldecode($_GET['msg']);
    }
}

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

// Check for QR pending confirmation (from QR scan)
$qr_pending = null;
if (isset($_SESSION['qr_pending'])) {
    $qr_pending = $_SESSION['qr_pending'];
    // Note: We'll clear this after the modal is shown via JavaScript
    // This ensures it's available for the first page load with the modal
}

// Note: Form submission for time in/out is now handled by the controller (TimeAttendancePortalController)
// The controller processes the request and sets session variables for the modal display


// Get attendance period - from hire date to today (not just current month)
$query_employee = "SELECT date_hired FROM employees WHERE employee_id = ?";
$stmt_emp = $conn->prepare($query_employee);
$stmt_emp->execute([$employee_id]);
$emp_data = $stmt_emp->fetch(PDO::FETCH_ASSOC);

$attendance_start = $emp_data['date_hired'] ?? date('Y-01-01');
// Use today as end date - allows viewing all attendance up to now
$attendance_end = date('Y-m-d');

// Get current month data for display purposes
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');

// Get attendance for all time (from hire to today)
$query = "SELECT * FROM ta_attendance 
          WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?
          ORDER BY time_in DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$employee_id, $attendance_start, $attendance_end]);
$monthly_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats for current month only (for monthly statistics)
$current_month_attendance = array_filter($monthly_attendance, function($record) use ($current_month_start, $current_month_end) {
    $record_date = date('Y-m-d', strtotime($record['time_in']));
    return $record_date >= $current_month_start && $record_date <= $current_month_end;
});

// Calculate monthly stats
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

// Get last 6 months data
$six_months_ago = date('Y-m-d', strtotime('-6 months'));
$query_six = "SELECT DATE(time_in) as date, status, total_hours_worked 
              FROM ta_attendance 
              WHERE employee_id = ? AND attendance_date >= ?
              ORDER BY time_in DESC";
$stmt_six = $conn->prepare($query_six);
$stmt_six->execute([$employee_id, $six_months_ago]);
$six_months_data = $stmt_six->fetchAll(PDO::FETCH_ASSOC);

// Get leave balance - Enhanced query to handle missing records
$current_year = date('Y');
$query_balance = "SELECT lt.leave_type_id, lt.leave_type_name, lt.is_deductible, lt.days_per_year,
                         lb.leave_balance_id,
                         COALESCE(lb.opening_balance, lt.days_per_year) as total_days,
                         COALESCE(lb.used_balance, 0) as used_days,
                         COALESCE(lb.remaining_balance, lt.days_per_year) as remaining_days
                  FROM ta_leave_types lt
                  LEFT JOIN ta_leave_balances lb ON lb.employee_id = ? AND lb.leave_type_id = lt.leave_type_id AND lb.year = ?
                  WHERE lt.is_deductible = 1
                  ORDER BY lt.leave_type_id";
$stmt_balance = $conn->prepare($query_balance);
$stmt_balance->execute([$employee_id, $current_year]);
$leave_balances = $stmt_balance->fetchAll(PDO::FETCH_ASSOC);

// Auto-create missing balance records for deductible leave types
foreach ($leave_balances as $balance) {
    if ($balance['leave_balance_id'] === null) {
        // Create missing balance record
        $create_balance = "INSERT INTO ta_leave_balances 
                          (employee_id, leave_type_id, year, opening_balance, used_balance, remaining_balance, created_at, updated_at)
                          VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $create_stmt = $conn->prepare($create_balance);
        $create_stmt->execute([
            $employee_id,
            $balance['leave_type_id'],
            $current_year,
            $balance['days_per_year'],
            0,
            $balance['days_per_year']
        ]);
    }
}

// Calculate attendance percentage
$working_days = Helper::calculateWorkingDays($current_month_start, $current_month_end);
$attendance_percentage = $working_days > 0 ? ($present_count / $working_days) * 100 : 0;

// Get today's assigned shift
$today = date('Y-m-d');
$today_shift = null;
// Note: Shift query disabled - using data from attendance records instead
// If needed, query ta_employee_shifts directly

// Get leave requests history
$query_requests = "SELECT lr.*, lt.leave_type_name 
                   FROM ta_leave_requests lr
                   JOIN ta_leave_types lt ON lr.leave_type_id = lt.leave_type_id
                   WHERE lr.employee_id = ?
                   ORDER BY lr.date_submitted DESC LIMIT 10";
$stmt_requests = $conn->prepare($query_requests);
$stmt_requests->execute([$employee_id]);
$leave_requests = $stmt_requests->fetchAll(PDO::FETCH_ASSOC);

// Get all leave types for form
$query_types = "SELECT * FROM ta_leave_types";
$stmt_types = $conn->prepare($query_types);
$stmt_types->execute();
$leave_types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- CSS and JS are loaded by layout.php -->
<!-- Employee Dashboard Content -->
<div style="display: flex; align-items: center; margin-bottom: 20px;">
    <div class="live-clock" id="liveClock">00:00:00</div>
</div>

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
                $time_in_recorded = $_SESSION['time_in_recorded'] ?? date('H:i:s');
                $time_in_date = $_SESSION['time_in_date'] ?? date('l, F j, Y');
                unset($_SESSION['show_time_in_confirm']);
                unset($_SESSION['time_in_recorded']);
                unset($_SESSION['time_in_date']);
            ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showTimeInConfirmation('<?php echo htmlspecialchars($time_in_date); ?>', '<?php echo htmlspecialchars($time_in_recorded); ?>');
                    });
                </script>
            <?php endif; ?>

            <?php 
            if (isset($_SESSION['show_time_out_confirm'])): 
                $time_out_recorded = $_SESSION['time_out_recorded'] ?? date('H:i:s');
                $time_out_date = $_SESSION['time_out_date'] ?? date('l, F j, Y');
                unset($_SESSION['show_time_out_confirm']);
                unset($_SESSION['time_out_recorded']);
                unset($_SESSION['time_out_date']);
            ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showTimeOutConfirmation('<?php echo htmlspecialchars($time_out_date); ?>', '<?php echo htmlspecialchars($time_out_recorded); ?>');
                    });
                </script>
            <?php endif; ?>

            <?php 
            // Handle QR pending confirmation
            if ($qr_pending): 
                $qr_action = $qr_pending['action_type'];
                $qr_message = $qr_pending['message'];
                $qr_time = $qr_pending['current_time'];
            ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Show QR confirmation modal
                        showConfirmationModal('<?php echo htmlspecialchars($qr_action); ?>', '<?php echo htmlspecialchars($qr_message); ?>');
                        
                        // Update modal to show correct time
                        const currentTimeEl = document.getElementById('currentTime');
                        if (currentTimeEl) {
                            currentTimeEl.textContent = '<?php echo htmlspecialchars($qr_time); ?>';
                        }
                        
                        // Mark this as QR confirmation so form knows to use AJAX
                        const form = document.getElementById('timeActionForm');
                        if (form) {
                            form.dataset.qrConfirmation = 'true';
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- Time In/Out Action Section -->
            <div class="time-action-section">
                <div class="time-action-header">
                    <h3> Time In/Out</h3>
                    <span><?php echo date('l, F j, Y'); ?></span>
                </div>
                
                <!-- Debug: Check statusInfo value -->
                <?php if (isset($statusInfo) && !empty($statusInfo['time_in'])): ?>
                    <script>console.log('statusInfo found:', <?php echo json_encode($statusInfo); ?>);</script>
                <?php else: ?>
                    <script>console.log('statusInfo empty:', <?php echo json_encode($statusInfo ?? 'NOT SET'); ?>);</script>
                <?php endif; ?>
                
                <div class="time-status">
                    <div class="time-status-item">
                        <div class="time-status-label">Time In</div>
                        <div class="time-status-value">
                            <?php 
                                $time_in_display = '--:--';
                                if (isset($statusInfo)) {
                                    error_log("statusInfo set: " . json_encode($statusInfo));
                                    if (!empty($statusInfo['time_in'])) {
                                        $time_in_display = Helper::formatTime($statusInfo['time_in']);
                                        error_log("time_in found: " . $statusInfo['time_in'] . " formatted as: " . $time_in_display);
                                    }
                                } else {
                                    error_log("statusInfo NOT set");
                                }
                                echo $time_in_display;
                            ?>
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

                    <!-- Approval Status Badge -->
                    <div class="time-status-item">
                        <div class="time-status-label">Status</div>
                        <div class="time-status-value">
                            <span class="badge <?php echo $statusInfo['approval_class']; ?>">
                                <?php echo $statusInfo['approval_status']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <form method="POST" class="time-action-buttons" id="timeActionForm">
                    <?php if (empty($statusInfo['time_in'])): ?>
                        <button type="button" class="btn-time-action btn-time-in" onclick="showConfirmationModal('time_in', 'Time In')">
                            Timed In
                        </button>
                        <span style="align-self: center; opacity: 0.9;"> Waiting for Time in</span>
                    <?php elseif (empty($statusInfo['time_out'])): ?>
                        <button type="button" class="btn-time-action btn-time-out" onclick="showConfirmationModal('time_out', 'Time Out')">
                            Timed Out
                        </button>
                        <span style="align-self: center; opacity: 0.9;"> Already timed in</span>
                    <?php else: ?>
                        <button type="submit" class="btn-time-action" disabled>
                            Time In Completed
                        </button>
                        <button type="submit" class="btn-time-action" disabled>
                            Time Out Completed
                        </button>
                    <?php endif; ?>
                    <input type="hidden" name="action" id="actionInput" value="">
                </form>

                <!-- Confirmation Modal -->
                <div id="confirmationModal" class="modal-overlay" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Confirm <span id="actionText"></span></h2>
                            <button class="modal-close" onclick="closeConfirmationModal()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <span id="actionTextBody" style="font-weight: bold;"></span>?</p>
                            <p style="color: #666; font-size: 13px; margin-top: 10px;">
                                Current time: <strong id="currentTime"></strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeConfirmationModal()" style="-webkit-user-select: none; user-select: none;">Cancel</button>
                            <button type="button" class="btn-confirm" id="confirmButton" style="-webkit-user-select: none; user-select: none;" ontouchstart="this.style.opacity='0.8'" ontouchend="this.style.opacity='1'" onclick="confirmTimeAction()">Yes, Confirm</button>
                        </div>
                    </div>
                </div>

                <!-- DEBUG ERROR DISPLAY (On-Page, visible without console) -->
                <div id="debugErrorModal" class="modal-overlay" style="display: none; z-index: 10000;">
                    <div class="modal-content" style="max-height: 80vh; overflow-y: auto; background: #fff3cd; border: 2px solid #ff6b6b;">
                        <div class="modal-header" style="background: #ff6b6b; color: white;">
                            <h2>⚠ Debug Information</h2>
                            <button class="modal-close" onclick="closeDebugModal()" style="color: white; font-size: 28px;">&times;</button>
                        </div>
                        <div class="modal-body" style="padding: 20px;">
                            <div id="debugContent" style="font-family: monospace; font-size: 12px; white-space: pre-wrap; word-break: break-all; background: #f0f0f0; padding: 15px; border-radius: 4px; color: #333;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-confirm" onclick="closeDebugModal()" style="width: 100%;">Close</button>
                            <button type="button" class="btn-cancel" onclick="copyDebugInfo()" style="width: 100%; margin-top: 10px;">Copy to Clipboard</button>
                        </div>
                    </div>
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
                                            <strong>Reason:</strong> <?php 
                                                $details = $req['details'] ?? 'N/A';
                                                echo htmlspecialchars(substr($details, 0, 60)) . (strlen($details) > 60 ? '...' : ''); 
                                            ?>
                                        </p>
                                        <p style="margin: 0; color: #999; font-size: 12px;">
                                            Submitted: <?php 
                                                $created = $req['created_at'] ?? null;
                                                echo $created ? date('M d, Y h:i A', strtotime($created)) : 'N/A'; 
                                            ?>
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
    $trend_data = [];
    
    // Safety check: ensure $six_months_data is available
    if (!isset($six_months_data)) {
        $six_months_data = [];
    }
    
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $trend_labels[] = date('M Y', strtotime($month . '-01'));
        
        $count = 0;
        foreach ($six_months_data as $record) {
            if (!empty($record['date']) && substr($record['date'], 0, 7) === $month && ($record['status'] === 'ON_TIME' || $record['status'] === 'EARLY')) {
                $count++;
            }
        }
        $trend_data[] = $count;
    }
    ?>

    <style>
        /* Confirmation Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            padding: 20px;
            box-sizing: border-box;
            pointer-events: none;
        }

        .modal-overlay.show {
            display: flex !important;
            pointer-events: auto;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease-out;
            pointer-events: auto;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 22px;
            color: #003d82;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-body {
            padding: 25px;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .modal-body p {
            margin: 0 0 15px 0;
        }

        .modal-body p:last-child {
            margin-bottom: 0;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-cancel, .btn-confirm {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
            pointer-events: auto;
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .btn-cancel:active, .btn-cancel[data-touched="true"] {
            background: #d0d0d0;
            opacity: 0.8;
        }

        .btn-confirm {
            background: #27ae60;
            color: white;
        }

        .btn-confirm:hover {
            background: #229954;
        }

        .btn-confirm:active, .btn-confirm[data-touched="true"] {
            background: #1e8449;
            transform: scale(0.98);
            opacity: 0.8;
        }

        /* Mobile Responsive Modal */
        @media (max-width: 600px) {
            .modal-overlay {
                padding: 15px;
                align-items: flex-end;
            }

            .modal-content {
                max-width: 100%;
                width: 100%;
                border-radius: 12px 12px 0 0;
                margin-bottom: 0;
                max-height: 90vh;
            }

            .modal-header {
                padding: 20px;
            }

            .modal-header h2 {
                font-size: 20px;
            }

            .modal-body {
                padding: 20px;
            }

            .modal-footer {
                padding: 15px 20px;
                gap: 10px;
            }

            .btn-cancel, .btn-confirm {
                flex: 1;
                padding: 14px 16px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                max-height: 85vh;
            }

            .modal-header h2 {
                font-size: 18px;
            }

            .modal-header {
                padding: 15px;
            }

            .modal-body {
                padding: 15px;
                font-size: 14px;
            }

            .modal-footer {
                padding: 12px 15px;
                flex-direction: column;
            }

            .btn-cancel, .btn-confirm {
                width: 100%;
            }
        }

        /* Approval Status Badge Styles */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
    </style>

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
            const fetchUrl = window.location.protocol + '//' + window.location.host + '/capstone_hr_management_system/employee_portal/time_attendance_portal/export_dashboard.php?format=preview';
            fetch(fetchUrl)
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

            // Enhanced Metrics
            html += '<div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">';
            html += '<h4 style="margin: 0 0 10px 0; color: #003d82;">📈 Performance Metrics</h4>';
            html += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';
            html += `<div style="background: #f0f4f8; padding: 10px; border-radius: 4px;">`;
            html += `<p style="margin: 0; font-size: 0.9em; color: #666;">Attendance Rate</p>`;
            html += `<p style="margin: 5px 0 0 0; font-size: 1.5em; color: #003d82; font-weight: bold;">${data.metrics?.attendance_rate ?? 0}%</p>`;
            html += `</div>`;
            html += `<div style="background: #f0f4f8; padding: 10px; border-radius: 4px;">`;
            html += `<p style="margin: 0; font-size: 0.9em; color: #666;">Absence Rate</p>`;
            html += `<p style="margin: 5px 0 0 0; font-size: 1.5em; color: #d9534f; font-weight: bold;">${data.metrics?.absence_rate ?? 0}%</p>`;
            html += `</div>`;
            html += `<div style="background: #f0f4f8; padding: 10px; border-radius: 4px;">`;
            html += `<p style="margin: 0; font-size: 0.9em; color: #666;">Punctuality Score</p>`;
            html += `<p style="margin: 5px 0 0 0; font-size: 1.5em; color: #5cb85c; font-weight: bold;">${data.metrics?.punctuality_score ?? 0}/100</p>`;
            html += `</div>`;
            html += `<div style="background: #f0f4f8; padding: 10px; border-radius: 4px;">`;
            html += `<p style="margin: 0; font-size: 0.9em; color: #666;">Overall Performance</p>`;
            html += `<p style="margin: 5px 0 0 0; font-size: 1.5em; color: #0275d8; font-weight: bold;">${data.metrics?.overall_performance_score ?? 0}/100</p>`;
            html += `</div>`;
            html += '</div>';
            
            // Late Minutes and Overtime Frequency
            html += '<div style="margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';
            html += `<div style="background: #f0f4f8; padding: 10px; border-radius: 4px;">`;
            html += `<p style="margin: 0; font-size: 0.9em; color: #666;">Late Incidents</p>`;
            html += `<p style="margin: 5px 0 0 0; font-size: 1.5em; color: #ff9800; font-weight: bold;">${data.punctuality?.total_late_incidents ?? 0}</p>`;
            html += `</div>`;
            html += `<div style="background: #f0f4f8; padding: 10px; border-radius: 4px;">`;
            html += `<p style="margin: 0; font-size: 0.9em; color: #666;">Overtime Frequency</p>`;
            html += `<p style="margin: 5px 0 0 0; font-size: 1.5em; color: #6f42c1; font-weight: bold;">${data.overtime?.overtime_frequency_rating ?? 'LOW'}</p>`;
            html += `</div>`;
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
            const exportUrl = window.location.protocol + '//' + window.location.host + '/capstone_hr_management_system/employee_portal/time_attendance_portal/export_dashboard.php?format=excel';
            window.location.href = exportUrl;
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
                    <select name="leave_type_id" id="leaveTypeSelect" required onchange="updateLeaveBalance()" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit;">
                        <option value="">-- Select Leave Type --</option>
                        <?php foreach ($leave_types as $type): ?>
                            <option value="<?php echo $type['leave_type_id']; ?>" data-is-deductible="<?php echo $type['is_deductible']; ?>">
                                <?php echo htmlspecialchars($type['leave_type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="leaveBalanceInfo" style="margin-top: 10px; padding: 10px; background: #e8f5e9; border-radius: 6px; display: none; font-size: 13px;">
                        <strong style="color: #27ae60;">Available Balance:</strong> <span id="balanceDisplay">-</span> days
                    </div>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Start Date*</label>
                    <input type="date" name="start_date" id="startDate" required min="<?php echo date('Y-m-d'); ?>" onchange="calculateLeaveDays()" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">End Date*</label>
                    <input type="date" name="end_date" id="endDate" required min="<?php echo date('Y-m-d'); ?>" onchange="calculateLeaveDays()" style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div id="leaveDaysInfo" style="display: none; padding: 10px; background: #fff3e0; border-radius: 6px; border-left: 4px solid #ff9800;">
                    <strong style="color: #e65100;">Total Days:</strong> <span id="totalDaysDisplay">0</span> days
                    <br><small style="color: #666;">(Weekdays will be calculated on approval)</small>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Reason for Leave*</label>
                    <textarea name="details" required placeholder="Explain why you need this leave..." style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; min-height: 100px; font-family: inherit; box-sizing: border-box;"></textarea>
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
        // Leave balance data from PHP (populate during page load)
        const leaveBalanceData = <?php echo json_encode(array_column($leave_balances, null, 'leave_type_id')); ?>;
        
        // Leave Request Modal Functions
        function openLeaveModal() {
            document.getElementById('leaveRequestModal').style.display = 'flex';
            // Reset form fields
            document.getElementById('leaveRequestForm').reset();
            document.getElementById('leaveMessage').style.display = 'none';
            document.getElementById('leaveBalanceInfo').style.display = 'none';
            document.getElementById('leaveDaysInfo').style.display = 'none';
        }

        function closeLeaveModal() {
            document.getElementById('leaveRequestModal').style.display = 'none';
            document.getElementById('leaveRequestForm').reset();
            document.getElementById('leaveMessage').style.display = 'none';
        }

        // Update leave balance display when leave type is selected
        function updateLeaveBalance() {
            const leaveTypeSelect = document.getElementById('leaveTypeSelect');
            const leaveTypeId = leaveTypeSelect.value;
            const balanceInfo = document.getElementById('leaveBalanceInfo');
            const balanceDisplay = document.getElementById('balanceDisplay');
            
            if (!leaveTypeId) {
                balanceInfo.style.display = 'none';
                return;
            }
            
            const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
            const isDeductible = selectedOption.getAttribute('data-is-deductible');
            
            if (isDeductible == 1 && leaveBalanceData[leaveTypeId]) {
                const balance = leaveBalanceData[leaveTypeId];
                balanceDisplay.textContent = Math.max(0, balance.remaining_days).toFixed(2);
                balanceInfo.style.display = 'block';
            } else if (isDeductible == 1) {
                balanceDisplay.textContent = 'N/A (No balance record)';
                balanceInfo.style.display = 'block';
            } else {
                balanceInfo.style.display = 'none';
            }
        }

        // Calculate and display number of leave days
        function calculateLeaveDays() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const leaveDaysInfo = document.getElementById('leaveDaysInfo');
            const totalDaysDisplay = document.getElementById('totalDaysDisplay');
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end date
                
                totalDaysDisplay.textContent = diffDays;
                leaveDaysInfo.style.display = 'block';
            } else {
                leaveDaysInfo.style.display = 'none';
            }
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
            
            // Use absolute path to ensure correct file resolution
            const fetchUrl = window.location.protocol + '//' + window.location.host + '/capstone_hr_management_system/employee_portal/time_attendance_portal/employee_dashboard.php';
            
            fetch(fetchUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#d4edda';
                    messageDiv.style.color = '#155724';
                    
                    // Show detailed success message with balance info
                    let successMsg = '✓ ' + data.message;
                    if (data.is_deductible) {
                        successMsg += `\n📊 Leave Details:\n` +
                                    `Days Requested: ${data.days_requested}\n` +
                                    `Holiday Count: ${data.holiday_count}\n` +
                                    `Working Days: ${data.working_days}\n` +
                                    `New Balance: ${data.new_balance?.toFixed(2)} days`;
                        messageDiv.style.whiteSpace = 'pre-line';
                    }
                    messageDiv.textContent = successMsg;
                    
                    // Reset form after 2 seconds and close
                    setTimeout(() => {
                        form.reset();
                        closeLeaveModal();
                        location.reload(); // Reload to show new request
                    }, 3000);
                } else {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.style.whiteSpace = 'pre-wrap';
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
        function showTimeInConfirmation(date, time) {
            document.getElementById('confirmDate').textContent = date || 'N/A';
            document.getElementById('confirmTime').textContent = time || 'N/A';
            document.getElementById('timeInConfirmModal').style.display = 'flex';
        }

        function closeTimeInConfirm() {
            document.getElementById('timeInConfirmModal').style.display = 'none';
        }

        // Time Out Confirmation Modal Functions
        function showTimeOutConfirmation(date, time) {
            document.getElementById('confirmTimeOutDate').textContent = date || 'N/A';
            document.getElementById('confirmTimeOutTime').textContent = time || 'N/A';
            document.getElementById('timeOutConfirmModal').style.display = 'flex';
        }

        function closeTimeOutConfirm() {
            document.getElementById('timeOutConfirmModal').style.display = 'none';
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
        // Time Action Confirmation Modal
        let pendingAction = null;

        function showConfirmationModal(action, actionLabel) {
            pendingAction = action;
            const modal = document.getElementById('confirmationModal');
            const actionText = document.getElementById('actionText');
            const actionTextBody = document.getElementById('actionTextBody');
            const currentTime = document.getElementById('currentTime');
            
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            });
            
            actionText.textContent = actionLabel;
            actionTextBody.textContent = actionLabel.toLowerCase();
            currentTime.textContent = timeString;
            
            modal.style.display = 'flex';
            modal.classList.add('show');
        }

        function closeConfirmationModal() {
            const modal = document.getElementById('confirmationModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            pendingAction = null;
        }

        function confirmTimeAction() {
            if (!pendingAction) {
                console.error('[QR-DEBUG] No pending action');
                showDebugModal('ERROR: No pending action selected');
                return;
            }
            
            console.log('[QR-DEBUG] Pending action:', pendingAction);
            console.log('[QR-DEBUG] Browser info:', navigator.userAgent);
            console.log('[QR-DEBUG] Device:', navigator.maxTouchPoints > 0 ? 'MOBILE/TOUCH' : 'DESKTOP');
            
            const form = document.getElementById('timeActionForm');
            const actionInput = document.getElementById('actionInput');
            const confirmBtn = document.getElementById('confirmButton');
            
            if (!form) {
                console.error('[QR-ERROR] Form not found!');
                showDebugModal('ERROR: Form #timeActionForm not found on page!\n\nPlease refresh the page and try again.');
                return;
            }
            
            console.log('[QR-DEBUG] Form found:', !!form);
            console.log('[QR-DEBUG] Form data-qrConfirmation:', form?.dataset?.qrConfirmation);
            
            // Show visual feedback immediately
            if (confirmBtn) confirmBtn.disabled = true;
            if (confirmBtn) confirmBtn.textContent = 'Processing...';
            
            // Store the action locally before closing modal (which sets pendingAction = null)
            const actionToPerform = pendingAction;
            
            actionInput.value = actionToPerform;
            
            closeConfirmationModal();
            
            // Check if this is a QR confirmation
            if (form.dataset.qrConfirmation === 'true') {
                console.log('[QR-DEBUG] QR confirmation detected, using AJAX');
                
                // For QR confirmations, submit via AJAX to the controller
                const formData = new FormData(form);
                formData.append('action', actionToPerform);  // Use stored action, not pendingAction which is now null!
                formData.append('qr_confirm', 'true');
                formData.append('method', 'QR');  // Explicitly set method for QR
                
                // Use a dedicated AJAX handler endpoint
                const fetchUrl = window.location.protocol + '//' + window.location.host + '/capstone_hr_management_system/employee_portal/api/attendance-confirm.php';
                
                console.log('[QR-DEBUG] Fetch URL:', fetchUrl);
                console.log('[QR-DEBUG] Action:', actionToPerform);
                
                // Store for debugging
                const debugInfo = {
                    url: fetchUrl,
                    action: actionToPerform,
                    timestamp: new Date().toISOString()
                };
                
                fetch(fetchUrl, {
                    method: 'POST',
                    body: formData,
                    mode: 'same-origin',
                    cache: 'no-cache',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('[QR-DEBUG] Response status:', response.status);
                    debugInfo.status = response.status;
                    debugInfo.statusText = response.statusText;
                    debugInfo.contentType = response.headers.get('content-type');
                    
                    return response.text();
                })
                .then(text => {
                    console.log('[QR-DEBUG] Response text:', text.substring(0, 200));
                    debugInfo.rawResponse = text.substring(0, 500);
                    
                    // Store debug info for later viewing
                    localStorage.setItem('lastAttendanceRequest', JSON.stringify(debugInfo));
                    
                    // Check if response looks like HTML
                    if (text.trim().startsWith('<') || text.includes('<!doctype')) {
                        const errorMsg = 'Server returned HTML instead of JSON.\n\n' +
                                        'Response preview:\n' + text.substring(0, 200);
                        console.error('[QR-ERROR] HTML response:', text);
                        if (confirmBtn) {
                            confirmBtn.disabled = false;
                            confirmBtn.textContent = 'Yes, Confirm';
                        }
                        showDebugModal(errorMsg + '\n\nThis usually means the server returned an error page instead of JSON.');
                        return;
                    }
                    
                    try {
                        const data = JSON.parse(text);
                        console.log('[QR-DEBUG] Parsed JSON:', data);
                        debugInfo.success = data.success;
                        debugInfo.message = data.message;
                        localStorage.setItem('lastAttendanceRequest', JSON.stringify(debugInfo));
                        
                        if (data.success) {
                            // Show success
                            console.log('[QR-SUCCESS] Time action recorded:', data.message);
                            alert('✓ ' + (data.message || 'Success'));
                            // Reload to refresh the page
                            window.location.href = window.location.protocol + '//' + window.location.host + '/capstone_hr_management_system/employee_portal/index.php?url=time-attendance';
                        } else {
                            console.error('[QR-ERROR] Server error:', data.message);
                            if (confirmBtn) {
                                confirmBtn.disabled = false;
                                confirmBtn.textContent = 'Yes, Confirm';
                            }
                            // Show error message as simple alert (not debug modal)
                            alert('✗ ' + (data.message || 'Unknown error'));
                        }
                    } catch (e) {
                        const errorMsg = 'Failed to parse server response.\n\n' +
                                        'Error: ' + e.message + '\n\n' +
                                        'Response preview:\n' + text.substring(0, 150);
                        console.error('[QR-ERROR] JSON parse error:', e, text);
                        if (confirmBtn) {
                            confirmBtn.disabled = false;
                            confirmBtn.textContent = 'Yes, Confirm';
                        }
                        showDebugModal(errorMsg);
                    }
                })
                .catch(error => {
                    console.error('[QR-ERROR] Fetch failed:', error);
                    console.error('[QR-ERROR] Error type:', error.name);
                    console.error('[QR-ERROR] Error message:', error.message);
                    debugInfo.fetchError = error.message;
                    debugInfo.errorName = error.name;
                    localStorage.setItem('lastAttendanceRequest', JSON.stringify(debugInfo));
                    
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.textContent = 'Yes, Confirm';
                    }
                    
                    const errorMsg = 'Network request failed.\n\n' +
                                    'Error: ' + error.message + '\n\n' +
                                    'Type: ' + error.name + '\n\n' +
                                    'Check your internet connection and try again.';
                    showDebugModal(errorMsg);
                });
            } else {
                // Normal form submission
                form.submit();
            }
        }

        // DEBUG: Display error on page
        function showDebugModal(errorData) {
            const modal = document.getElementById('debugErrorModal');
            const content = document.getElementById('debugContent');
            
            let debugText = '=== DEBUG INFORMATION ===\n\n';
            debugText += 'Timestamp: ' + new Date().toISOString() + '\n\n';
            debugText += 'Browser: ' + navigator.userAgent + '\n';
            debugText += 'Is Mobile: ' + (navigator.maxTouchPoints > 0 ? 'YES' : 'NO') + '\n\n';
            
            if (typeof errorData === 'string') {
                debugText += 'ERROR:\n' + errorData + '\n\n';
            } else if (typeof errorData === 'object') {
                debugText += 'ERROR DETAILS:\n';
                for (let key in errorData) {
                    debugText += key + ': ' + JSON.stringify(errorData[key]) + '\n';
                }
                debugText += '\n';
            }
            
            // Add last stored request info
            const lastReq = localStorage.getItem('lastAttendanceRequest');
            if (lastReq) {
                debugText += 'LAST REQUEST:\n' + JSON.stringify(JSON.parse(lastReq), null, 2) + '\n\n';
            }
            
            debugText += '=== END DEBUG ===';
            
            content.textContent = debugText;
            modal.style.display = 'flex';
        }

        function closeDebugModal() {
            const modal = document.getElementById('debugErrorModal');
            modal.style.display = 'none';
        }

        function copyDebugInfo() {
            const content = document.getElementById('debugContent');
            const text = content.textContent;
            
            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Debug info copied to clipboard!');
                }).catch(err => {
                    alert('Could not copy: ' + err);
                });
            } else {
                alert('Clipboard not available on your device');
            }
        }

        // Check if form exists on page load
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('timeActionForm');
            if (!form) {
                console.error('[CRITICAL] Form #timeActionForm not found on page!');
                // Add a visible warning
                const warning = document.createElement('div');
                warning.style.cssText = 'position: fixed; top: 10px; right: 10px; background: #ff6b6b; color: white; padding: 15px; border-radius: 8px; font-weight: bold; z-index: 9999;';
                warning.textContent = '⚠ Form not found - QR confirmation may not work';
                document.body.appendChild(warning);
            }
        });

        // Close modal when clicking outside of it
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmationModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeConfirmationModal();
                    }
                });
            }

            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // NOTE: Sidebar toggle is handled by layout.php (AdminLTE)
            // The standalone dashboard's menu toggle logic is disabled here
            // to avoid conflicts with the layout.php sidebar management

            // ========== MOBILE FIX: Universal Touch Event Support ==========
            // Some mobile browsers don't properly trigger click events
            // This adds touch event fallback for all buttons
            document.addEventListener('touchstart', function(e) {
                // Make sure the touch target is a button or clickable element
                let target = e.target;
                
                // Walk up the DOM tree to find a button
                while (target && target !== document.body) {
                    if (target.tagName === 'BUTTON' || 
                        target.classList.contains('btn') ||
                        target.onclick !== null ||
                        target.hasAttribute('onclick')) {
                        
                        // Mark that we're touching this element
                        target.setAttribute('data-touched', 'true');
                        target.style.opacity = '0.8';
                        break;
                    }
                    target = target.parentElement;
                }
            }, false);

            document.addEventListener('touchend', function(e) {
                let target = e.target;
                
                // Walk up the DOM tree to find the button that was touched
                while (target && target !== document.body) {
                    if (target.hasAttribute('data-touched')) {
                        target.removeAttribute('data-touched');
                        target.style.opacity = '1';
                        
                        // Trigger click event manually if onclick attribute exists
                        if (target.onclick !== null) {
                            target.onclick.call(target, new MouseEvent('click', { bubbles: true }));
                        } else if (target.hasAttribute('onclick')) {
                            const func = new Function(target.getAttribute('onclick'));
                            func.call(target);
                        }
                        
                        // Prevent default and stop propagation to avoid double-clicks
                        e.preventDefault();
                        break;
                    }
                    target = target.parentElement;
                }
            }, false);

            // Also ensure touch events on buttons trigger their click handlers
            const buttons = document.querySelectorAll('button, [role="button"]');
            buttons.forEach(button => {
                // Ensure buttons have proper styling for touch
                if (!button.style.touchAction) {
                    button.style.touchAction = 'manipulation';
                }
            });
        });
    </script>
    <!-- Mobile-responsive and sidebar toggle are handled by layout.php -->
    <script src="/capstone_hr_management_system/employee_portal/time_attendance_portal/assets/mobile-responsive.js" defer></script>
