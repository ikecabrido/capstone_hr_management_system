<?php
// Script to create cm_clinic_reports table for clinic reports
echo "<h2>🗄️ Clinic Reports Database Setup</h2>";

require_once "../auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

echo "<p style='color: green;'>✅ Database connection successful</p>";

// Create cm_clinic_reports table
$create_table_sql = "
CREATE TABLE IF NOT EXISTS cm_clinic_reports (
    report_id VARCHAR(50) PRIMARY KEY,
    report_type VARCHAR(50) NOT NULL,
    report_date DATE NOT NULL,
    start_date DATE,
    end_date DATE,
    report_data LONGTEXT,
    generated_by VARCHAR(100) NOT NULL,
    status ENUM('Generated', 'Viewed', 'Exported', 'Archived') DEFAULT 'Generated',
    file_format ENUM('HTML', 'PDF', 'Excel', 'CSV') DEFAULT 'HTML',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_report_date (report_date),
    INDEX idx_report_type (report_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $db->exec($create_table_sql);
    echo "<p style='color: green;'>✅ cm_clinic_reports table created successfully!</p>";
    
    // Verify table exists
    $result = $db->query("SHOW TABLES LIKE 'cm_clinic_reports'");
    if ($result->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Table verification successful!</p>";
        
        // Show table structure
        echo "<h3>📋 Table Structure:</h3>";
        $result = $db->query("DESCRIBE cm_clinic_reports");
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test report insertion
        echo "<h3>🧪 Test Report Insertion:</h3>";
        $test_report = [
            'report_id' => 'TEST001',
            'report_type' => 'Daily',
            'report_date' => date('Y-m-d'),
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'report_data' => json_encode(['test' => 'data']),
            'generated_by' => 'Setup Script',
            'status' => 'Generated',
            'file_format' => 'HTML'
        ];
        
        $sql = "INSERT INTO cm_clinic_reports 
                (report_id, report_type, report_date, start_date, end_date, report_data, generated_by, status, file_format)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $test_report['report_id'],
            $test_report['report_type'],
            $test_report['report_date'],
            $test_report['start_date'],
            $test_report['end_date'],
            $test_report['report_data'],
            $test_report['generated_by'],
            $test_report['status'],
            $test_report['file_format']
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Test report inserted successfully!</p>";
            
            // Clean up test record
            $db->query("DELETE FROM cm_clinic_reports WHERE report_id = 'TEST001'");
            echo "<p style='color: blue;'>🧹 Test record cleaned up</p>";
            
            echo "<p style='color: green; font-size: 18px; font-weight: bold;'>🎉 Clinic Reports database is ready!</p>";
        } else {
            echo "<p style='color: red;'>❌ Test report insertion failed</p>";
            $error_info = $db->errorInfo();
            echo "<p><strong>Error:</strong> " . print_r($error_info, true) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Table verification failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error creating table: " . $e->getMessage() . "</p>";
    echo "<h4>Exception Details:</h4>";
    echo "<pre style='background: #ffebee; padding: 10px; border-radius: 5px;'>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>🔗 Next Steps:</h3>";
echo "<ul>";
echo "<li><strong>Access Clinic Reports:</strong> <a href='Clinic_Reports.php' style='color: #007bff;'>Clinic_Reports.php</a></li>";
echo "<li><strong>Generate Reports:</strong> Daily, Weekly, Monthly, and Custom reports</li>";
echo "<li><strong>Export Options:</strong> HTML, PDF, Excel, CSV formats</li>";
echo "<li><strong>Statistics:</strong> Comprehensive clinic statistics</li>";
echo "</ul>";

echo "<p><a href='Clinic_Reports.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Go to Clinic Reports</a></p>";
?>
