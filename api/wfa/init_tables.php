<?php
/**
 * Initialize Report Snapshots Tables
 * Run this once to set up the database tables
 */

$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create wfa_report_snapshots table
    $sql1 = "CREATE TABLE IF NOT EXISTS wfa_report_snapshots (
        snapshot_id INT PRIMARY KEY AUTO_INCREMENT,
        snapshot_type VARCHAR(50) NOT NULL,
        snapshot_name VARCHAR(255) NOT NULL,
        snapshot_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_employees INT,
        active_employees INT,
        inactive_employees INT,
        attrition_rate DECIMAL(5,2),
        average_tenure DECIMAL(5,1),
        department_count INT,
        position_count INT,
        snapshot_data LONGTEXT,
        created_by VARCHAR(100),
        notes TEXT,
        INDEX idx_snapshot_type (snapshot_type),
        INDEX idx_snapshot_date (snapshot_date)
    )";
    
    if ($conn->query($sql1) === FALSE) {
        throw new Exception("Error creating wfa_report_snapshots: " . $conn->error);
    }
    
    // Create wfa_report_history table
    $sql2 = "CREATE TABLE IF NOT EXISTS wfa_report_history (
        report_id INT PRIMARY KEY AUTO_INCREMENT,
        report_type VARCHAR(50) NOT NULL,
        report_title VARCHAR(255),
        report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        file_path VARCHAR(500),
        file_format VARCHAR(20),
        file_size INT,
        created_by VARCHAR(100),
        is_favorite BOOLEAN DEFAULT FALSE,
        notes TEXT,
        INDEX idx_report_type (report_type),
        INDEX idx_report_date (report_date)
    )";
    
    if ($conn->query($sql2) === FALSE) {
        throw new Exception("Error creating wfa_report_history: " . $conn->error);
    }
    
    // Create wfa_insights_history table
    $sql3 = "CREATE TABLE IF NOT EXISTS wfa_insights_history (
        insight_id INT PRIMARY KEY AUTO_INCREMENT,
        insight_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_kpis INT,
        critical_insights INT,
        warning_insights INT,
        info_insights INT,
        insights_data LONGTEXT,
        action_taken VARCHAR(500),
        resolved_by VARCHAR(100),
        resolution_date TIMESTAMP NULL,
        INDEX idx_insight_date (insight_date)
    )";
    
    if ($conn->query($sql3) === FALSE) {
        throw new Exception("Error creating wfa_insights_history: " . $conn->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'All tables created successfully!',
        'tables' => [
            'wfa_report_snapshots' => 'Created',
            'wfa_report_history' => 'Created',
            'wfa_insights_history' => 'Created'
        ]
    ]);
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
