<?php
session_start();
require_once __DIR__ . '/../../auth/auth.php';
require_once __DIR__ . '/../../auth/auth_check.php';
require_once __DIR__ . '/../controllers/payrollClearanceController.php';

$controller = new PayrollClearanceController();
$clearanceId = isset($_GET['clearance_id']) ? (int)$_GET['clearance_id'] : 0;
$clearance = $controller->getClearanceDetails($clearanceId);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payroll Clearance Form</title>
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
    <style>
        body { background: #f8f9fa; }
        .print-container { max-width: 900px; margin: 2rem auto; background: #fff; padding: 2rem; border: 1px solid #dee2e6; }
        .form-header { margin-bottom: 1.5rem; }
        .form-section { margin-bottom: 1.5rem; }
        .signature-line { margin-top: 3rem; }
        .signature-line span { display: inline-block; width: 250px; border-top: 1px solid #000; text-align: center; }
        .no-print { display: block; margin-bottom: 1rem; }
        @media print { .no-print { display: none; } }
    </style>
</head>

<body>
    <div class="print-container">
        <?php if (!$clearance): ?>
            <div class="alert alert-danger">Payroll clearance record not found.</div>
        <?php else: ?>
            <div class="no-print">
                <button type="button" onclick="window.print();" class="btn btn-primary"><i class="fas fa-print"></i> Print Form</button>
                <a href="payrollClearance.php" class="btn btn-secondary">Back</a>
            </div>
            <div class="form-header text-center">
                <h2>Payroll Clearance Form</h2>
                <p class="text-muted">Final payroll clearance for exit management settlement</p>
            </div>

            <div class="form-section">
                <h5>Payroll Clearance Details</h5>
                <table class="table table-borderless table-sm">
                    <tr><th>Form No.</th><td>PC-<?= str_pad($clearance['id'], 5, '0', STR_PAD_LEFT) ?></td></tr>
                    <tr><th>Request Date</th><td><?= htmlspecialchars($clearance['requested_at']) ?></td></tr>
                    <tr><th>Status</th><td><?= htmlspecialchars(ucfirst($clearance['status'])) ?></td></tr>
                    <tr><th>Approved By</th><td><?= htmlspecialchars($clearance['approved_by'] ?? '-') ?></td></tr>
                    <tr><th>Approved At</th><td><?= htmlspecialchars($clearance['approved_at'] ?? '-') ?></td></tr>
                </table>
            </div>

            <div class="form-section">
                <h5>Employee Information</h5>
                <table class="table table-borderless table-sm">
                    <tr><th>Employee ID</th><td><?= htmlspecialchars($clearance['employee_id']) ?></td></tr>
                    <tr><th>Name</th><td><?= htmlspecialchars($clearance['full_name']) ?></td></tr>
                    <tr><th>Department</th><td><?= htmlspecialchars($clearance['department']) ?></td></tr>
                    <tr><th>Position</th><td><?= htmlspecialchars($clearance['position']) ?></td></tr>
                    <tr><th>Resignation Type</th><td><?= htmlspecialchars($clearance['resignation_type']) ?></td></tr>
                    <tr><th>Last Working Date</th><td><?= htmlspecialchars($clearance['last_working_date']) ?></td></tr>
                </table>
            </div>

            <div class="form-section">
                <h5>Settlement Summary</h5>
                <table class="table table-borderless table-sm">
                    <tr><th>Settlement Date</th><td><?= htmlspecialchars($clearance['settlement_date']) ?></td></tr>
                    <tr><th>Gross Pay</th><td>₱<?= number_format((float)($clearance['gross_pay'] ?? 0), 2) ?></td></tr>
                    <tr><th>Total Deductions</th><td>₱<?= number_format((float)($clearance['total_deductions'] ?? 0), 2) ?></td></tr>
                    <tr><th>Net Pay</th><td>₱<?= number_format((float)($clearance['net_pay'] ?? ($clearance['net_payable'] ?? 0)), 2) ?></td></tr>
                    <tr><th>Gratuity</th><td>₱<?= number_format((float)($clearance['gratuity'] ?? 0), 2) ?></td></tr>
                    <tr><th>Notice Pay</th><td>₱<?= number_format((float)($clearance['notice_pay'] ?? 0), 2) ?></td></tr>
                    <tr><th>Outstanding Loans</th><td>₱<?= number_format((float)($clearance['outstanding_loans'] ?? 0), 2) ?></td></tr>
                    <tr><th>Other Deductions</th><td>₱<?= number_format((float)($clearance['other_deductions'] ?? 0), 2) ?></td></tr>
                </table>
            </div>

            <div class="form-section">
                <h5>Payroll Clearance Notes</h5>
                <p><?= nl2br(htmlspecialchars($clearance['comments'] ?? 'No additional comments.')) ?></p>
            </div>

            <div class="row signature-line">
                <div class="col-md-6 text-center">
                    <span>Payroll Administrator</span>
                </div>
                <div class="col-md-6 text-center">
                    <span>HR Representative</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
