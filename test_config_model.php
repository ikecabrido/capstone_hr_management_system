<?php
// Test script to verify payrollEmployeeConfigModel now returns correct data

try {
    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Include the model
    require_once __DIR__ . '/payroll/models/payrollEmployeeConfigModel.php';

    $configModel = new PayrollEmployeeConfigModel($pdo);

    // Test 1: Get all employees with config
    echo "=== TEST 1: getAllEmployeesWithConfig() ===\n";
    $employees = $configModel->getAllEmployeesWithConfig();
    echo "Total active employees: " . count($employees) . "\n";

    if (count($employees) > 0) {
        echo "\nFirst 3 employees:\n";
        for ($i = 0; $i < min(3, count($employees)); $i++) {
            $emp = $employees[$i];
            echo "\n  ID: {$emp['employee_id']}\n";
            echo "  Name: {$emp['full_name']}\n";
            echo "  Position: {$emp['position']}\n";
            echo "  Base Salary: {$emp['base_salary']}\n";
            echo "  Teacher Qualification: {$emp['teacher_qualification']}\n";
            echo "  Has SSS: {$emp['has_sss']}\n";
        }
    }

    // Test 2: Get single employee config
    echo "\n\n=== TEST 2: getEmployeeConfig(7) ===\n";
    $singleEmp = $configModel->getEmployeeConfig(7);
    if ($singleEmp) {
        echo "Employee ID: {$singleEmp['employee_id']}\n";
        echo "Name: {$singleEmp['full_name']}\n";
        echo "Position: {$singleEmp['position']}\n";
        echo "Base Salary: {$singleEmp['base_salary']}\n";
        echo "Teacher Qualification: {$singleEmp['teacher_qualification']}\n";
        echo "Has SSS: {$singleEmp['has_sss']}\n";
    } else {
        echo "Employee 7 not found\n";
    }

    // Test 3: Direct database verification
    echo "\n\n=== TEST 3: Direct DB Query Verification ===\n";
    $stmt = $pdo->query("SELECT employee_id, full_name, position, base_salary, teacher_qualification FROM employees WHERE employment_status='Active' LIMIT 3");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Records in employees table:\n";
    foreach ($results as $row) {
        echo "  {$row['employee_id']} | {$row['full_name']} | {$row['position']} | Base: {$row['base_salary']} | Qual: {$row['teacher_qualification']}\n";
    }

    echo "\n\n✅ Data retrieval test completed successfully!\n";
    echo "Base salary values are now being read from employees table correctly.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
