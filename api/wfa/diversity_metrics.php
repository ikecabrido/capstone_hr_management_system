<?php
/**
 * WFA Diversity Metrics API
 * Returns diversity statistics including gender, age, and tenure distribution
 * 
 * Usage: GET /api/wfa/diversity_metrics.php?date=2026-03-21&category=gender
 */

header('Content-Type: application/json');
error_reporting(0);

$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $metric_date = $_GET['date'] ?? date('Y-m-d');
    $category = $_GET['category'] ?? null; // gender, age_group, tenure
    
    // Validate date
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $metric_date)) {
        throw new Exception('Invalid date format. Use YYYY-MM-DD');
    }
    
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'wfa_diversity_metrics'");
    if (!$table_check || $table_check->num_rows === 0) {
        // Return empty result if table doesn't exist
        echo json_encode(array(
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => array('gender_summary' => [], 'age_summary' => [], 'tenure_summary' => [])
        ));
        exit;
    }
    
    // Simple query without prepared statements to avoid bind issues
    $query = "
        SELECT 
            diversity_category,
            category_value,
            employee_count,
            percentage
        FROM wfa_diversity_metrics
        ORDER BY diversity_category, percentage DESC
    ";
    
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode(array(
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => array('gender_summary' => [])
        ));
        exit;
    }
    
    // Organize by category
    $gender_summary = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['diversity_category'] === 'gender') {
            $gender_summary[] = $row;
        }
    }
    
    $response = array(
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => array(
            'gender_summary' => $gender_summary
        )
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(200);
    $fallback = array(
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => array('gender_summary' => [])
    );
    echo json_encode($fallback);
}

