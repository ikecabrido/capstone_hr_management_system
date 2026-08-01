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
$page_head_extra = "<link rel=\"stylesheet\" href=\"../assets/style.css\">\n<link rel=\"stylesheet\" href=\"../assets/dashboard.css\">\n<link rel=\"stylesheet\" href=\"../assets/adminlte-overrides.css\">\n<link rel=\"stylesheet\" href=\"../assets/hr-template.css\">";
?>
<?php require_once __DIR__. '/../layout/page_start.php';?>
<?php require_once __DIR__. '/../layout/sidebar.php';?>
<?php $page_title = 'Time & Attendance Dashboard'; $page_subtitle = 'Real-time attendance and HR analytics'; $page_icon = 'fa-chart-line';?>
<?php require_once __DIR__. '/../layout/content_header.php';?>

    <style>
        /* Copied from Absence & Late Management template for consistent HR UI */

        /* Stats Grid - Dashboard Style */
        .stats-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-bottom: 24px;
        }

        .stat-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(13, 71, 161, 0.08);
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
            padding: 18px 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }

        .stat-card.pending::before,
        .stat-card.approved::before {
            display: none;
        }

        .stat-card.rejected::before {
            background: linear-gradient(180deg, #c62828 0%, #ef5350 100%);
        }

        .stat-card .info-box-icon {
            width: 54px;
            height: 54px;
            min-width: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            color: #fff;
            font-size: 22px;
            box-shadow: inset 0 -4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-card .info-box-icon.bg-primary {
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
        }

        .stat-card .info-box-icon.bg-success {
            background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
        }

        .stat-card .info-box-icon.bg-warning {
            background: linear-gradient(135deg, #ef6c00 0%, #f9a825 100%);
        }

        .stat-card .info-box-icon.bg-info {
            background: linear-gradient(135deg, #006064 0%, #00acc1 100%);
        }

        .stat-card .info-box-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .stat-card .info-box-text {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            line-height: 1.2;
        }

        .stat-card .info-box-number {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-card .info-box-unit {
            font-size: 13px;
            color: #64748b;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .filter-section input,
        .filter-section select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .filter-section input:focus,
        .filter-section select:focus {
            outline: none;
            border-color: #003d82;
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.1);
        }

        /* Records Table */
        .records-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
        }

        .records-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .records-table thead {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
        }

        .records-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .records-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e8eef7;
        }

        .records-table tbody tr {
            transition: all 0.3s ease;
        }

        .records-table tbody tr:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 61, 130, 0.05);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-absent {
            background: #ffe5e5;
            color: #dc3545;
        }

        .badge-late {
            background: #fff3cd;
            color: #ff9800;
        }

        .badge-pending {
            background: #e3f2fd;
            color: #2196f3;
        }

        .badge-approved {
            background: #e8f5e9;
            color: #28a745;
        }

        .badge-rejected {
            background: #ffebee;
            color: #dc3545;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-approve {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
        }

        .btn-approve:hover {
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
        }

        .btn-reject:hover {
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
        }

        .btn-view {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 61, 130, 0.2);
        }

        .btn-view:hover {
            box-shadow: 0 6px 16px rgba(0, 61, 130, 0.4);
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1500;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e8eef7;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: #003d82;
            font-weight: 700;
        }

        .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
        }

        .modal-header .close-btn:hover {
            color: #333;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #003d82;
            box-shadow: 0 0 0 3px rgba(0, 61, 130, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e8eef7;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 61, 130, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 61, 130, 0.3);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state p {
            font-size: 16px;
            color: #666;
        }

        @media (max-width: 768px) {
            .filter-section {
                grid-template-columns: 1fr;
            }

            .records-table table {
                font-size: 12px;
            }

            .records-table th,
            .records-table td {
                padding: 8px;
            }
        }

        /* AdminLTE Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0d47a1 0%, #0b3c91 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 99999;
        }

        .preloader.flex-column {
            flex-direction: column;
        }

        .preloader.justify-content-center {
            justify-content: center;
        }

        .preloader.align-items-center {
            align-items: center;
        }

        .preloader img {
            max-width: 100px;
            height: auto;
            display: block;
        }

        .animation__wobble {
            animation: wobble 2.5s infinite ease-in-out;
        }

        @keyframes wobble {
            0% { transform: translateX(0); }
            15% { transform: translateX(-5px) rotate(-5deg); }
            30% { transform: translateX(3px) rotate(3deg); }
            45% { transform: translateX(-3px) rotate(-3deg); }
            60% { transform: translateX(2px) rotate(2deg); }
            75% { transform: translateX(-1px) rotate(-1deg); }
            100% { transform: translateX(0); }
        }
    </style>

    <div class="ta-dashboard absence-late-container glass-panel">
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
                        <a href="shifts.php" style="background: #FF9800; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;">Manage Shifts</a>
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
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="info-box-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Employees</span>
                        <span class="info-box-number"><?php echo $allEmployees;?></span>
                        <span class="info-box-unit">Active employees</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="info-box-icon bg-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Present Today</span>
                        <span class="info-box-number"><?php echo $todayStats['present_count']?? 0;?></span>
                        <span class="info-box-unit"><?php echo $attendancePercentage;?>% attendance</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="info-box-icon bg-warning">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="info-box-content">
                        <span class="info-box-text">Absent Today</span>
                        <span class="info-box-number"><?php echo $todayStats['absent_count']?? 0;?></span>
                        <span class="info-box-unit">Need follow-up</span>
                    </div>
                </div>

                <div class="stat-card">
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

            <!-- Employee QR and Camera moved to separate sidebar tabs -->

            <!-- Real-time Updates Widget -->
            <!-- Live Activity Feed removed from dashboard to simplify UI -->

            <!-- Today's Attendance Table -->
            <div class="container">
                <h2>Today's Attendance (<?php echo count($todayRecords);?> employees)</h2>

                <div class="filter-controls" style="flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
                    <input type="text" id="attendanceSearch" placeholder="Search by name, employee #, or department..." style="flex: 1 1 320px;" />
                    <select id="attendanceSort" style="width: 220px;">
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

                <div id="attendancePagination" style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px; gap: 10px; flex-wrap: wrap;">
                    <div id="attendancePageInfo" style="font-size: 14px; color: #555;"></div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <button type="button" id="attendancePrev" class="btn btn-sm btn-secondary">Previous</button>
                        <button type="button" id="attendanceNext" class="btn btn-sm btn-secondary">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee QR moved to Employee QR Directory tab -->

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
            // legacy stub - directory removed in new UI
        }

        function filterQRDirectory() {
            // legacy stub - directory removed in new UI
        }

        function renderQRDirectory() {
            // Populate modal select for employee QR selection.
            const select = document.getElementById('qrEmployeeSelect');
            if (!select) return;
            select.innerHTML = '';
            employees.forEach(emp => {
                const opt = document.createElement('option');
                opt.value = emp.employee_id;
                opt.text = `${emp.employee_no || emp.employee_id} - ${emp.full_name}`;
                select.appendChild(opt);
            });

            // When selection changes, auto-generate QR
                select.addEventListener('change', function() {
                    const id = this.value;
                    if (!id) return; // ignore placeholder/no-selection
                    const emp = employees.find(e => e.employee_id == id);
                    if (emp) {
                        viewQR(emp.employee_id, emp.full_name, emp.department || 'N/A', emp.position || 'N/A');
                    }
                });

                // add placeholder option; do not auto-open modal on page load
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.text = '-- Select Employee --';
                select.insertBefore(placeholder, select.firstChild);
        }

        let currentQRInstance = null;
        function viewQR(id, name, dept, pos) {
            $('#qrModal').modal('show');
            $('#modalEmpName').text(name);
            $('#modalEmpDetails').text(dept + ' - ' + pos);
            $('#qrModal').data('employeeId', id);

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
            let emp = null;
            if (id) {
                emp = employees.find(e => e.employee_id == id);
            } else {
                const sel = document.getElementById('qrEmployeeSelect');
                const selectedId = sel ? sel.value : null;
                emp = employees.find(e => e.employee_id == selectedId);
            }

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
        let attendanceCurrentPage = 1;
        const attendancePageSize = 20;

        function getStatus(record) {
            if (isHolidayToday) {
                return 'HOLIDAY';
            }
            if (!record.time_in) {
                return 'ABSENT';
            }
            const time = new Date(record.time_in);
            return time.getHours() > 9 ? 'LATE' : 'PRESENT';
        }

        function getStatusOrder(status) {
            switch (status) {
                case 'PRESENT': return 1;
                case 'LATE': return 2;
                case 'ABSENT': return 3;
                default: return 4;
            }
        }

        function renderAttendancePage(records) {
            const tbody = document.getElementById('attendanceBody');
            const pageInfo = document.getElementById('attendancePageInfo');
            const prevBtn = document.getElementById('attendancePrev');
            const nextBtn = document.getElementById('attendanceNext');

            tbody.innerHTML = '';

            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: #999;">No records found</td></tr>';
                pageInfo.textContent = 'Showing 0 of 0 records';
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                return;
            }

            const totalPages = Math.max(1, Math.ceil(records.length / attendancePageSize));
            attendanceCurrentPage = Math.min(attendanceCurrentPage, totalPages);
            const start = (attendanceCurrentPage - 1) * attendancePageSize;
            const pageRecords = records.slice(start, start + attendancePageSize);

            pageRecords.forEach(record => {
                const row = document.createElement('tr');
                const status = getStatus(record);
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

            pageInfo.textContent = `Showing ${start + 1} to ${Math.min(start + attendancePageSize, records.length)} of ${records.length} records`;
            prevBtn.disabled = attendanceCurrentPage === 1;
            nextBtn.disabled = attendanceCurrentPage === totalPages;
        }

        function filterAndSort() {
            attendanceCurrentPage = 1;
            const searchTerm = document.getElementById('attendanceSearch').value.toLowerCase();
            const sortOption = document.getElementById('attendanceSort').value;

            let filtered = attendanceData.filter(record => {
                const name = record.full_name.toLowerCase();
                const dept = (record.department || '').toLowerCase();
                return name.includes(searchTerm) || dept.includes(searchTerm);
            });

            filtered.sort((a, b) => {
                const statusA = getStatus(a);
                const statusB = getStatus(b);
                const statusDiff = getStatusOrder(statusA) - getStatusOrder(statusB);
                if (statusDiff !== 0) {
                    return statusDiff;
                }

                switch (sortOption) {
                    case 'name': return a.full_name.localeCompare(b.full_name);
                    case 'name-desc': return b.full_name.localeCompare(a.full_name);
                    case 'time': return new Date(b.time_in || 0) - new Date(a.time_in || 0);
                    case 'time-asc': return new Date(a.time_in || 0) - new Date(b.time_in || 0);
                    case 'department': return (a.department || '').localeCompare(b.department || '');
                    case 'status': return 0;
                    default: return 0;
                }
            });

            renderAttendancePage(filtered);
        }

        function escapeHtml(text) {
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return text? String(text).replace(/[&<>"']/g, m => map[m]) : '';
        }

        document.getElementById('attendancePrev').addEventListener('click', function() {
            if (attendanceCurrentPage > 1) {
                attendanceCurrentPage -= 1;
                filterAndSort();
            }
        });

        document.getElementById('attendanceNext').addEventListener('click', function() {
            attendanceCurrentPage += 1;
            filterAndSort();
        });

        $(document).ready(function() {
            renderQRDirectory();
            filterAndSort();
            $('#attendanceSearch').on('keyup', filterAndSort);
            $('#attendanceSort').on('change', filterAndSort);
        });

        // Camera and employee-QR functionality moved to separate pages (`qr_scanner.php`, `employee_qr_list.php`).

        function updateClock() {
            const clockElement = document.getElementById('liveClock');
            if (clockElement) {
                const now = new Date();
                clockElement.textContent = now.toLocaleTimeString();
            }
        }
        setInterval(updateClock, 1000);
    </script>
</div>

<?php require_once __DIR__. '/../layout/content_footer.php';?>
<?php require_once __DIR__. '/../layout/page_end.php';?>