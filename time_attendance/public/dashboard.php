<?php

/**
 * HR Dashboard - Time & Attendance System
 * Main interface for HR to view attendance, generate QR codes, and manage approvals
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/Attendance.php";
require_once "../app/models/Employee.php";
require_once "../app/helpers/Helper.php";
require_once "../app/helpers/AuditLog.php";
require_once "../app/core/Session.php";

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
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="stylesheet" href="../assets/realtime-dashboard.css">
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
            transition: margin-left 0.3s ease;
        }

        body.sidebar-collapsed {
            margin-left: 0;
        }

        .main-content {
            width: calc(100% - 250px);
            margin-left: 250px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            overflow-y: auto;
            transition: width 0.3s ease, margin-left 0.3s ease;
        }

        body.sidebar-collapsed .main-content {
            width: 100%;
            margin-left: 0;
        }

        .content-wrapper {
            width: 100%;
            margin: 0;
            padding: 30px 20px;
        }

        /* Override AdminLTE container defaults */
        .container,
        .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    </style>
</head>

<body>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Time & Attendance Dashboard</h1>
                <div class="live-clock" id="liveClock">00:00:00</div>
            </div>
            <!-- Quick Stats -->
            <div class="dashboard-grid">
                <div class="card employees">
                    <h3>Total Employees</h3>
                    <div class="card-value"><?php echo $allEmployees; ?></div>
                    <div class="card-unit">Active employees</div>
                </div>

                <div class="card present">
                    <h3>Present Today</h3>
                    <div class="card-value"><?php echo $todayStats['present_count'] ?? 0; ?></div>
                    <div class="card-unit"><?php echo $attendancePercentage; ?>% attendance</div>
                </div>

                <div class="card absent">
                    <h3>Absent Today</h3>
                    <div class="card-value"><?php echo $todayStats['absent_count'] ?? 0; ?></div>
                    <div class="card-unit">Need follow-up</div>
                </div>

                <div class="card pending">
                    <h3>Pending Approvals</h3>
                    <div class="card-value"><?php echo count($pendingApprovals); ?></div>
                    <div class="card-unit">Manual entries</div>
                </div>
            </div>

            <!-- Attendance Metrics Overview -->
            <div style="margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">📊 Monthly Attendance Metrics</h2>

                <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <!-- Attendance Rate -->
                    <div class="metric-card" style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Avg Attendance Rate</span>
                            <i class="fas fa-chart-pie" style="color: #2196F3; font-size: 18px;"></i>
                        </div>
                        <div id="avg-attendance-rate" style="font-size: 28px; font-weight: bold; color: #2196F3;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Target: 95%+</div>
                    </div>

                    <!-- Punctuality Score -->
                    <div class="metric-card" style="background: #e8f5e9; border-left: 4px solid #4CAF50; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Avg Punctuality</span>
                            <i class="fas fa-thumbs-up" style="color: #4CAF50; font-size: 18px;"></i>
                        </div>
                        <div id="avg-punctuality-score" style="font-size: 28px; font-weight: bold; color: #4CAF50;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Grade Scale: A-F</div>
                    </div>

                    <!-- Absence Rate -->
                    <div class="metric-card" style="background: #ffebee; border-left: 4px solid #f44336; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Avg Absence Rate</span>
                            <i class="fas fa-ban" style="color: #f44336; font-size: 18px;"></i>
                        </div>
                        <div id="avg-absence-rate" style="font-size: 28px; font-weight: bold; color: #f44336;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Alert if >20%</div>
                    </div>

                    <!-- Performance Score -->
                    <div class="metric-card" style="background: #fff3e0; border-left: 4px solid #FF9800; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Performance Score</span>
                            <i class="fas fa-star" style="color: #FF9800; font-size: 18px;"></i>
                        </div>
                        <div id="avg-performance-score" style="font-size: 28px; font-weight: bold; color: #FF9800;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Weighted: 40-35-25</div>
                    </div>

                    <!-- Late Incidents -->
                    <div class="metric-card" style="background: #e1f5fe; border-left: 4px solid #03A9F4; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Late Incidents</span>
                            <i class="fas fa-hourglass-end" style="color: #03A9F4; font-size: 18px;"></i>
                        </div>
                        <div id="total-late-incidents" style="font-size: 28px; font-weight: bold; color: #03A9F4;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Current Month</div>
                    </div>

                    <!-- Overtime Hours -->
                    <div class="metric-card" style="background: #f3e5f5; border-left: 4px solid #9C27B0; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Overtime Hours</span>
                            <i class="fas fa-bolt" style="color: #9C27B0; font-size: 18px;"></i>
                        </div>
                        <div id="total-overtime-hours" style="font-size: 28px; font-weight: bold; color: #9C27B0;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Total for Month</div>
                    </div>

                    <!-- Excellent Performers -->
                    <div class="metric-card" style="background: #e0f2f1; border-left: 4px solid #009688; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Excellent (A Grade)</span>
                            <i class="fas fa-check-circle" style="color: #009688; font-size: 18px;"></i>
                        </div>
                        <div id="excellent-performers" style="font-size: 28px; font-weight: bold; color: #009688;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Score ≥90</div>
                    </div>

                    <!-- Critical Issues -->
                    <div class="metric-card" style="background: #ffe0b2; border-left: 4px solid #FF6F00; padding: 20px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase;">Critical Issues</span>
                            <i class="fas fa-exclamation-triangle" style="color: #FF6F00; font-size: 18px;"></i>
                        </div>
                        <div id="critical-issues" style="font-size: 28px; font-weight: bold; color: #FF6F00;">--</div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">Needs Action</div>
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
        // Load attendance metrics
        function loadAttendanceMetrics() {
            const monthYear = new Date().toISOString().slice(0, 7);
            $.ajax({
                url: '../app/api/metrics.php',
                type: 'GET',
                data: {
                    action: 'get_attendance_metrics_summary',
                    month_year: monthYear
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.summary) {
                        $('#avg-attendance-rate').text(Math.round(response.summary.avg_attendance_rate) + '%');
                        $('#avg-punctuality-score').text(Math.round(response.summary.avg_punctuality_score));
                        $('#avg-absence-rate').text(Math.round(response.summary.avg_absence_rate) + '%');
                        $('#avg-performance-score').text(Math.round(response.summary.avg_overall_performance));
                        $('#total-late-incidents').text(Math.round(response.summary.total_late_incidents));
                        $('#total-overtime-hours').text(Math.round(response.summary.total_overtime_hours * 10) / 10);
                        $('#excellent-performers').text(response.summary.excellent_performers);
                        $('#critical-issues').text(response.summary.critical_issues);
                    }
                },
                error: function(error) {
                    console.log('Error loading metrics:', error);
                    // Set default values on error
                    const defaultElements = ['#avg-attendance-rate', '#avg-punctuality-score', '#avg-absence-rate',
                        '#avg-performance-score', '#total-late-incidents', '#total-overtime-hours',
                        '#excellent-performers', '#critical-issues'
                    ];
                    defaultElements.forEach(el => $(el).text('N/A'));
                }
            });
        }

        // Load metrics on page load
        $(document).ready(function() {
            loadAttendanceMetrics();
            // Auto-refresh metrics every 5 minutes
            setInterval(loadAttendanceMetrics, 5 * 60 * 1000);
        });

        // Attendance data from PHP
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
                const status = record.time_in ? (new Date(record.time_in).getHours() > 9 ? 'LATE' : 'PRESENT') : 'ABSENT';
                const statusClass = status === 'PRESENT' ? 'badge-success' : (status === 'LATE' ? 'badge-warning' : 'badge-danger');

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
                        const statusA = a.time_in ? 'PRESENT' : 'ABSENT';
                        const statusB = b.time_in ? 'PRESENT' : 'ABSENT';
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

        // Load dark mode preference on page load
        window.addEventListener('load', function() {
            const darkMode = localStorage.getItem('darkMode') === 'true';
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