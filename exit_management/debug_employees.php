<?php
session_start();
require_once "../auth/auth_check.php";
require_once "controllers/ExitManagementController.php";

header('Content-Type: application/json');

try {
    $controller = new ExitManagementController();
    
    // Test 1: Get eligible employees
    echo "<!-- Test 1: Get eligible employees -->\n";
    $employees = $controller->getEligibleEmployees();
    
    echo json_encode([
        'total_employees' => count($employees),
        'employees' => $employees,
        'test' => 'success'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>
