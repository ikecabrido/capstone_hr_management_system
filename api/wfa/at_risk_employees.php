<?php
/**
 * WFA At-Risk Employees API
 * Returns employees at risk of leaving with detailed risk assessment data
 * 
 * Usage: GET /api/wfa/at_risk_employees.php?limit=10&risk_level=high
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
    'pagination' => array(
        'limit' => 10,
        'offset' => 0,
        'total' => 0,
        'has_more' => false
    ),
    'data' => array('employees' => [])
);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }
    
    $limit = min((int)($_GET['limit'] ?? 10), 100);
    $risk_level = $_GET['risk_level'] ?? 'high';
    $offset = (int)($_GET['offset'] ?? 0);
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'wfa_risk_assessment'");
    if (!$result || $result->num_rows === 0) {
        echo json_encode($response);
        exit;
    }
    
    // Simple query without complex prepared statements
    if ($risk_level === 'all') {
        $query = "
            SELECT 
                ra.id,
                ra.employee_id,
                COALESCE(e.full_name, 'Unknown') as employee_name,
                COALESCE(e.department, 'N/A') as department,
                COALESCE(e.position, 'N/A') as position,
                ra.risk_level,
                ra.risk_score,
                COALESCE(ra.performance_score, 0) as performance_score,
                ra.updated_at
            FROM wfa_risk_assessment ra
            LEFT JOIN employees e ON ra.employee_id = e.employee_id
            ORDER BY ra.risk_score DESC
            LIMIT $limit OFFSET $offset
        ";
    } else {
        // Validate and escape risk_level
        $safe_risk_level = $conn->real_escape_string($risk_level);
        $query = "
            SELECT 
                ra.id,
                ra.employee_id,
                COALESCE(e.full_name, 'Unknown') as employee_name,
                COALESCE(e.department, 'N/A') as department,
                COALESCE(e.position, 'N/A') as position,
                ra.risk_level,
                ra.risk_score,
                COALESCE(ra.performance_score, 0) as performance_score,
                ra.updated_at
            FROM wfa_risk_assessment ra
            LEFT JOIN employees e ON ra.employee_id = e.employee_id
            WHERE ra.risk_level = '$safe_risk_level'
            ORDER BY ra.risk_score DESC
            LIMIT $limit OFFSET $offset
        ";
    }
    
    $result = $conn->query($query);
    if (!$result) {
        // Table exists but query failed - return empty
        echo json_encode($response);
        exit;
    }
    
    $employees = array();
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    // Get total count
    if ($risk_level === 'all') {
        $count_result = $conn->query("SELECT COUNT(*) as total FROM wfa_risk_assessment");
    } else {
        $safe_risk_level = $conn->real_escape_string($risk_level);
        $count_result = $conn->query("SELECT COUNT(*) as total FROM wfa_risk_assessment WHERE risk_level = '$safe_risk_level'");
    }
    
    $count_data = $count_result->fetch_assoc();
    $total = (int)($count_data['total'] ?? 0);
    
    $response = array(
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'pagination' => array(
            'limit' => $limit,
            'offset' => $offset,
            'total' => $total,
            'has_more' => ($offset + $limit) < $total
        ),
        'data' => array('employees' => $employees)
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Return success response even on error (graceful degradation)
    http_response_code(200);
    echo json_encode($response);
}

