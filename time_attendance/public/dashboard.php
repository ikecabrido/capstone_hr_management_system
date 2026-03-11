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
    header("Location: Login.php");
    exit;
}

// Only HR can access this page
if (!AuthController::hasRole('HR_ADMIN')) {
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
$current_role = $_SESSION['role'] ?? 'HR_ADMIN';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - Time & Attendance System</title>
    <link rel="icon" href="../Bestlink College of the Philippines.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/mobile-responsive.js" defer></script>
</head>
<body>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Time & Attendance Dashboard</h1>
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
                        <strong>${escapeHtml(record.first_name + ' ' + record.last_name)}</strong><br>
                        <small style="color: #999;">${escapeHtml(record.employee_number)}</small>
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
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        // Search and filter functionality
        function filterAndSort() {
            const searchTerm = document.getElementById('attendanceSearch').value.toLowerCase();
            const sortOption = document.getElementById('attendanceSort').value;
            
            let filtered = attendanceData.filter(record => {
                const name = (record.first_name + ' ' + record.last_name).toLowerCase();
                const empNum = record.employee_number.toLowerCase();
                const dept = (record.department || '').toLowerCase();
                return name.includes(searchTerm) || empNum.includes(searchTerm) || dept.includes(searchTerm);
            });
            
            // Sort
            switch(sortOption) {
                case 'name':
                    filtered.sort((a, b) => (a.first_name + a.last_name).localeCompare(b.first_name + b.last_name));
                    break;
                case 'name-desc':
                    filtered.sort((a, b) => (b.first_name + b.last_name).localeCompare(a.first_name + a.last_name));
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
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
