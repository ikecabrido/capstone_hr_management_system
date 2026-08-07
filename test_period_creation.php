<?php
require_once 'auth/database.php';
require_once 'payroll/models/payrollPeriodModel.php';

$db = Database::getInstance()->getConnection();

try {
    $periodModel = new PayrollPeriodModel($db);

    // Test creating a period
    $result = $periodModel->create([
        'period_name' => 'Test Period Jan 1-15, 2024',
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-15',
        'pay_date' => '2024-01-20',
        'status' => 'open'
    ]);

    echo 'Period creation result: ' . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;

    // Check the created period
    $stmt = $db->query('SELECT period_id, period_name FROM pr_periods ORDER BY period_id DESC LIMIT 1');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Created period ID: ' . $row['period_id'] . ' - Name: ' . $row['period_name'] . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
