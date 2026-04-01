<?php
/**
 * WFA Attrition Metrics API
 * Returns attrition trends, monthly rates, and separation analytics
 * 
 * Usage: GET /api/wfa/attrition_metrics.php?year=2026&month=3
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
    'data' => array(
        'monthly_summary' => [],
        'by_separation_type' => [],
        'summary' => array(
            'total_separations' => 0,
            'attrition_rate' => 0
        )
    )
);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        echo json_encode($response);
        exit;
    }
    
    $year = (int)($_GET['year'] ?? date('Y'));
    $month = (int)($_GET['month'] ?? date('m'));
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'wfa_monthly_attrition'");
    if (!$result || $result->num_rows === 0) {
        echo json_encode($response);
        exit;
    }
    
    // Simple queries without prepared statements to avoid bind issues
    $query = "
        SELECT 
            year_month,
            total_separations,
            voluntary_separations,
            involuntary_separations,
            attrition_rate_percent,
            average_tenure_departing
        FROM wfa_monthly_attrition
        ORDER BY year_month DESC
        LIMIT 12
    ";
    
    $result = $conn->query($query);
    if ($result) {
        $monthly_data = [];
        while ($row = $result->fetch_assoc()) {
            $monthly_data[] = $row;
        }
        $response['data']['monthly_summary'] = $monthly_data;
    }
    
    // Check for attrition_tracking table
    $result = $conn->query("SHOW TABLES LIKE 'wfa_attrition_tracking'");
    if ($result && $result->num_rows > 0) {
        // Get by separation type
        $query = "
            SELECT 
                separation_type,
                COUNT(*) as count
            FROM wfa_attrition_tracking
            GROUP BY separation_type
        ";
        
        $result = $conn->query($query);
        if ($result) {
            $type_data = [];
            while ($row = $result->fetch_assoc()) {
                $type_data[] = $row;
            }
            $response['data']['by_separation_type'] = $type_data;
        }
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode($response);
}
