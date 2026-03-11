<?php
/**
 * Calendar View - Monthly Attendance Calendar
 * Displays attendance records in a calendar format with color-coding
 */

require_once '../app/config/Database.php';
require_once '../app/core/Session.php';
require_once '../app/models/Attendance.php';
require_once '../app/models/Employee.php';
require_once '../app/controllers/AuthController.php';

// Verify session
Session::start();
if (!AuthController::isAuthenticated()) {
    header('Location: Login.php');
    exit;
}

$user_id = AuthController::getCurrentUserId();
$current_role = AuthController::getCurrentRole();

// Get employee details
$employeeModel = new Employee();
$employee = $employeeModel->getByUserId($user_id);
$employee_id = $employee['employee_id'];

$database = new Database();
$db = $database->getConnection();

// Get month and year from URL or use current
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Validate month/year
$month = max(1, min(12, $month));
$year = max(2020, min(date('Y') + 1, $year));

// Get employee filter (for managers)
$view_employee_id = $employee_id;
if ($current_role === 'HR_ADMIN' || $current_role === 'DEPARTMENT_HEAD') {
    if (isset($_GET['employee_id']) && is_numeric($_GET['employee_id'])) {
        $view_employee_id = (int)$_GET['employee_id'];
    }
}

// Get attendance data for the month
$start_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$end_date = date('Y-m-t', strtotime($start_date));

$stmt = $db->prepare("
    SELECT 
        DATE(time_in) as attendance_date,
        COUNT(*) as record_count,
        SUM(CASE WHEN time_in IS NOT NULL THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN TIME(time_in) > '08:00:00' THEN 1 ELSE 0 END) as late_count,
        AVG(total_hours_worked) as avg_hours,
        MAX(time_out) as last_time_out
    FROM attendance
    WHERE employee_id = :employee_id
    AND DATE(time_in) BETWEEN :start_date AND :end_date
    GROUP BY DATE(time_in)
    ORDER BY DATE(time_in)
");

$stmt->execute([
    ':employee_id' => $view_employee_id,
    ':start_date' => $start_date,
    ':end_date' => $end_date
]);

$attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create lookup array
$attendance_by_date = [];
foreach ($attendance_data as $record) {
    $day = (int)date('d', strtotime($record['attendance_date']));
    $attendance_by_date[$day] = [
        'status' => determineStatus($record),
        'records' => $record['record_count'],
        'late' => $record['late_count'],
        'hours' => $record['avg_hours'],
        'date' => $record['attendance_date']
    ];
}

// Get employee info for header
if ($view_employee_id !== $employee_id) {
    $emp_stmt = $db->prepare("SELECT first_name, last_name FROM employees WHERE id = :id");
    $emp_stmt->execute([':id' => $view_employee_id]);
    $employee_info = $emp_stmt->fetch(PDO::FETCH_ASSOC);
    $view_employee_name = ($employee_info) ? $employee_info['first_name'] . ' ' . $employee_info['last_name'] : 'Unknown';
} else {
    $view_employee_name = htmlspecialchars($_SESSION['first_name'] ?? 'You');
}

// Get month name
$month_name = date('F Y', strtotime($start_date));

/**
 * Determine attendance status
 */
function determineStatus($record) {
    if ($record['record_count'] == 0) {
        return 'absent';
    } elseif ($record['late_count'] > 0) {
        return 'late';
    } else {
        return 'present';
    }
}

/**
 * Get calendar days for the month
 */
function getCalendarDays($year, $month) {
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $last_day = mktime(0, 0, 0, $month + 1, 0, $year);
    $first_day_of_week = date('w', $first_day);
    $num_days = date('d', $last_day);
    
    return [
        'first_day_of_week' => $first_day_of_week,
        'num_days' => $num_days
    ];
}

$calendar_info = getCalendarDays($year, $month);

// Get navigation dates
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Get employees for dropdown (only for managers)
$employees_list = [];
if ($current_role === 'HR_ADMIN' || $current_role === 'DEPARTMENT_HEAD') {
    $emp_query = "
        SELECT id, first_name, last_name, employee_id 
        FROM employees 
        WHERE status = 'ACTIVE'
    ";
    
    if ($current_role === 'DEPARTMENT_HEAD') {
        // Get current user's department
        $dept_stmt = $db->prepare("
            SELECT department FROM employees WHERE id = :id
        ");
        $dept_stmt->execute([':id' => $employee_id]);
        $dept_result = $dept_stmt->fetch(PDO::FETCH_ASSOC);
        $current_dept = $dept_result['department'] ?? '';
        
        if ($current_dept) {
            $emp_query .= " AND department = '{$current_dept}'";
        }
    }
    
    $emp_query .= " ORDER BY first_name, last_name";
    $employees_list = $db->query($emp_query)->fetchAll(PDO::FETCH_ASSOC);
}

$current_page = 'calendar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Calendar</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/mobile-responsive.js" defer></script>
    <style>
        .top-header {
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%) !important;
            color: white !important;
        }

        .top-header h1 {
            color: white !important;
        }

        .top-header .breadcrumb {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .top-header .breadcrumb a {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            padding: 20px;
            border-radius: 8px;
            color: white;
        }

        .calendar-title {
            font-size: 28px;
            font-weight: bold;
            color: white;
            margin: 0;
        }

        .calendar-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .calendar-nav button,
        .calendar-nav a {
            padding: 10px 15px;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            min-height: 44px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .calendar-nav button:hover,
        .calendar-nav a:hover {
            background: #229954;
            transform: translateY(-2px);
        }

        .calendar-nav .current-month {
            min-width: 180px;
            text-align: center;
            font-weight: 600;
        }

        .employee-filter {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }

        .employee-filter select {
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 44px;
        }

        .calendar-container {
            background: var(--bg-primary);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .calendar-table thead {
            background: linear-gradient(135deg, #003d82 0%, #0066cc 100%);
            color: white;
        }

        .calendar-table th {
            padding: 15px;
            text-align: center;
            font-weight: 600;
            border: 1px solid var(--border-color);
            color: white;
        }

        .calendar-table td {
            padding: 10px;
            border: 1px solid var(--border-color);
            min-height: 100px;
            vertical-align: top;
            background: var(--bg-primary);
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }

        .calendar-table td:hover {
            background: var(--light-bg);
            transform: scale(1.02);
            z-index: 10;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .calendar-table td.empty {
            background: var(--light-bg);
            cursor: default;
        }

        .calendar-table td.empty:hover {
            transform: none;
            box-shadow: none;
        }

        .day-number {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .day-status {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            margin: 4px 0;
            font-weight: 600;
            text-align: center;
        }

        .status-present {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-late {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-absent {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .day-hours {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .legend {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .legend-present {
            background: #d4edda;
            border-color: #c3e6cb;
        }

        .legend-late {
            background: #fff3cd;
            border-color: #ffeaa7;
        }

        .legend-absent {
            background: #f8d7da;
            border-color: #f5c6cb;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: var(--bg-primary);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
            border-left: 4px solid var(--secondary-color);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--text-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: var(--text-secondary);
            background: none;
            border: none;
            padding: 0;
            width: 30px;
            height: 30px;
        }

        .modal-close:hover {
            color: var(--text-primary);
        }

        .record-item {
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-bottom: 10px;
            background: var(--light-bg);
        }

        .record-time {
            font-weight: bold;
            color: var(--secondary-color);
        }

        .record-duration {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .calendar-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .calendar-nav {
                width: 100%;
                justify-content: space-between;
            }

            .calendar-table th {
                padding: 8px;
                font-size: 12px;
            }

            .calendar-table td {
                min-height: 80px;
                padding: 8px;
            }

            .day-number {
                font-size: 14px;
            }

            .day-status {
                font-size: 11px;
            }

            .stats-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .calendar-title {
                font-size: 20px;
            }

            .calendar-table {
                font-size: 12px;
            }

            .calendar-table th {
                padding: 6px;
            }

            .calendar-table td {
                min-height: 60px;
                padding: 6px;
            }

            .day-number {
                font-size: 12px;
            }

            .legend {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include '../app/components/Sidebar.php'; ?>

    <div class="main-content">
        <div class="top-header">
            <div class="top-header-content">
                <h1 style="margin: 0; font-size: 24px;">Attendance Calendar</h1>
                <div class="breadcrumb">
                    <a href="index.php">Dashboard</a> / Calendar
                </div>
            </div>
        </div>

        <main>
            <div class="calendar-header">
                <h2 class="calendar-title"><?php echo $month_name; ?></h2>
                <div class="calendar-nav">
                    <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?><?php echo ($view_employee_id !== $employee_id) ? '&employee_id=' . $view_employee_id : ''; ?>" class="nav-btn">
                        ← Previous
                    </a>
                    <span class="current-month"><?php echo date('M Y', strtotime("$year-$month-01")); ?></span>
                    <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?><?php echo ($view_employee_id !== $employee_id) ? '&employee_id=' . $view_employee_id : ''; ?>" class="nav-btn">
                        Next →
                    </a>
                    <a href="?" class="nav-btn">Today</a>
                </div>
            </div>

            <?php if (!empty($employees_list)): ?>
            <div class="employee-filter">
                <label for="employee_select">View Employee:</label>
                <select id="employee_select" onchange="changeEmployee()">
                    <option value="<?php echo $employee_id; ?>" <?php echo ($view_employee_id === $employee_id) ? 'selected' : ''; ?>>
                        My Calendar
                    </option>
                    <?php foreach ($employees_list as $emp): ?>
                        <option value="<?php echo $emp['id']; ?>" <?php echo ($view_employee_id === $emp['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="calendar-container">
                <table class="calendar-table">
                    <thead>
                        <tr>
                            <th>Sunday</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $day_counter = 1;
                        $first_day_of_week = $calendar_info['first_day_of_week'];
                        $num_days = $calendar_info['num_days'];

                        // Fill empty cells at start of month
                        for ($i = 0; $i < $first_day_of_week; $i++) {
                            echo '<td class="empty"></td>';
                        }

                        // Fill calendar days
                        for ($day = 1; $day <= $num_days; $day++) {
                            // New row every Sunday
                            if (($day - 1 + $first_day_of_week) % 7 === 0 && $day !== 1) {
                                echo '</tr><tr>';
                            }

                            $has_data = isset($attendance_by_date[$day]);
                            $status = $has_data ? $attendance_by_date[$day]['status'] : 'no-data';
                            $date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            $is_today = ($date_str === date('Y-m-d'));
                            $is_future = strtotime($date_str) > time();

                            echo '<td class="' . ($is_today ? 'today' : '') . '" onclick="' . ($has_data ? "showDetails('$date_str')" : '') . '" style="' . ($is_future ? 'opacity: 0.5;' : '') . '">';
                            echo '<div class="day-number">' . $day . '</div>';

                            if ($has_data) {
                                $data = $attendance_by_date[$day];
                                echo '<div class="day-status status-' . $data['status'] . '">';
                                if ($data['status'] === 'present') {
                                    echo 'Present';
                                } elseif ($data['status'] === 'late') {
                                    echo 'Late';
                                } else {
                                    echo '✗ Absent';
                                }
                                echo '</div>';
                                if ($data['hours']) {
                                    echo '<div class="day-hours">' . number_format($data['hours'], 1) . 'h</div>';
                                }
                            }

                            echo '</td>';
                        }

                        // Fill empty cells at end of month
                        $total_cells = ($day - 1 + $first_day_of_week);
                        $remaining_cells = (ceil($total_cells / 7) * 7) - $total_cells;
                        for ($i = 0; $i < $remaining_cells; $i++) {
                            echo '<td class="empty"></td>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box legend-present"></div>
                    <span>Present - On time attendance</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box legend-late"></div>
                    <span>Late - Arrived after 8:00 AM</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box legend-absent"></div>
                    <span>Absent - No records</span>
                </div>
            </div>

            <?php
            // Calculate stats
            $total_present = 0;
            $total_late = 0;
            $total_absent = 0;
            $total_hours = 0;

            foreach ($attendance_by_date as $day_data) {
                if ($day_data['status'] === 'present') {
                    $total_present++;
                } elseif ($day_data['status'] === 'late') {
                    $total_late++;
                }
                $total_hours += $day_data['hours'] ?? 0;
            }

            // Count absent days (working days without records)
            $working_days = 0;
            for ($day = 1; $day <= $num_days; $day++) {
                $date_obj = new DateTime("$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT));
                $day_of_week = $date_obj->format('w');
                // Count weekdays only
                if ($day_of_week != 0 && $day_of_week != 6) {
                    $working_days++;
                    if (!isset($attendance_by_date[$day])) {
                        $total_absent++;
                    }
                }
            }
            ?>

            <div class="stats-summary">
                <div class="stat-card">
                    <h3>Present</h3>
                    <div class="value" style="color: #27ae60;"><?php echo $total_present; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Late</h3>
                    <div class="value" style="color: #f39c12;"><?php echo $total_late; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Absent</h3>
                    <div class="value" style="color: #e74c3c;"><?php echo $total_absent; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Hours</h3>
                    <div class="value" style="color: #3498db;"><?php echo number_format($total_hours, 1); ?></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Day Details Modal -->
    <div id="detailsModal" class="modal" onclick="closeDetails(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <span id="modalDate"></span>
                <button class="modal-close" onclick="closeDetails()">×</button>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
    const attendanceData = <?php echo json_encode($attendance_by_date); ?>;

    function showDetails(date) {
        const data = attendanceData[parseInt(date.split('-')[2])];
        if (!data) return;

        const dateObj = new Date(date);
        const dateStr = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        document.getElementById('modalDate').textContent = dateStr;

        // Fetch detailed records for this date
        fetch(`../app/api/get_day_records.php?date=${date}&employee_id=<?php echo $view_employee_id; ?>`)
            .then(response => response.json())
            .then(records => {
                let html = '';
                if (records.length === 0) {
                    html = '<p style="text-align: center; color: #7f8c8d;">No records for this day.</p>';
                } else {
                    records.forEach(record => {
                        const time_in = new Date(record.time_in).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        const time_out = record.time_out ? new Date(record.time_out).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : 'N/A';
                        
                        html += `
                            <div class="record-item">
                                <div class="record-time">${time_in} - ${time_out}</div>
                                <div class="record-duration">Duration: ${record.total_hours_worked ? parseFloat(record.total_hours_worked).toFixed(1) : '0'} hours</div>
                                <div class="record-duration">Status: ${record.time_out ? 'Completed' : 'In Progress'}</div>
                            </div>
                        `;
                    });
                }
                document.getElementById('modalBody').innerHTML = html;
                document.getElementById('detailsModal').classList.add('show');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalBody').innerHTML = '<p style="color: red;">Error loading details.</p>';
                document.getElementById('detailsModal').classList.add('show');
            });
    }

    function closeDetails(event) {
        if (event && event.target.id !== 'detailsModal') return;
        document.getElementById('detailsModal').classList.remove('show');
    }

    function changeEmployee() {
        const employeeId = document.getElementById('employee_select').value;
        window.location.href = `?month=<?php echo $month; ?>&year=<?php echo $year; ?>&employee_id=${employeeId}`;
    }

    // Close modal on Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDetails();
        }
    });
    </script>
</body>
</html>
