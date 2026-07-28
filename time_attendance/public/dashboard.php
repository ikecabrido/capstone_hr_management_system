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
    header("Location:../../login_form.php");
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

// Get all active employees for QR Directory
$activeEmployees = $employeeModel->getAll('Active');

// Calculate today's attendance percentage
$attendancePercentage = 0;
if ($allEmployees > 0 && $todayStats) {
    $attendancePercentage = round(($todayStats['present_count'] / $allEmployees) * 100, 2);
}

$current_page = 'dashboard.php';
$current_role = $_SESSION['user']['role']?? $_SESSION['role']?? 'time';

$page_title = 'HR Dashboard - Time & Attendance System';
$page_head_extra = '';
?>
<?php require_once __DIR__. '/../layout/page_start.php';?>
<?php require_once __DIR__. '/../layout/sidebar.php';?>
<?php $page_title = 'Time & Attendance Dashboard'; $page_subtitle = 'Real-time attendance and HR analytics'; $page_icon = 'fa-chart-line';?>
<?php require_once __DIR__. '/../layout/content_header.php';?>

    <style>
    .ta-dashboard * { margin: 0; padding: 0; box-sizing: border-box; }
    .ta-dashboard { font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; }

    .ta-dashboard .dashboard-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); margin-bottom:22px }
    .ta-dashboard .info-box { display:flex; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(15,23,42,.08); overflow:hidden; border:0 }
    .ta-dashboard .info-box-icon { display:flex; align-items:center; justify-content:center; width:70px; min-width:70px; font-size:32px; color:#fff; background:#1976d2 }
    .ta-dashboard .info-box-icon.bg-primary{ background: linear-gradient(135deg,#0d47a1,#1976d2) }
    .ta-dashboard .info-box-icon.bg-success{ background: linear-gradient(135deg,#2e7d32,#43a047) }
    .ta-dashboard .info-box-icon.bg-warning{ background: linear-gradient(135deg,#f57f17,#fbc02d) }
    .ta-dashboard .info-box-icon.bg-info{ background: linear-gradient(135deg,#0097a7,#00bcd4) }
    .ta-dashboard .info-box-content{ flex:1; padding:12px 16px; display:flex; flex-direction:column; justify-content:center }
    .ta-dashboard .info-box-text{ font-size:12px; color:#999; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px }
    .ta-dashboard .info-box-number{ font-size:28px; font-weight:700; color:#0d47a1 }
    .ta-dashboard .info-box-unit{ font-size:12px; color:#bbb; margin-top:4px }

    .ta-dashboard .card{ border-radius:12px; border:0; box-shadow:0 2px 8px rgba(15,23,42,.08); overflow:hidden }
    .ta-dashboard .card.card-header{ background:#f8f9fa; border-bottom:1px solid #e9ecef; padding:14px 16px; color:#0d47a1 }
    .ta-dashboard .card.card-header.card-title{ color:#0d47a1; font-weight:600; font-size:16px }
    .ta-dashboard .card.card-body{ padding:16px }

    .ta-dashboard .realtime-dashboard-widget,.ta-dashboard .container { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(15,23,42,.08); padding:16px; margin-bottom:20px }
    .ta-dashboard .container h2 { font-size:20px; color:#0d47a1; margin-bottom:14px; font-weight:600 }

    .ta-dashboard .btn,.ta-dashboard button { border-radius:6px; border:1px solid transparent; font-weight:600; transition: all .2s ease }
    .ta-dashboard button:hover,.ta-dashboard .btn:hover { transform: translateY(-1px) }
    .ta-dashboard #realtimeRefresh { background:#1976d2; color:#fff; padding:7px 12px; font-size:13px }

    .ta-dashboard .filter-controls{ display:flex; flex-wrap:wrap; gap:10px; margin-bottom:12px }
    .ta-dashboard .filter-controls input,.ta-dashboard .filter-controls select{ border:1px solid #d0d7de; border-radius:6px; padding:8px 10px; font-size:14px; color:#2f3a4a; background:#fff; min-height:38px }
    .ta-dashboard .filter-controls input:focus,.ta-dashboard .filter-controls select:focus{ border-color:#1976d2; outline:none; box-shadow:0 0 0 2px rgba(25,118,210,.12) }

    .ta-dashboard #attendanceTable{ width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden }
    .ta-dashboard #attendanceTable thead th{ background:#f8f9fa; color:#0d47a1; font-weight:600; text-align:left; padding:12px 10px; border-bottom:1px solid #e9ecef }
    .ta-dashboard #attendanceTable tbody td{ padding:12px 10px; border-bottom:1px solid #eef2f6; color:#374151; font-size:14px }
    .ta-dashboard #attendanceTable tbody tr:hover{ background:#f8fbff }
    .ta-dashboard #attendanceTable tbody tr:last-child td{ border-bottom:0 }

    .ta-dashboard .badge{ display:inline-block; padding:4px 8px; border-radius:999px; font-size:12px; font-weight:700; letter-spacing:.2px; text-transform:uppercase }
    .ta-dashboard .badge-success{ background:#e8f5e9; color:#2e7d32 }
    .ta-dashboard .badge-warning{ background:#fff8e1; color:#f57f17 }
    .ta-dashboard .badge-info{ background:#e3f2fd; color:#1565c0 }
    .ta-dashboard .badge-danger{ background:#ffebee; color:#c62828 }

    /* QR Directory specific styles */
    .qr-item { border-bottom: 1px solid #eee; padding: 10px 0; display: flex; align-items: center; justify-content: space-between; }
    .qr-info { display: flex; align-items: center; gap: 15px; }
    .qr-thumb { width: 40px; height: 40px; }

    #qrModal.modal-dialog { max-width: 400px; }
    #qrModal.modal-content { text-align: center; padding: 20px; }
    #qrModal #qrcode { display: flex; justify-content: center; margin: 20px 0; }

    /* Preloader styles left global intentionally */
    .preloader { position: fixed; top:0; left:0; width:100%; height:100%; background: linear-gradient(135deg,#0d47a1 0%,#0b3c91 100%); display:flex; align-items:center; justify-content:center; flex-direction:column; z-index:99999 }
    .animation__wobble { animation: wobble 2.5s infinite ease-in-out }
    @keyframes wobble { 0%{transform:translateX(0)}15%{transform:translateX(-5px) rotate(-5deg)}30%{transform:translateX(3px) rotate(3deg)}45%{transform:translateX(-3px) rotate(-3deg)}60%{transform:translateX(2px) rotate(2deg)}75%{transform:translateX(-1px) rotate(-1deg)}100%{transform:translateX(0)} }
    </style>

    <div class="ta-dashboard">
            <!-- Shift Assignment Alert -->
            <?php if ($employeesWithoutShiftCount > 0):?>
            <div style="background: #fff3e0; border-left: 4px solid #FF9800; padding: 15px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 28px; color: #FF9800;"></i>
                <div style="flex: 1;">
                    <strong style="color: #e65100; font-size: 16px;">Shift Assignment Required</strong>
                    <p style="margin: 8px 0 0 0; color: #5d4037; font-size: 14px;">
                        <strong><?php echo $employeesWithoutShiftCount;?> <?php echo $employeesWithoutShiftCount > 1? 'employees' : 'employee';?></strong> <?php echo $employeesWithoutShiftCount > 1? 'do' : 'does';?> not have an active shift assignment. This may affect attendance tracking and payroll calculations.
                    </p>
                    <p style="margin: 10px 0 0 0;">
                        <a href="../shifts.php" style="background: #FF9800; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;">Manage Shifts</a>
                    </p>
                </div>
            </div>
            <?php endif;?>

            <!-- Holiday Alert -->
            <?php if ($isHolidayToday && $todayHolidayInfo):?>
            <div style="background: #e3f2fd; border-left: 4px solid #2196F3; padding: 15px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-calendar-check" style="font-size: 28px; color: #2196F3;"></i>
                <div>
                    <strong style="color: #1565c0; font-size: 16px;">Today is a Public Holiday</strong>
                    <p style="margin: 8px 0 0 0; color: #455a64; font-size: 14px;">
                        <strong><?php echo htmlspecialchars($todayHolidayInfo['name']);?></strong> - All employees are marked as HOLIDAY. No absences will be recorded.
                    </p>
                </div>
            </div>
            <?php endif;?>

            <!-- Quick Stats -->
            <div class="dashboard-grid">
                <div class="info-box">
                    <div class="info-box-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employees</span>
                        <span class="info-box-number"><?php echo $allEmployees;?></span>
                        <span class="info-box-unit">Active employees</span>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon bg-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Present Today</span>
                        <span class="info-box-number"><?php echo $todayStats['present_count']?? 0;?></span>
                        <span class="info-box-unit"><?php echo $attendancePercentage;?>% attendance</span>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon bg-warning">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Absent Today</span>
                        <span class="info-box-number"><?php echo $todayStats['absent_count']?? 0;?></span>
                        <span class="info-box-unit">Need follow-up</span>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon bg-info">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Approvals</span>
                        <span class="info-box-number"><?php echo count($pendingApprovals);?></span>
                        <span class="info-box-unit">Manual entries</span>
                    </div>
                </div>
            </div>

            <!-- Employee QR Directory -->
            <div class="container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 style="margin-bottom: 0;"><i class="fas fa-qrcode"></i> Employee QR Directory</h2>
                    <button class="btn btn-primary" onclick="toggleQRDirectory()"><i class="fas fa-qrcode"></i> Show/Hide Directory</button>
                </div>
                <p class="text-muted">Use the directory below to view and print unique QR codes for employee IDs.</p>
                
                <div class="filter-controls">
                    <input type="text" id="qrEmployeeSearch" placeholder="Search employee by name or ID..." onkeyup="filterQRDirectory()" />
                </div>
                
                <div id="qrDirectoryWrapper" style="display:none;">
                    <div id="qrDirectoryContainer" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                        <table class="table table-striped mb-0" id="qrDirectoryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 80px;">QR</th>
                                    <th style="width: 120px;">Employee ID</th>
                                    <th>Full Name</th>
                                    <th>Department</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="qrDirectoryBody">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kiosk Section -->
            <div class="container">
                <div class="card card-info">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-camera"></i> Kiosk Mode (Attendance Scanner)</h4>
                    </div>
                    <div class="card-body text-center">
                        <div id="kioskScanner"></div>
                        <div id="kioskStatus" class="mt-4 p-3 rounded alert alert-info" style="display: none;">
                            Ready to scan...
                        </div>
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
                <h2>Today's Attendance (<?php echo count($todayRecords);?> employees)</h2>

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
                            <th style="width: 100px;">QR / ID</th>
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

    <!-- QR Print Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">Employee QR Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="qrcode"></div>
                    <div id="qrEmployeeInfo" style="margin-top: 15px;">
                        <h4 id="modalEmpName"></h4>
                        <p id="modalEmpDetails" class="text-muted"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="printQR()">
                        <i class="fas fa-print"></i> Print QR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        // Data from PHP
        const isHolidayToday = <?php echo json_encode($isHolidayToday);?>;
        const holidayInfo = <?php echo json_encode($todayHolidayInfo);?>;
        const attendanceData = <?php echo json_encode($todayRecords);?>;
        const employees = <?php echo json_encode($activeEmployees);?>;

        // --- QR Directory Logic ---
        function toggleQRDirectory() {
            const wrapper = document.getElementById('qrDirectoryWrapper');
            wrapper.style.display = (wrapper.style.display === 'none' || wrapper.style.display === '')? 'block' : 'none';
        }

        function filterQRDirectory() {
            const search = document.getElementById('qrEmployeeSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#qrDirectoryBody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(search)? '' : 'none';
            });
        }

        function renderQRDirectory() {
            const tbody = document.getElementById('qrDirectoryBody');
            tbody.innerHTML = '';

            employees.forEach(emp => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><div id="qr-box-${emp.employee_id}" class="qr-thumb-container"></div></td>
                    <td><strong>${escapeHtml(emp.employee_no || emp.employee_id)}</strong></td>
                    <td>${escapeHtml(emp.full_name)}</td>
                    <td>${escapeHtml(emp.department || 'N/A')}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewQR(${emp.employee_id}, '${escapeHtml(emp.full_name)}', '${escapeHtml(emp.department || 'N/A')}', '${escapeHtml(emp.position || 'N/A')}')">View</button>
                        <button class="btn btn-sm btn-info" onclick="printQR('${emp.employee_id}')"><i class="fas fa-print"></i></button>
                    </td>
                `;
                tbody.appendChild(row);

                // Pre-generate small QR for table if needed, but let's do it on demand for performance
                const qrBox = document.createElement('div');
                qrBox.id = 'qr-box-' + emp.employee_id;
                qrBox.className = 'qr-thumb-container';
                row.cells[0].appendChild(qrBox);
                new QRCode(qrBox, {
                    text: emp.employee_id.toString(),
                    width: 50,
                    height: 50
                });
            });
        }

        let currentQRInstance = null;
        function viewQR(id, name, dept, pos) {
            $('#qrModal').modal('show');
            $('#modalEmpName').text(name);
            $('#modalEmpDetails').text(dept + ' - ' + pos);
            
            const container = document.getElementById('qrcode');
            container.innerHTML = '';
            currentQRInstance = new QRCode(container, {
                text: id.toString(),
                width: 200,
                height: 200,
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        function printQR(id) {
            const emp = employees.find(e => e.employee_id == id);
            if (emp) {
                viewQR(emp.employee_id, emp.full_name, emp.department || 'N/A', emp.position || 'N/A');
                setTimeout(performPrint, 500);
            }
        }

        function performPrint() {
            const content = document.getElementById('qrcode').innerHTML;
            const win = window.open('', '', 'width=400,height=400');
            win.document.write(`
                <html>
                <head><title>Print QR</title></head>
                <body style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh;">
                    <div style="padding:20px; border:1px solid #000; text-align:center;">
                        ${content}
                        <h3 style="margin-top:10px;">${$('#modalEmpName').text()}</h3>
                        <p>${$('#modalEmpDetails').text()}</p>
                    </div>
                </body>
                </html>
            `);
            win.document.close();
            win.print();
        }

        // --- Attendance Display Logic ---
        function displayRecords(records) {
            const tbody = document.getElementById('attendanceBody');
            tbody.innerHTML = '';

            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: #999;">No records found</td></tr>';
                return;
            }

            records.forEach(record => {
                const row = document.createElement('tr');
                let status;
                if (isHolidayToday) {
                    status = 'HOLIDAY';
                } else {
                    status = record.time_in? (new Date(record.time_in).getHours() > 9? 'LATE' : 'PRESENT') : 'ABSENT';
                }
                
                const statusClass = status === 'PRESENT'? 'badge-success' : (
                    status === 'LATE'? 'badge-warning' : (
                        status === 'HOLIDAY'? 'badge-info' : 'badge-danger'
                    )
                );

                row.innerHTML = `
                    <td><strong>${escapeHtml(record.employee_no || record.employee_id)}</strong></td>
                    <td>${escapeHtml(record.full_name)}</td>
                    <td>${escapeHtml(record.department || 'N/A')}</td>
                    <td>${escapeHtml(record.position || 'N/A')}</td>
                    <td>${record.time_in? new Date(record.time_in).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: true}) : '<span style="color: #e74c3c;">Not recorded</span>'}</td>
                    <td>${record.time_out? new Date(record.time_out).toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: true}) : '<span style="color: #f39c12;">Pending</span>'}</td>
                    <td>${record.duration || 'N/A'}</td>
                    <td><span class="badge ${statusClass}">${status}</span></td>
                `;
                tbody.appendChild(row);
            });
        }

        function filterAndSort() {
            const searchTerm = document.getElementById('attendanceSearch').value.toLowerCase();
            const sortOption = document.getElementById('attendanceSort').value;

            let filtered = attendanceData.filter(record => {
                const name = record.full_name.toLowerCase();
                const dept = (record.department || '').toLowerCase();
                return name.includes(searchTerm) || dept.includes(searchTerm);
            });

            switch (sortOption) {
                case 'name': filtered.sort((a, b) => a.full_name.localeCompare(b.full_name)); break;
                case 'name-desc': filtered.sort((a, b) => b.full_name.localeCompare(a.full_name)); break;
                case 'time': filtered.sort((a, b) => new Date(b.time_in || 0) - new Date(a.time_in || 0)); break;
                case 'time-asc': filtered.sort((a, b) => new Date(a.time_in || 0) - new Date(b.time_in || 0)); break;
                case 'department': filtered.sort((a, b) => (a.department || '').localeCompare(b.department || '')); break;
                case 'status':
                    filtered.sort((a, b) => {
                        let sA = isHolidayToday? 'HOLIDAY' : (a.time_in? 'PRESENT' : 'ABSENT');
                        let sB = isHolidayToday? 'HOLIDAY' : (b.time_in? 'PRESENT' : 'ABSENT');
                        return sB.localeCompare(sA);
                    });
                    break;
            }
            displayRecords(filtered);
        }

        function escapeHtml(text) {
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return text? String(text).replace(/[&<>"']/g, m => map[m]) : '';
        }

        $(document).ready(function() {
            renderQRDirectory();
            displayRecords(attendanceData);
            $('#attendanceSearch').on('keyup', filterAndSort);
            $('#attendanceSort').on('change', filterAndSort);
        });

        // --- Kiosk Logic ---
        async function processScan(employeeId) {
            const statusDiv = document.getElementById('kioskStatus');
            statusDiv.style.display = 'block';
            statusDiv.className = 'mt-4 p-3 rounded alert alert-info';
            statusDiv.innerText = 'Processing...';

            try {
                const response = await fetch(`processStaticQR.php?id=${employeeId}`);
                const result = await response.json();

                if (result.success) {
                    statusDiv.className = 'mt-4 p-3 rounded alert alert-success';
                    statusDiv.innerHTML = `<strong>${result.message}</strong><br>${result.employee_info.full_name}`;
                } else {
                    statusDiv.className = 'mt-4 p-3 rounded alert alert-danger';
                    statusDiv.innerText = result.message;
                }
            } catch (e) {
                statusDiv.className = 'mt-4 p-3 rounded alert alert-danger';
                statusDiv.innerText = 'Error connecting to server.';
            }
        }

        function initKiosk() {
            const html5QrCode = new Html5Qrcode("kioskScanner");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                processScan(decodedText);
            }).catch(err => console.error(err));
        }

        $(document).ready(function() {
            initKiosk();
        });

        function updateClock() {
            const clockElement = document.getElementById('liveClock');
            if (clockElement) {
                const now = new Date();
                clockElement.textContent = now.toLocaleTimeString();
            }
        }
        setInterval(updateClock, 1000);
        $('#realtimeRefresh').click(function() {
            location.reload();
        });
    </script>
</div>

<?php require_once __DIR__. '/../layout/content_footer.php';?>
<?php require_once __DIR__. '/../layout/page_end.php';?>