<?php

/**
 * HR Dashboard - Time & Attendance System
 * Main interface for HR to view attendance, generate QR codes, and manage approvals
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Employee.php";
require_once "../app/models/Holiday.php";
require_once "../app/models/EmployeeShift.php";
require_once "../app/helpers/Helper.php";
require_once "../app/helpers/AuditLog.php";
require_once "../app/core/Session.php";
require_once "../../auth/database.php";

use App\Models\Holiday;

Session::start();

// Check if user is authenticated
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

// Only HR/Time module staff can access this page
if (!AuthController::hasRole('time')) {
    header("Location: employee_dashboard.php");
    exit;
}

$attendanceModel = new Attendance();
$employeeModel = new Employee();
$auditLog = new AuditLog();

// Initialize Holiday model and check if today is a holiday
$database = Database::getInstance();
$db = $database->getConnection();
$holidayModel = new Holiday($db);
$isHolidayToday = $holidayModel->isHoliday(date('Y-m-d'));
$todayHolidayInfo = $holidayModel->getHolidayByDate(date('Y-m-d'));

// Initialize EmployeeShift model and check for employees without shifts
$employeeShiftModel = new EmployeeShift($db);
$employeesWithoutShift = $employeeShiftModel->getEmployeesWithoutShift();
$employeesWithoutShiftCount = count($employeesWithoutShift);

// Get statistics
$todayStats = $attendanceModel->getTodaySummary();
$allEmployees = $employeeModel->getTotalCount('ACTIVE');
$todayRecords = $attendanceModel->getTodayAllEmployees(100);
$pendingApprovals = $attendanceModel->getPendingApprovals(10);

// Calculate today's attendance percentage
$attendancePercentage = 0;
if ($allEmployees > 0 && $todayStats) {
    $attendancePercentage = round(($todayStats['present_count'] / $allEmployees) * 100, 2);
}

$current_page = 'dashboard.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - Time & Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="../assets/realtime-dashboard.css">
    <link rel="stylesheet" href="../assets/adminlte-overrides.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/mobile-responsive.js" defer></script>
    <script src="../assets/realtime-dashboard.js" defer></script>
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/plugins/toastr/toastr.min.js"></script>
    <script src="../../assets/dist/js/adminlte.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            padding: 24px 28px 28px;
            margin-top: 60px;
            transition: margin-left 0.3s ease, width 0.3s ease;
            background: #f4f6f9;
            min-height: calc(100vh - 60px);
        }

        .container,
        .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .page-header {
            background: linear-gradient(135deg, #0b3c91 0%, #1976d2 100%);
            padding: 24px 28px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(11, 60, 145, 0.18);
            position: relative;
            overflow: hidden;
            margin: 0 0 20px;
            border: 1px solid rgba(255,255,255,0.12);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            position: relative;
            z-index: 1;
            letter-spacing: 0.2px;
        }

        .page-title i {
            font-size: 32px;
            opacity: 0.95;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            margin: 6px 0 0 0;
            position: relative;
            z-index: 1;
            font-weight: 400;
        }

        .dashboard-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            margin-bottom: 22px;
        }

        .dashboard-grid .info-box {
            display: flex;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            border: 0;
        }

        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            min-width: 70px;
            font-size: 32px;
            color: white;
            background: #1976d2;
        }

        .info-box-icon.bg-primary {
            background: linear-gradient(135deg, #0d47a1, #1976d2);
        }

        .info-box-icon.bg-success {
            background: linear-gradient(135deg, #2e7d32, #43a047);
        }

        .info-box-icon.bg-warning {
            background: linear-gradient(135deg, #f57f17, #fbc02d);
        }

        .info-box-icon.bg-info {
            background: linear-gradient(135deg, #0097a7, #00bcd4);
        }

        .info-box-content {
            flex: 1;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-box-text {
            font-size: 12px;
            color: #999;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-box-number {
            font-size: 28px;
            font-weight: 700;
            color: #0d47a1;
            line-height: 1;
        }

        .info-box-unit {
            font-size: 12px;
            color: #bbb;
            margin-top: 4px;
        }

        .dashboard-grid .card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            border-left: 4px solid #1976d2;
        }

        .card {
            border-radius: 12px;
            border: 0;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .main-sidebar,
        .main-header {
            border: 0;
        }

        .card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 14px 16px;
            color: #0d47a1;
        }

        .card .card-header .card-title {
            color: #0d47a1;
            font-weight: 600;
            font-size: 16px;
        }

        .card .card-body {
            padding: 16px;
        }

        .content-wrapper .card,
        .content-wrapper .page-header {
            margin-left: 0;
            margin-right: 0;
        }

        .realtime-dashboard-widget,
        .container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            padding: 16px;
            margin-bottom: 20px;
        }

        .container h2 {
            font-size: 20px;
            color: #0d47a1;
            margin-bottom: 14px;
            font-weight: 600;
        }

        .btn,
        button {
            border-radius: 6px;
            border: 1px solid transparent;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        button:hover,
        .btn:hover {
            transform: translateY(-1px);
        }

        #realtimeRefresh {
            background: #1976d2;
            color: #fff;
            padding: 7px 12px;
            font-size: 13px;
        }

        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .filter-controls input,
        .filter-controls select {
            border: 1px solid #d0d7de;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 14px;
            color: #2f3a4a;
            background: #fff;
            min-height: 38px;
        }

        .filter-controls input:focus,
        .filter-controls select:focus {
            border-color: #1976d2;
            outline: none;
            box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.12);
        }

        #attendanceTable {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        #attendanceTable thead th {
            background: #f8f9fa;
            color: #0d47a1;
            font-weight: 600;
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #e9ecef;
        }

        #attendanceTable tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #eef2f6;
            color: #374151;
            font-size: 14px;
        }

        #attendanceTable tbody tr:hover {
            background: #f8fbff;
        }

        #attendanceTable tbody tr:last-child td {
            border-bottom: 0;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }

        .badge-success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-warning {
            background: #fff8e1;
            color: #f57f17;
        }

        .badge-info {
            background: #e3f2fd;
            color: #1565c0;
        }

        .badge-danger {
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div
        class="preloader flex-column justify-content-center align-items-center">
        <img
            class="animation__wobble"
            src="../../assets/pics/bcpLogo.png"
            alt="AdminLTELogo"
            height="60"
            width="60" />
    </div>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
        <div class="page-header">
            <div>
                <div class="page-title">
                    <i class="fas fa-chart-line"></i> Time & Attendance Dashboard
                </div>
                <div class="page-subtitle">Real-time attendance and HR analytics</div>
            </div>
        </div>

            <!-- Shift Assignment Alert -->
            <?php if ($employeesWithoutShiftCount > 0): ?>
            <div style="background: #fff3e0; border-left: 4px solid #FF9800; padding: 15px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 28px; color: #FF9800;"></i>
                <div style="flex: 1;">
                    <strong style="color: #e65100; font-size: 16px;">Shift Assignment Required</strong>
                    <p style="margin: 8px 0 0 0; color: #5d4037; font-size: 14px;">
                        <strong><?php echo $employeesWithoutShiftCount; ?> employee<?php echo $employeesWithoutShiftCount > 1 ? 's' : ''; ?></strong> <?php echo $employeesWithoutShiftCount > 1 ? 'do' : 'does'; ?> not have an active shift assignment. This may affect attendance tracking and payroll calculations.
                    </p>
                    <p style="margin: 10px 0 0 0;">
                        <a href="../shifts.php" style="background: #FF9800; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;">Manage Shifts</a>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Holiday Alert -->
            <?php if ($isHolidayToday && $todayHolidayInfo): ?>
            <div style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-calendar-check" style="font-size: 28px; color: #2196F3;"></i>
                <div>
                    <strong style="color: #1565c0; font-size: 16px;">Today is a Public Holiday</strong>
                    <p style="margin: 8px 0 0 0; color: #455a64; font-size: 14px;">
                        <strong><?php echo htmlspecialchars($todayHolidayInfo['name']); ?></strong> - All employees are marked as HOLIDAY. No absences will be recorded.
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Stats -->
            <div class="dashboard-grid">
                <div class="info-box">
                    <div class="info-box-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employees</span>
                        <span class="info-box-number"><?php echo $allEmployees; ?></span>
                        <span class="info-box-unit">Active employees</span>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon bg-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Present Today</span>
                        <span class="info-box-number"><?php echo $todayStats['present_count'] ?? 0; ?></span>
                        <span class="info-box-unit"><?php echo $attendancePercentage; ?>% attendance</span>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon bg-warning">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Absent Today</span>
                        <span class="info-box-number"><?php echo $todayStats['absent_count'] ?? 0; ?></span>
                        <span class="info-box-unit">Need follow-up</span>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon bg-info">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Approvals</span>
                        <span class="info-box-number"><?php echo count($pendingApprovals); ?></span>
                        <span class="info-box-unit">Manual entries</span>
                    </div>
                </div>
            </div>

            <!-- Real-time Updates Widget -->
            <div class="realtime-dashboard-widget">
                <div class="realtime-header">
                    <div class="realtime-title">
                        <i class="fas fa-signal"></i>
                        Live Activity Feed
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div class="realtime-status">
                            <span class="status-indicator"></span>
                            <span>Live Updates</span>
                        </div>
                        <button id="realtimeRefresh" title="Refresh Now">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>

                <div id="realtimeMetrics">
                    <div class="metric-item">
                        <span class="metric-label">Recent Logins:</span>
                        <span class="metric-value">-</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">Time Ins:</span>
                        <span class="metric-value">-</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">Time Outs:</span>
                        <span class="metric-value">-</span>
                    </div>
                </div>

                <div id="realtimeEventsContainer">
                    <div style="text-align: center; padding: 20px; color: #999;">
                        <i class="fas fa-spinner fa-spin"></i> Loading live events...
                    </div>
                </div>

                <div style="margin-top: 10px; text-align: right; font-size: 11px; color: #999;">
                    Last updated: <span id="realtimeLastRefresh">--:--:--</span>
                </div>
            </div>

            <!-- Today's Attendance Table -->
            <div class="container">
                <h2>Today's Attendance (<?php echo count($todayRecords); ?> employees)</h2>

                <!-- Search and Sort Controls -->
                <div class="filter-controls">
                    <input type="text" id="attendanceSearch" placeholder="Search by name, employee #, or department..." />
                    <select id="attendanceSort">
                        <option value="name">Sort: Name (A-Z)</option>
                        <option value="name-desc">Sort: Name (Z-A)</option>
                        <option value="time">Sort: Time In (Latest)</option>
                        <option value="time-asc">Sort: Time In (Earliest)</option>
                        <option value="department">Sort: Department</option>
                        <option value="status">Sort: Status</option>
                    </select>
                </div>

                <table id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Attendance data from PHP
        const isHolidayToday = <?php echo json_encode($isHolidayToday); ?>;
        const holidayInfo = <?php echo json_encode($todayHolidayInfo); ?>;
        const attendanceData = <?php echo json_encode($todayRecords); ?>;

        // Display records
        function displayRecords(records) {
            const tbody = document.getElementById('attendanceBody');
            tbody.innerHTML = '';

            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: #999;">No records found</td></tr>';
                return;
            }

            records.forEach(record => {
                const row = document.createElement('tr');
                
                // Check if today is a holiday - if so, mark status as HOLIDAY
                let status;
                if (isHolidayToday) {
                    status = 'HOLIDAY';
                } else {
                    status = record.time_in ? (new Date(record.time_in).getHours() > 9 ? 'LATE' : 'PRESENT') : 'ABSENT';
                }
                
                const statusClass = status === 'PRESENT' ? 'badge-success' : (
                    status === 'LATE' ? 'badge-warning' : (
                        status === 'HOLIDAY' ? 'badge-info' : 'badge-danger'
                    )
                );

                row.innerHTML = `
                    <td>
                        <strong>${escapeHtml(record.full_name)}</strong>
                    </td>
                    <td>${escapeHtml(record.department || 'N/A')}</td>
                    <td>${escapeHtml(record.position || 'N/A')}</td>
                    <td>
                        ${record.time_in 
                            ? new Date(record.time_in).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: true})
                            : '<span style="color: #e74c3c;">Not recorded</span>'}
                    </td>
                    <td>
                        ${record.time_out 
                            ? new Date(record.time_out).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: true})
                            : '<span style="color: #f39c12;">Pending</span>'}
                    </td>
                    <td>${record.duration || 'N/A'}</td>
                    <td><span class="badge ${statusClass}">${status}</span></td>
                `;
                tbody.appendChild(row);
            });
        }

        // Escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Search and filter functionality
        function filterAndSort() {
            const searchTerm = document.getElementById('attendanceSearch').value.toLowerCase();
            const sortOption = document.getElementById('attendanceSort').value;

            let filtered = attendanceData.filter(record => {
                const name = record.full_name.toLowerCase();
                const dept = (record.department || '').toLowerCase();
                return name.includes(searchTerm) || dept.includes(searchTerm);
            });

            // Sort
            switch (sortOption) {
                case 'name':
                    filtered.sort((a, b) => a.full_name.localeCompare(b.full_name));
                    break;
                case 'name-desc':
                    filtered.sort((a, b) => b.full_name.localeCompare(a.full_name));
                    break;
                case 'time':
                    filtered.sort((a, b) => new Date(b.time_in || 0) - new Date(a.time_in || 0));
                    break;
                case 'time-asc':
                    filtered.sort((a, b) => new Date(a.time_in || 0) - new Date(b.time_in || 0));
                    break;
                case 'department':
                    filtered.sort((a, b) => (a.department || '').localeCompare(b.department || ''));
                    break;
                case 'status':
                    filtered.sort((a, b) => {
                        let statusA;
                        let statusB;
                        
                        if (isHolidayToday) {
                            statusA = 'HOLIDAY';
                            statusB = 'HOLIDAY';
                        } else {
                            statusA = a.time_in ? 'PRESENT' : 'ABSENT';
                            statusB = b.time_in ? 'PRESENT' : 'ABSENT';
                        }
                        
                        return statusB.localeCompare(statusA);
                    });
                    break;
            }

            displayRecords(filtered);
        }

        // Event listeners
        document.getElementById('attendanceSearch').addEventListener('keyup', filterAndSort);
        document.getElementById('attendanceSort').addEventListener('change', filterAndSort);

        // Initial display
        displayRecords(attendanceData);

        // Load dark mode preference (default to light mode for time_attendance)
        window.addEventListener('load', function() {
            const darkModeSetting = localStorage.getItem('darkMode');
            const darkMode = darkModeSetting === 'true'; // Only true if explicitly set
            
            // Reset to light mode by default on each page load for time_attendance
            if (!darkModeSetting) {
                localStorage.setItem('darkMode', 'false');
            }
            
            if (darkMode) {
                document.body.classList.add('dark-mode');
            }
        });

        // Live Clock
        function updateClock() {
            const clockElement = document.getElementById('liveClock');
            if (clockElement) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
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
            }, 3000); // Show for 3 seconds (allows animation to loop multiple times)

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
                            }, 3000); // Allow animation to loop
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>