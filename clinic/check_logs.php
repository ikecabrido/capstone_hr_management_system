<?php
// Script to check PHP error logs and diagnose emergency case issues
echo "<h2>🔍 Emergency Case Error Diagnosis</h2>";

// Check PHP error log
$error_log_path = ini_get('error_log');
echo "<h3>📋 Error Log Location:</h3>";
echo "<p><strong>Path:</strong> " . $error_log_path . "</p>";

if (file_exists($error_log_path)) {
    echo "<h3>📝 Recent Error Log Entries:</h3>";
    $logs = file_get_contents($error_log_path);
    $recent_logs = array_slice(explode("\n", $logs), -20); // Last 20 lines
    
    echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    foreach ($recent_logs as $log_line) {
        if (trim($log_line)) {
            // Highlight emergency-related logs
            if (strpos($log_line, 'Emergency') !== false || strpos($log_line, 'emergency') !== false) {
                echo "<p style='color: red; font-weight: bold;'>" . htmlspecialchars($log_line) . "</p>";
            } else {
                echo "<p style='color: #333;'>" . htmlspecialchars($log_line) . "</p>";
            }
        }
    }
    echo "</div>";
} else {
    echo "<p style='color: orange;'>⚠️ Error log file not found</p>";
}

// Check database connection
echo "<h3>🗄️ Database Connection Test:</h3>";
try {
    require_once "../auth/database.php";
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    if ($db === null) {
        echo "<p style='color: red;'>❌ Database connection is null</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Test cm_emergency_cases table
        $result = $db->query("SHOW TABLES LIKE 'cm_emergency_cases'");
        $table_exists = $result->rowCount() > 0;
        
        if ($table_exists) {
            echo "<p style='color: green;'>✅ cm_emergency_cases table exists</p>";
            
            // Check table structure
            $result = $db->query("DESCRIBE cm_emergency_cases");
            echo "<h4>Table Structure:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            $required_fields = ['case_id', 'patient_id', 'incident_date', 'incident_type', 'severity_level', 'chief_complaint'];
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $field_name = $row['Field'];
                $row_style = in_array($field_name, $required_fields) ? "background: #e8f5e8;" : "";
                echo "<tr style='$row_style'>";
                echo "<td>{$field_name}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Test a simple insert
            echo "<h4>🧪 Test Insert:</h4>";
            try {
                $test_case_id = 'TEST' . time();
                $sql = "INSERT INTO cm_emergency_cases (case_id, patient_id, incident_date, incident_type, severity_level, chief_complaint, created_by) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([
                    $test_case_id,
                    'TEST001',
                    date('Y-m-d H:i:s'),
                    'Medical Emergency',
                    'Medium',
                    'Test complaint',
                    'Test User'
                ]);
                
                if ($result) {
                    echo "<p style='color: green;'>✅ Test insert successful</p>";
                    // Clean up
                    $db->query("DELETE FROM cm_emergency_cases WHERE case_id = '$test_case_id'");
                    echo "<p style='color: blue;'>🧹 Test record cleaned up</p>";
                } else {
                    echo "<p style='color: red;'>❌ Test insert failed</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Test insert error: " . $e->getMessage() . "</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ cm_emergency_cases table does not exist</p>";
            echo "<p><strong>Fix:</strong> Run the database setup script to create the table</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database test error: " . $e->getMessage() . "</p>";
}

// Check PHP settings
echo "<h3>⚙️ PHP Settings:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>error_reporting</td><td>" . error_reporting() . "</td></tr>";
echo "<tr><td>display_errors</td><td>" . (ini_get('display_errors') ? 'On' : 'Off') . "</td></tr>";
echo "<tr><td>log_errors</td><td>" . (ini_get('log_errors') ? 'On' : 'Off') . "</td></tr>";
echo "<tr><td>error_log</td><td>" . ini_get('error_log') . "</td></tr>";
echo "</table>";

echo "<h3>🔧 Quick Fixes:</h3>";
echo "<ul>";
echo "<li><strong>Missing incident_date:</strong> Ensure form includes incident_date field</li>";
echo "<li><strong>Database permissions:</strong> Check MySQL user has INSERT privileges</li>";
echo "<li><strong>Table constraints:</strong> Verify all required fields are provided</li>";
echo "<li><strong>Session data:</strong> Ensure user session is active</li>";
echo "</ul>";
?>
