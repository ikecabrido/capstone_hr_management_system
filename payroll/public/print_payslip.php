<?php
require_once __DIR__ . '/../controllers/SalaryController.php';

$controller = new SalaryController();

// Get ID from query string
if (!isset($_GET['id'])) {
    die("Payslip ID missing.");
}

$payslipId = (int) $_GET['id'];
$payslip = $controller->viewPayslip($payslipId);

if (!$payslip) {
    die("Payslip not found.");
}

// Now $payslip is defined and ready to use
$employeeName = htmlspecialchars($payslip['first_name'] . ' ' . $payslip['last_name']);
$position = htmlspecialchars($payslip['position']);
$generatedAt = date("M d, Y", strtotime($payslip['generated_at']));
$grossPay = number_format($payslip['gross_pay'], 2);
$netPay = number_format($payslip['net_pay'], 2);
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payslip - <?= $employeeName ?></title>
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <style>
        body {
            font-family: "Source Sans Pro", sans-serif;
        }

        .payslip {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .btn-print {
            margin-top: 20px;
        }
    </style>
</head>

<body onload="window.print();">
    <div class="payslip">
        <h2 class="text-center">Payslip</h2>
        <p>
            <strong>Employee:</strong> <?= $employeeName ?><br>
            <strong>Position:</strong> <?= $position ?><br>
            <strong>Date:</strong> <?= $generatedAt ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Gross Pay</strong></td>
                    <td class="text-right">₱<?= $grossPay ?></td>
                </tr>

                <?php if (!empty($payslip['earnings'])): ?>
                    <tr>
                        <td colspan="2"><strong>Allowances / Earnings</strong></td>
                    </tr>
                    <?php foreach ($payslip['earnings'] as $earning): ?>
                        <tr>
                            <td><?= htmlspecialchars($earning['description']) ?></td>
                            <td class="text-right">₱<?= number_format($earning['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($payslip['deductions'])): ?>
                    <tr>
                        <td colspan="2"><strong>Deductions</strong></td>
                    </tr>
                    <?php foreach ($payslip['deductions'] as $deduction): ?>
                        <tr>
                            <td><?= htmlspecialchars($deduction['description']) ?></td>
                            <td class="text-right">₱<?= number_format($deduction['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <tr>
                    <td><strong>Net Pay</strong></td>
                    <td class="text-right"><strong>₱<?= $netPay ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>