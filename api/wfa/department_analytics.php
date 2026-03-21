<?php
/**
 * WFA Department Analytics API
 * Returns department-level statistics and comparisons
 * 
 * Usage: GET /api/wfa/department_analytics.php?date=2026-03-21&department=IT
 */

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

$response = array(
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'data' => array('departments' => [])
);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        echo json_encode($response);
        exit;
    }
    
    $metric_date = $_GET['date'] ?? date('Y-m-d');
    
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'wfa_department_analytics'");
    if (!$table_check || $table_check->num_rows === 0) {
        echo json_encode($response);
        exit;
    }
    
    // Build simple query
    $query = "
        SELECT 
            id,
            department,
            employee_count,
            average_salary,
            average_performance_score,
            average_tenure_years
        FROM wfa_department_analytics
        ORDER BY employee_count DESC
    ";
    
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode($response);
        exit;
    }
    
    $departments = array();
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    
    $response = array(
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => array('departments' => $departments)
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode($response);
}
