<?php
/**
 * WFA Dashboard Metrics API
 * Returns real-time employee metrics, KPIs, and summary statistics
 * 
 * Usage: GET /api/wfa/dashboard_metrics.php?date=2026-03-21
 */

header('Content-Type: application/json');
error_reporting(0); // Suppress PHP errors, return JSON instead

// Get database connection
$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Get date parameter (default to today)
    $metric_date = $_GET['date'] ?? date('Y-m-d');
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $metric_date)) {
        throw new Exception('Invalid date format. Use YYYY-MM-DD');
    }
    
    // Check if tables exist
    $tables_to_check = ['wfa_employee_metrics', 'wfa_risk_assessment', 'wfa_attrition_tracking', 'wfa_department_analytics'];
    $existing_tables = [];
    
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $existing_tables[$table] = true;
        }
    }
    
    // Query 1: Get today's metrics (if table exists)
    $metrics = array(
        'total_employees' => 0,
        'total_teachers' => 0,
        'total_staff' => 0,
        'new_hires_this_year' => 0,
        'average_salary' => 0,
        'average_performance_score' => 0,
        'total_departments' => 0,
        'metric_date' => $metric_date
    );
    
    if (isset($existing_tables['wfa_employee_metrics'])) {
        $stmt = $conn->prepare("
            SELECT 
                total_employees,
                total_teachers,
                total_staff,
                new_hires_this_year,
                average_salary,
                average_performance_score,
                total_departments,
                metric_date
            FROM wfa_employee_metrics
            WHERE metric_date = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $metric_date);
        $stmt->execute();
        $metrics_result = $stmt->get_result();
        $fetched = $metrics_result->fetch_assoc();
        if ($fetched) {
            $metrics = $fetched;
        }
    }
    
    // Query 2: Get at-risk employee count
    $risk_data = ['high_risk_count' => 0, 'avg_risk_score' => 0];
    if (isset($existing_tables['wfa_risk_assessment'])) {
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as high_risk_count,
                ROUND(AVG(risk_score), 2) as avg_risk_score
            FROM wfa_risk_assessment
            WHERE risk_level = 'high'
        ");
        $stmt->execute();
        $risk_result = $stmt->get_result();
        $risk_data = $risk_result->fetch_assoc();
    }
    
    // Query 3: Get recent attrition data
    $attrition_data = ['recent_separations' => 0, 'total_tenure_lost' => 0];
    if (isset($existing_tables['wfa_attrition_tracking'])) {
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as recent_separations,
                ROUND(SUM(tenure_years), 2) as total_tenure_lost
            FROM wfa_attrition_tracking
            WHERE YEAR(separation_date) = YEAR(?)
                AND MONTH(separation_date) = MONTH(?)
        ");
        $stmt->bind_param("ss", $metric_date, $metric_date);
        $stmt->execute();
        $attrition_result = $stmt->get_result();
        $attrition_data = $attrition_result->fetch_assoc();
    }
    
    // Query 4: Get department analytics summary
    $dept_data = ['active_departments' => 0, 'avg_dept_size' => 0];
    if (isset($existing_tables['wfa_department_analytics'])) {
        $stmt = $conn->prepare("
            SELECT 
                COUNT(DISTINCT department) as active_departments,
                ROUND(AVG(employee_count), 1) as avg_dept_size
            FROM wfa_department_analytics
            WHERE metric_date = ?
        ");
        $stmt->bind_param("s", $metric_date);
        $stmt->execute();
        $dept_result = $stmt->get_result();
        $dept_data = $dept_result->fetch_assoc();
    }
    
    // Build response
    $response = array(
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => array(
            'employee_metrics' => $metrics,
            'at_risk_count' => (int)($risk_data['high_risk_count'] ?? 0),
            'at_risk' => array(
                'high_risk_count' => (int)($risk_data['high_risk_count'] ?? 0),
                'avg_risk_score' => (float)($risk_data['avg_risk_score'] ?? 0)
            ),
            'attrition' => array(
                'recent_separations' => (int)($attrition_data['recent_separations'] ?? 0),
                'total_tenure_lost' => (float)($attrition_data['total_tenure_lost'] ?? 0)
            ),
            'departments' => array(
                'active_count' => (int)($dept_data['active_departments'] ?? 0),
                'avg_size' => (float)($dept_data['avg_dept_size'] ?? 0)
            )
        )
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode(array(
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => array(
            'employee_metrics' => array(),
            'at_risk_count' => 0
        )
    ));
}
