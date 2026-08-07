<?php
// Test script to verify payrollEmployeeConfigModel now fetches from rao_offer_salary

try {
    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check what's in rao_offer_salary
    echo "=== Checking rao_offer_salary for accepted offers ===\n";
    $stmt = $pdo->query("
        SELECT ros.application_id, rha.employee_id, ros.salary, ros.offer_status
        FROM rao_offer_salary ros
        JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id
        WHERE ros.offer_status = 'accepted'
        LIMIT 5
    ");
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($offers) > 0) {
        echo "Found " . count($offers) . " accepted offers:\n";
        foreach ($offers as $offer) {
            echo "  Employee ID: {$offer['employee_id']}, Salary: {$offer['salary']}, Status: {$offer['offer_status']}\n";
        }
    } else {
        echo "⚠️ No accepted offers found in rao_offer_salary\n";
    }

    // Include the model
    echo "\n=== Testing getAllEmployeesWithConfig() ===\n";
    require_once __DIR__ . '/payroll/models/payrollEmployeeConfigModel.php';

    $configModel = new PayrollEmployeeConfigModel($pdo);
    $employees = $configModel->getAllEmployeesWithConfig();
    echo "Total active employees: " . count($employees) . "\n";
    
    if (count($employees) > 0) {
        echo "\nFirst employee:\n";
        $emp = $employees[0];
        echo "  ID: {$emp['employee_id']}\n";
        echo "  Name: {$emp['full_name']}\n";
        echo "  Position: {$emp['position']}\n";
        echo "  Base Salary (from rao_offer_salary): {$emp['base_salary']}\n";
        echo "  Teacher Qualification: {$emp['teacher_qualification']}\n";
    }

    echo "\n✅ Test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
