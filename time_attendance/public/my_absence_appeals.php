<?php
/**
 * Employee Absence & Late Appeal Interface
 * Allows employees to submit excuses for absences and late arrivals
 */

require_once "../app/controllers/AuthController.php";
require_once "../app/models/AbsenceLateMgmt.php";
require_once "../app/core/Session.php";

Session::start();

// Check authentication
if (!AuthController::isAuthenticated()) {
    header("Location: ../../login_form.php");
    exit;
}

$absenceLateMgmt = new AbsenceLateMgmt();
$employee_id = $_SESSION['user']['employee_id'] ?? null;

// Get employee's records
$filters = [
    'employee_id' => $employee_id,
    'excuse_status' => $_GET['status'] ?? null,
    'limit' => 50
];

$records = $absenceLateMgmt->getRecords($filters);
$summary = $absenceLateMgmt->getEmployeeSummary($employee_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Absence & Late Appeals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-top: 3px solid #007bff;
        }

        .summary-card h4 {
            color: #666;
            font-size: 12px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .tab-btn.active {
            color: #007bff;
            border-bottom-color: #007bff;
        }

        .records-list {
            display: grid;
            gap: 15px;
        }

        .record-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }

        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .record-type {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .record-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .record-badge.absent {
            background: #ffe5e5;
            color: #dc3545;
        }

        .record-badge.late {
            background: #fff3cd;
            color: #ff9800;
        }

        .record-badge.pending {
            background: #e3f2fd;
            color: #2196f3;
        }

        .record-badge.approved {
            background: #e8f5e9;
            color: #28a745;
        }

        .record-badge.rejected {
            background: #ffebee;
            color: #dc3545;
        }

        .record-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .info-item {
            color: #666;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 3px;
        }

        .record-reason {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .record-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="margin-bottom: 10px;">
            <i class="fas fa-calendar-times"></i> My Absence & Late Appeals
        </h1>
        <p style="color: #666; margin-bottom: 30px;">Submit and track your excuses for absences and late arrivals</p>

        <!-- Summary -->
        <div class="summary-grid">
            <div class="summary-card">
                <h4>This Month</h4>
                <div class="summary-value"><?php echo $summary['total_records'] ?? 0; ?></div>
                <small>Total Records</small>
            </div>
            <div class="summary-card">
                <h4>Absences</h4>
                <div class="summary-value"><?php echo $summary['absent_count'] ?? 0; ?></div>
            </div>
            <div class="summary-card">
                <h4>Late Arrivals</h4>
                <div class="summary-value"><?php echo $summary['late_count'] ?? 0; ?></div>
            </div>
            <div class="summary-card">
                <h4>Pending</h4>
                <div class="summary-value"><?php echo $summary['pending_count'] ?? 0; ?></div>
            </div>
            <div class="summary-card">
                <h4>Approved</h4>
                <div class="summary-value"><?php echo $summary['excused_count'] ?? 0; ?></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="filterByStatus(null)">All</button>
            <button class="tab-btn" onclick="filterByStatus('PENDING')">Pending</button>
            <button class="tab-btn" onclick="filterByStatus('APPROVED')">Approved</button>
            <button class="tab-btn" onclick="filterByStatus('REJECTED')">Rejected</button>
        </div>

        <!-- Records -->
        <div class="records-list">
            <?php if (count($records) > 0): ?>
                <?php foreach ($records as $record): ?>
                <div class="record-card">
                    <div class="record-header">
                        <div>
                            <div class="record-type">
                                <i class="fas fa-<?php echo $record['type'] === 'ABSENT' ? 'user-slash' : 'clock'; ?>"></i>
                                <?php echo $record['type'] === 'ABSENT' ? 'Absence' : 'Late Arrival'; ?>
                            </div>
                            <small><?php echo date('F d, Y', strtotime($record['absence_date'])); ?></small>
                        </div>
                        <div>
                            <span class="record-badge <?php echo strtolower($record['excuse_status']); ?>">
                                <?php echo $record['excuse_status']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="record-info">
                        <div class="info-item">
                            <span class="info-label">Submitted</span>
                            <?php echo date('M d, Y', strtotime($record['submitted_date'])); ?>
                        </div>
                        <?php if ($record['reviewed_date']): ?>
                        <div class="info-item">
                            <span class="info-label">Reviewed</span>
                            <?php echo date('M d, Y', strtotime($record['reviewed_date'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($record['reason']): ?>
                    <div class="record-reason">
                        <strong>Your Reason:</strong><br>
                        <?php echo htmlspecialchars($record['reason']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($record['approval_notes']): ?>
                    <div class="record-reason" style="background: #f0f8ff;">
                        <strong>HR Notes:</strong><br>
                        <?php echo htmlspecialchars($record['approval_notes']); ?>
                    </div>
                    <?php endif; ?>

                    <div class="record-actions">
                        <?php if ($record['excuse_status'] === 'PENDING'): ?>
                        <button class="btn btn-primary" onclick="editExcuse(<?php echo $record['record_id']; ?>)">
                            <i class="fas fa-edit"></i> Edit Excuse
                        </button>
                        <?php endif; ?>
                        <button class="btn btn-secondary" onclick="viewDetails(<?php echo $record['record_id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No records found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit/Submit Modal -->
    <div class="modal-overlay" id="excuseModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Submit Excuse</h2>
                <button class="close-btn" onclick="closeModal('excuseModal')">&times;</button>
            </div>
            <form id="excuseForm">
                <div class="form-group">
                    <label>Reason for Absence/Late</label>
                    <textarea id="excuseReason" placeholder="Explain why you were absent or late..." required></textarea>
                    <small>Be as detailed as possible to help HR understand your situation</small>
                </div>
                <div class="form-group">
                    <label>Supporting Document (Optional)</label>
                    <input type="file" id="supportingDoc">
                    <small>Upload medical certificate, travel document, etc. if available</small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('excuseModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Excuse</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/toastr/toastr.min.js"></script>
    <script>
        let currentRecordId = null;

        function filterByStatus(status) {
            let url = 'my_absence_appeals.php';
            if (status) {
                url += `?status=${status}`;
            }
            window.location.href = url;
        }

        function editExcuse(recordId) {
            currentRecordId = recordId;
            fetch(`../app/api/absence_late_management.php?action=get_record&record_id=${recordId}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        document.getElementById('excuseReason').value = res.data.reason || '';
                        openModal('excuseModal');
                    }
                })
                .catch(err => toastr.error('Failed to load record'));
        }

        function viewDetails(recordId) {
            fetch(`../app/api/absence_late_management.php?action=get_record&record_id=${recordId}`)
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const record = res.data;
                        let details = `
                            <div style="margin-bottom: 15px;">
                                <strong>Type:</strong> ${record.type}<br>
                                <strong>Date:</strong> ${new Date(record.absence_date).toLocaleDateString()}<br>
                                <strong>Status:</strong> ${record.excuse_status}<br>
                                <strong>Reason:</strong><br>
                                <p style="background: #f8f9fa; padding: 10px; border-radius: 4px;">
                                    ${htmlEscape(record.reason || 'Not provided')}
                                </p>
                                ${record.approval_notes ? `
                                    <strong>HR Review Notes:</strong><br>
                                    <p style="background: #f0f8ff; padding: 10px; border-radius: 4px;">
                                        ${htmlEscape(record.approval_notes)}
                                    </p>
                                ` : ''}
                            </div>
                        `;
                        alert(details);
                    }
                })
                .catch(err => toastr.error('Failed to load details'));
        }

        document.getElementById('excuseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const reason = document.getElementById('excuseReason').value;

            fetch('../app/api/absence_late_management.php?action=submit_excuse', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    record_id: currentRecordId,
                    reason: reason
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    toastr.success(res.message);
                    closeModal('excuseModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(res.message);
                }
            })
            .catch(err => toastr.error('Failed to submit excuse'));
        });

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
