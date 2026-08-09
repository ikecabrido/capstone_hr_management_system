<?php
/**
 * Absence & Late Management Interface
 * HR interface for managing absence and late arrival records
 */

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/models/AbsenceLateMgmt.php';
require_once __DIR__ . '/../app/models/Employee.php';
require_once __DIR__ . '/../app/core/Session.php';

Session::start();

// Check authentication
if (!AuthController::isAuthenticated()) {
    header('Location: ' . dirname(__DIR__) . '/../../login_form.php');
    exit;
}

// Check HR/Time permission
if (!AuthController::hasRole('time') && !AuthController::hasRole('hr')) {
    header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
    exit;
}

$absenceLateMgmt = new AbsenceLateMgmt();
$employeeModel = new Employee();
$current_page = 'absence_late_management.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';

// Get initial data
$filters = [
    'type' => $_GET['type'] ?? null,
    'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
    'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
    'limit' => 50
];

$records = $absenceLateMgmt->getRecords($filters);
$summaryStats = $absenceLateMgmt->getSummaryStats(['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]);
?>

<?php $page_title = 'Absence & Late Management'; ?>
<?php $page_head_extra = "<link rel=\"stylesheet\" href=\"../assets/style.css\">\n<link rel=\"stylesheet\" href=\"../assets/dashboard.css\">\n<link rel=\"stylesheet\" href=\"../assets/adminlte-overrides.css\">"; ?>
<?php require_once __DIR__ . '/../layout/page_start.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>
<?php $page_title = 'Absence & Late Management'; $page_subtitle = 'Manage absence and late records'; $page_icon = 'fa-calendar-times'; ?>
<?php require_once __DIR__ . '/../layout/content_header.php'; ?>

<style>

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
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, #0d47a1 0%, #42a5f5 100%);
            border-radius: 0 8px 8px 0;
        }

        .stat-card.pending::before {
            background: linear-gradient(180deg, #0097a7 0%, #00bcd4 100%);
        }

        .stat-card.approved::before {
            background: linear-gradient(180deg, #2e7d32 0%, #66bb6a 100%);
        }

        .stat-card.rejected::before {
            background: linear-gradient(180deg, #c62828 0%, #ef5350 100%);
        }

        .stat-card .stat-icon {
            width: 56px;
            min-width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            color: white;
            font-size: 24px;
            box-shadow: inset 0 -4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-card .stat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
        }

        .stat-card .stat-content h4 {
            color: #64748b;
            font-size: 12px;
            margin: 0;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.2;
        }

        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            margin-top: 0;
        }

        body.dark-mode .stat-card {
            background: linear-gradient(135deg, #1e1e1e 0%, #252b32 100%) !important;
            border-color: #3f4650 !important;
            color: #f3f4f6 !important;
        }

        body.dark-mode .stat-card .stat-content h4 {
            color: #cbd5e1 !important;
        }

        body.dark-mode .stat-card .stat-value {
            color: #ffffff !important;
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
            color: #ffffff !important;
        }

        body.dark-mode .records-table thead,
        body.dark-mode .records-table th {
            background: linear-gradient(135deg, #173b63 0%, #245b91 100%) !important;
            color: #ffffff !important;
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

        body.dark-mode .records-table,
        body.dark-mode .records-table table,
        body.dark-mode .records-table tbody,
        body.dark-mode .records-table tbody tr,
        body.dark-mode .records-table tbody tr td {
            background: #1e1e1e !important;
            color: #f1f5f9 !important;
            border-color: #3f4650 !important;
        }

        body.dark-mode .records-table tbody tr:hover,
        body.dark-mode .records-table tbody tr:hover td {
            background: #2a3440 !important;
            color: #ffffff !important;
            box-shadow: none;
        }

        body.dark-mode .records-table .badge-absent {
            background: #5a2328 !important;
            color: #ffd6d9 !important;
        }

        body.dark-mode .records-table .badge-late {
            background: #5a431a !important;
            color: #ffe6a6 !important;
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
            0% {
                transform: translateX(0);
            }
            15% {
                transform: translateX(-5px) rotate(-5deg);
            }
            30% {
                transform: translateX(3px) rotate(3deg);
            }
            45% {
                transform: translateX(-3px) rotate(-3deg);
            }
            60% {
                transform: translateX(2px) rotate(2deg);
            }
            75% {
                transform: translateX(-1px) rotate(-1deg);
            }
            100% {
                transform: translateX(0);
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
            0% {
                transform: translateX(0);
            }
            15% {
                transform: translateX(-5px) rotate(-5deg);
            }
            30% {
                transform: translateX(3px) rotate(3deg);
            }
            45% {
                transform: translateX(-3px) rotate(-3deg);
            }
            60% {
                transform: translateX(2px) rotate(2deg);
            }
            75% {
                transform: translateX(-1px) rotate(-1deg);
            }
            100% {
                transform: translateX(0);
            }
        }

    </style>
            <!-- Page Header -->
            <div class="absence-late-container glass-panel">

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #0d47a1, #1976d2);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h4>Total Records</h4>
                            <div class="stat-value"><?php echo $summaryStats['total_records'] ?? 0; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #c62828, #e53935);">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div class="stat-content">
                            <h4>Total Absences</h4>
                            <div class="stat-value"><?php echo $summaryStats['total_absents'] ?? 0; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f57f17, #fbc02d);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h4>Total Late Arrivals</h4>
                            <div class="stat-value"><?php echo $summaryStats['total_lates'] ?? 0; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section glass-panel">
                    <input type="date" id="startDate" value="<?php echo $filters['start_date']; ?>" placeholder="Start Date">
                    <input type="date" id="endDate" value="<?php echo $filters['end_date']; ?>" placeholder="End Date">
                    
                    <select id="typeFilter">
                        <option value="">All Types</option>
                        <option value="ABSENT" <?php echo $filters['type'] === 'ABSENT' ? 'selected' : ''; ?>>Absence</option>
                        <option value="LATE" <?php echo $filters['type'] === 'LATE' ? 'selected' : ''; ?>>Late</option>
                    </select>

                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>

                    <button class="btn btn-secondary" onclick="generateReport()">
                        <i class="fas fa-file-pdf"></i> Generate Report
                    </button>
                </div>

                <!-- Records Table -->
                <div class="records-table glass-panel">
                    <?php if (count($records) > 0): ?>
                    <table id="recordsTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Late Hours</th>
                                <th>Working Hours</th>
                                <th>Reason</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['department'] ?? 'N/A'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($record['absence_date'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $record['type'] === 'ABSENT' ? 'badge-absent' : 'badge-late'; ?>">
                                        <?php echo $record['type']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo isset($record['late_minutes']) && $record['late_minutes'] !== null ? number_format((float)$record['late_minutes'] / 60, 2) . 'h' : 'N/A'; ?>
                                </td>
                                <td>
                                    <?php echo isset($record['total_hours_worked']) && $record['total_hours_worked'] !== null ? number_format((float)$record['total_hours_worked'], 2) . 'h' : 'N/A'; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars(substr($record['reason'] ?? '', 0, 30)); ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($record['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="viewRecord(<?php echo $record['record_id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No absence or late records found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- View Record Modal -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Absence/Late Details</h2>
                <button class="close-btn" onclick="closeModal('viewModal')">&times;</button>
            </div>
            <div id="recordDetails"></div>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const type = document.getElementById('typeFilter').value;

            let url = 'absence_late_management.php?';
            if (startDate) url += `start_date=${startDate}&`;
            if (endDate) url += `end_date=${endDate}&`;
            if (type) url += `type=${type}`;

            window.location.href = url;
        }

        function viewRecord(recordId) {
            fetch(`../app/api/absence_late_management.php?action=get_record&record_id=${recordId}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const record = res.data;
                        let html = `
                            <div class="form-group">
                                <label>Employee</label>
                                <p>${htmlEscape(record.full_name)}</p>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <p>${htmlEscape(record.department || 'N/A')}</p>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <p><span class="badge badge-${record.type.toLowerCase()}">${record.type}</span></p>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <p>${new Date(record.absence_date).toLocaleDateString()}</p>
                            </div>
                            <div class="form-group">
                                <label>Reason</label>
                                <p>${htmlEscape(record.reason || 'Not provided')}</p>
                            </div>
                            <div class="form-group">
                                <label>Late Hours</label>
                                <p>${record.late_minutes !== null ? `${(parseFloat(record.late_minutes) / 60).toFixed(2)}h` : 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Time In</label>
                                <p>${record.time_in ? new Date(record.time_in).toLocaleString() : 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Time Out</label>
                                <p>${record.time_out ? new Date(record.time_out).toLocaleString() : 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Working Hours</label>
                                <p>${record.total_hours_worked !== null ? `${parseFloat(record.total_hours_worked).toFixed(2)}h` : 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Regular Hours</label>
                                <p>${record.regular_hours !== null ? `${parseFloat(record.regular_hours).toFixed(2)}h` : 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Overtime Hours</label>
                                <p>${record.overtime_hours !== null ? `${parseFloat(record.overtime_hours).toFixed(2)}h` : 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <p>${htmlEscape(record.notes || 'No notes')}</p>
                            </div>
                        `;
                        document.getElementById('recordDetails').innerHTML = html;
                        openModal('viewModal');
                    }
                })
                .catch(err => toastr.error('Failed to load record'));
        }

        function generateReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const type = document.getElementById('typeFilter').value;

            let url = '../app/api/absence_late_management.php?action=get_report';
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            if (type) url += `&type=${type}`;

            fetch(url)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        downloadPdfReport(res.data, startDate, endDate, type);
                    } else {
                        toastr.error(res.message || 'Failed to generate report');
                    }
                })
                .catch(err => toastr.error('Failed to generate report'));
        }

        function downloadPdfReport(data, startDate, endDate, type) {
            const reportWindow = window.open('', '_blank', 'width=1200,height=800');
            if (!reportWindow) {
                toastr.error('Please allow pop-ups to generate the PDF report');
                return;
            }

            const reportRows = data.length ? data.map(record => `
                <tr>
                    <td>${htmlEscape(record.full_name || 'N/A')}</td>
                    <td>${htmlEscape(record.department || 'N/A')}</td>
                    <td><span class="type ${String(record.type).toLowerCase()}">${htmlEscape(record.type || 'N/A')}</span></td>
                    <td>${htmlEscape(record.absence_date || 'N/A')}</td>
                    <td>${htmlEscape(record.reason || 'Not provided')}</td>
                </tr>`).join('') : '<tr><td colspan="5" class="empty">No records found for the selected filters.</td></tr>';

            const title = 'Absence & Late Management Report';
            const generated = new Date().toLocaleString();
            const filters = `${startDate || 'All dates'} to ${endDate || 'All dates'}${type ? ` · Type: ${type}` : ''}`;
            const logoUrl = new URL('../Bestlink College of the Philippines.jpeg', window.location.href).href;

            reportWindow.document.write(`<!doctype html><html><head><title>${title}</title><style>
                @page { size: A4 landscape; margin: 14mm; }
                * { box-sizing: border-box; }
                body { margin: 0; color: #172b4d; font-family: Arial, sans-serif; font-size: 11px; }
                .institution-header { display: flex; align-items: center; justify-content: center; gap: 16px; padding: 0 0 12px; border-bottom: 1px solid #dbe5f0; margin-bottom: 16px; }
                .institution-header img { width: 66px; height: 66px; object-fit: contain; display: block; flex: 0 0 auto; }
                .institution-details { text-align: left; }
                .institution-name { margin: 0; color: #0b4175; font-size: 18px; font-weight: 800; letter-spacing: .2px; }
                .institution-address { margin: 4px 0 0; color: #667085; font-size: 10px; }
                .report-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0b5cab; padding-bottom: 14px; margin-bottom: 18px; }
                h1 { color: #0b4175; font-size: 23px; margin: 0 0 6px; }
                .subtitle, .meta { color: #667085; margin: 3px 0; }
                .summary { display: inline-block; padding: 9px 13px; border-radius: 8px; background: #eef6ff; color: #0b5cab; font-size: 12px; font-weight: bold; }
                table { width: 100%; border-collapse: collapse; table-layout: fixed; }
                th { background: #0b5cab; color: white; padding: 10px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; }
                td { padding: 9px 8px; border-bottom: 1px solid #dbe5f0; vertical-align: top; word-wrap: break-word; }
                tr:nth-child(even) { background: #f6f9fc; }
                .type, .status { display: inline-block; padding: 4px 7px; border-radius: 10px; font-size: 9px; font-weight: bold; }
                .type.absent { background: #fde8e8; color: #b42318; } .type.late { background: #fff2cc; color: #8a5b00; }
                .status.pending { background: #fff2cc; color: #8a5b00; } .status.approved { background: #dcfce7; color: #166534; } .status.rejected { background: #fde8e8; color: #b42318; }
                .empty { text-align: center; padding: 30px; color: #667085; }
                .footer { margin-top: 18px; color: #667085; font-size: 10px; text-align: right; }
                @media print { .no-print { display: none; } }
            </style></head><body>
                <div class="institution-header">
                    <img src="${logoUrl}" alt="Bestlink College of the Philippines logo">
                    <div class="institution-details">
                        <h2 class="institution-name">Bestlink College of the Philippines</h2>
                        <p class="institution-address">Lot 1 Ipo Road Brgy. Minuyan Proper, City of San Jose Del Monte, Bulacan</p>
                    </div>
                </div>
                <div class="report-header"><div><h1>${title}</h1><p class="subtitle">${filters}</p><p class="meta">Generated: ${generated}</p></div><div class="summary">${data.length} record${data.length === 1 ? '' : 's'}</div></div>
                <table><thead><tr><th>Employee</th><th>Department</th><th>Type</th><th>Date</th><th>Reason</th></tr></thead><tbody>${reportRows}</tbody></table>
                <div class="footer">Human Resource Management System · Official report</div>
                <button class="no-print" onclick="window.print()" style="margin-top:18px;padding:9px 16px;background:#0b5cab;color:#fff;border:0;border-radius:5px;cursor:pointer;"><i class="fas fa-print"></i> Print / Save as PDF</button>
            </body></html>`);
            reportWindow.document.close();
            reportWindow.focus();
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function htmlEscape(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
    <!-- Preloader Management Script -->
    <script>
        function hidePreloader() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.visibility = 'hidden';
            }
        }

        function showPreloader() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'flex';
                preloader.style.visibility = 'visible';
            }
        }

        window.addEventListener('load', hidePreloader);

        document.addEventListener('DOMContentLoaded', function() {
            // Delay hiding preloader so animation is visible
            setTimeout(hidePreloader, 6000);
            const navLinks = document.querySelectorAll('a');

            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const href = this.getAttribute('href');
                    if (href && !href.includes('logout') && !href.startsWith('javascript') && !href.startsWith('#') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                        showPreloader();
                    }
                });
            });
        });
    </script>

<?php require_once __DIR__ . '/../layout/content_footer.php'; ?>
<?php require_once __DIR__ . '/../layout/page_end.php'; ?>