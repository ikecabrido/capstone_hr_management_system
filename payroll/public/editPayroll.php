<?php
require_once "../database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Get DB connection
    $db = Database::getInstance()->getConnection();

    // 2. Retrieve POST data
    $employeeId = $_POST['employee_id'] ?? null;
    $periodId   = $_POST['period_id'] ?? null;
    $grossPay   = $_POST['gross_pay'] ?? null;
    $deductions = $_POST['total_deductions'] ?? null;

    if (!$employeeId || !$periodId || $grossPay === null || $deductions === null) {
        die("Invalid input.");
    }

    // 3. Calculate net pay
    $netPay = $grossPay - $deductions;

    // 4. Update payslip for this employee & period
    $sql = "
        UPDATE payslips p
        JOIN payroll_runs r ON p.payroll_run_id = r.id
        SET p.gross_pay = :gross, p.total_deductions = :deductions, p.net_pay = :net
        WHERE p.employee_id = :employee_id
        AND r.payroll_period_id = :period_id
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':gross'       => $grossPay,
        ':deductions'  => $deductions,
        ':net'         => $netPay,
        ':employee_id' => $employeeId,
        ':period_id'   => $periodId
    ]);

    // 5. Redirect back to payroll page or show success
    header("Location: ../views/payrollProcess.php?period_id={$periodId}&updated=1");
    exit;
} else {
    die("Invalid request method.");
}
