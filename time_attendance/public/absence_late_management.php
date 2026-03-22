<?php
/**
 * Absence & Late Management Interface
 * HR interface for managing absence and late arrival records
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/AbsenceLateMgmt.php";
require_once "../app/models/Employee.php";
require_once "../app/core/Session.php";

Session::start();

// Check authentication
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

// Check HR/Time permission
if (!AuthController::hasRole('time') && !AuthController::hasRole('hr')) {
    header("Location: employee_dashboard.php");
    exit;
}

$absenceLateMgmt = new AbsenceLateMgmt();
$employeeModel = new Employee();

// Get initial data
$filters = [
    'excuse_status' => $_GET['status'] ?? 'PENDING',
    'type' => $_GET['type'] ?? null,
    'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
    'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
    'limit' => 50
];

$records = $absenceLateMgmt->getRecords($filters);
$pendingCount = count($absenceLateMgmt->getPendingApprovals(100));
$summaryStats = $absenceLateMgmt->getSummaryStats(['start_date' => $filters['start_date'], 'end_date' => $filters['end_date']]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absence & Late Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
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

        .container, .container-fluid {
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 100%;
        }

        .preloader {
            margin: 0;
            padding: 0;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 35px;
            margin-top: 0;
            background: linear-gradient(135deg, #003d82 0%, #005ba8 100%);
            padding: 35px;
            border-radius: 0;
            box-shadow: 0 4px 20px rgba(0, 61, 130, 0.15);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .page-title i {
            font-size: 36px;
            opacity: 0.95;
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 61, 130, 0.08);
            border: 2px solid rgba(0, 61, 130, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #003d82, #005ba8);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #003d82, #005ba8);
            border-radius: 50%;
            opacity: 0.05;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 61, 130, 0.2);
            border-color: rgba(0, 61, 130, 0.15);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover::after {
            opacity: 0.08;
        }

        .stat-card h4 {
            color: #666;
            font-size: 13px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #003d82;
            position: relative;
            z-index: 1;
        }

        .stat-card.pending .stat-value {
            color: #ffc107;
        }

        .stat-card.approved .stat-value {
            color: #28a745;
        }

        .stat-card.rejected .stat-value {
            color: #dc3545;
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
    </style>
</head>
<body>
    <?php require_once "../app/components/Sidebar.php"; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title">
                    <i class="fas fa-calendar-times"></i>
                    <span>Absence & Late Management</span>
                </div>
                <div class="page-subtitle">Review and manage employee absence and late arrival records</div>
            </div>

            <div class="absence-late-container">

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Records</h4>
                        <div class="stat-value"><?php echo $summaryStats['total_records'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <h4>Total Absences</h4>
                        <div class="stat-value"><?php echo $summaryStats['total_absents'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card">
                        <h4>Total Late Arrivals</h4>
                        <div class="stat-value"><?php echo $summaryStats['total_lates'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card pending">
                        <h4>Pending Reviews</h4>
                        <div class="stat-value"><?php echo $summaryStats['pending_reviews'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card approved">
                        <h4>Approved Excuses</h4>
                        <div class="stat-value"><?php echo $summaryStats['approved_excuses'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card rejected">
                        <h4>Rejected Excuses</h4>
                        <div class="stat-value"><?php echo $summaryStats['rejected_excuses'] ?? 0; ?></div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section">
                    <input type="date" id="startDate" value="<?php echo $filters['start_date']; ?>" placeholder="Start Date">
                    <input type="date" id="endDate" value="<?php echo $filters['end_date']; ?>" placeholder="End Date">
                    
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="PENDING" <?php echo $filters['excuse_status'] === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                        <option value="APPROVED" <?php echo $filters['excuse_status'] === 'APPROVED' ? 'selected' : ''; ?>>Approved</option>
                        <option value="REJECTED" <?php echo $filters['excuse_status'] === 'REJECTED' ? 'selected' : ''; ?>>Rejected</option>
                    </select>

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
                <div class="records-table">
                    <?php if (count($records) > 0): ?>
                    <table id="recordsTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Excused</th>
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
                                    <span class="badge badge-<?php echo strtolower($record['excuse_status']); ?>">
                                        <?php echo $record['excuse_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($record['is_excused']): ?>
                                        <?php if ($record['excuse_type'] === 'APPROVED_LEAVE'): ?>
                                            <span class="badge badge-success" title="Excused due to approved leave request">
                                                <i class="fas fa-check-circle"></i> Leave Approved
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-excused">
                                                <i class="fas fa-check"></i> Excused
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-unexcused">
                                            <i class="fas fa-times"></i> Unexcused
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($record['excuse_type'] === 'APPROVED_LEAVE'): ?>
                                        <em>Approved Leave</em>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars(substr($record['reason'] ?? '', 0, 30)); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($record['submitted_date'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" onclick="viewRecord(<?php echo $record['record_id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <?php if ($record['excuse_status'] === 'PENDING' && $record['excuse_type'] !== 'APPROVED_LEAVE' && (AuthController::hasRole('time') || AuthController::hasRole('hr'))): ?>
                                        <button class="btn-action btn-approve" onclick="approveExcuse(<?php echo $record['record_id']; ?>)">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn-action btn-reject" onclick="rejectExcuse(<?php echo $record['record_id']; ?>)">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <?php endif; ?>
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

    <!-- Review Modal -->
    <div class="modal-overlay" id="reviewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Review Excuse</h2>
                <button class="close-btn" onclick="closeModal('reviewModal')">&times;</button>
            </div>
            <form id="reviewForm">
                <div class="form-group">
                    <label>Decision</label>
                    <select id="reviewDecision" required>
                        <option value="">Select decision...</option>
                        <option value="APPROVED">Approve</option>
                        <option value="REJECTED">Reject</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Review Notes</label>
                    <textarea id="reviewNotes" placeholder="Enter your review notes..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('reviewModal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/toastr/toastr.min.js"></script>
    <script>
        let currentRecordId = null;

        function applyFilters() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const status = document.getElementById('statusFilter').value;
            const type = document.getElementById('typeFilter').value;

            let url = 'absence_late_management.php?';
            if (startDate) url += `start_date=${startDate}&`;
            if (endDate) url += `end_date=${endDate}&`;
            if (status) url += `status=${status}&`;
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
                                <label>Status</label>
                                <p><span class="badge badge-${record.excuse_status.toLowerCase()}">${record.excuse_status}</span></p>
                            </div>
                            <div class="form-group">
                                <label>Reason</label>
                                <p>${htmlEscape(record.reason || 'Not provided')}</p>
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <p>${htmlEscape(record.notes || 'No notes')}</p>
                            </div>
                            <div class="form-group">
                                <label>Approval Notes</label>
                                <p>${htmlEscape(record.approval_notes || 'Not reviewed yet')}</p>
                            </div>
                        `;
                        document.getElementById('recordDetails').innerHTML = html;
                        openModal('viewModal');
                    }
                })
                .catch(err => toastr.error('Failed to load record'));
        }

        function approveExcuse(recordId) {
            currentRecordId = recordId;
            document.getElementById('reviewDecision').value = 'APPROVED';
            document.getElementById('reviewNotes').value = '';
            openModal('reviewModal');
        }

        function rejectExcuse(recordId) {
            currentRecordId = recordId;
            document.getElementById('reviewDecision').value = 'REJECTED';
            document.getElementById('reviewNotes').value = '';
            openModal('reviewModal');
        }

        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const status = document.getElementById('reviewDecision').value;
            const notes = document.getElementById('reviewNotes').value;

            fetch('../app/api/absence_late_management.php?action=review_excuse', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    record_id: currentRecordId,
                    status: status,
                    notes: notes
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    toastr.success(res.message);
                    closeModal('reviewModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(res.message);
                }
            })
            .catch(err => toastr.error('Failed to submit review'));
        });

        function generateReport() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const status = document.getElementById('statusFilter').value;
            const type = document.getElementById('typeFilter').value;

            let url = '../app/api/absence_late_management.php?action=get_report';
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            if (status) url += `&status=${status}`;
            if (type) url += `&type=${type}`;

            fetch(url)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        downloadReport(res.data);
                    }
                })
                .catch(err => toastr.error('Failed to generate report'));
        }

        function downloadReport(data) {
            let csv = 'Employee,Department,Type,Date,Status,Excused,Reason,Reviewed By,Review Date\n';
            data.forEach(record => {
                csv += `"${record.full_name}","${record.department}","${record.type}","${record.absence_date}","${record.excuse_status}","${record.is_excused ? 'Yes' : 'No'}","${record.reason || ''}","${record.reviewed_by_name || ''}","${record.reviewed_date || ''}"\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `absence-late-report-${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
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
</body>
</html>
