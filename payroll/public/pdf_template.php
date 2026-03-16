<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .sub {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background: #f0f0f0;
        }

        .summary {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <h2>Company Payroll Report</h2>

    <div class="sub">
        Period: <?= htmlspecialchars($period['period_name']) ?><br>
        Generated: <?= date('F d, Y') ?>
    </div>

    <div class="summary">

        <strong>Total Employees:</strong> <?= count($payroll) ?><br>

        <strong>Total Gross:</strong>
        ₱<?= number_format(array_sum(array_column($payroll, 'gross_pay')), 2) ?><br>

        <strong>Total Deductions:</strong>
        ₱<?= number_format(array_sum(array_column($payroll, 'total_deductions')), 2) ?><br>

        <strong>Total Net:</strong>
        ₱<?= number_format(array_sum(array_column($payroll, 'net_pay')), 2) ?><br>

    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Position</th>
                <th>Type</th>
                <th>Gross</th>
                <th>Deductions</th>
                <th>Net</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($payroll as $i => $row): ?>

                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['employee_name']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['employment_type']) ?></td>
                    <td>₱<?= number_format($row['gross_pay'], 2) ?></td>
                    <td>₱<?= number_format($row['total_deductions'], 2) ?></td>
                    <td>₱<?= number_format($row['net_pay'], 2) ?></td>
                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>

</body>

</html>