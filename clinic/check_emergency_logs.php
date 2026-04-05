<?php
// Script to check emergency case error logs
echo "<h2>🔍 Emergency Case Error Log Analysis</h2>";

// Get PHP error log path
$error_log_path = ini_get('error_log');
echo "<h3>📋 Error Log Location:</h3>";
echo "<p><strong>Path:</strong> " . $error_log_path . "</p>";

if (file_exists($error_log_path)) {
    echo "<h3>📝 Recent Error Log Entries (Last 50):</h3>";
    $logs = file_get_contents($error_log_path);
    $lines = explode("\n", $logs);
    $recent_lines = array_slice($lines, -50);
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; max-height: 400px; overflow-y: auto;'>";
    
    $emergency_errors = [];
    $other_errors = [];
    
    foreach ($recent_lines as $line_num => $log_line) {
        $line = trim($log_line);
        if (empty($line)) continue;
        
        // Highlight emergency-related errors
        if (stripos($line, 'emergency') !== false || 
            stripos($line, 'Emergency') !== false || 
            stripos($line, 'cm_emergency_cases') !== false ||
            stripos($line, 'INSERT INTO') !== false) {
            echo "<p style='color: #d32f2f; font-weight: bold; margin-bottom: 5px;'>";
            echo "<strong>Line " . (count($lines) - 50 + $line_num + 1) . ":</strong> " . htmlspecialchars($line);
            echo "</p>";
            $emergency_errors[] = $line;
        } else {
            echo "<p style='color: #666; font-size: 12px; margin-bottom: 3px;'>";
            echo htmlspecialchars($line);
            echo "</p>";
            $other_errors[] = $line;
        }
    }
    echo "</div>";
    
    // Summary
    echo "<h3>📊 Error Summary:</h3>";
    echo "<div style='display: flex; gap: 20px;'>";
    echo "<div style='flex: 1; background: #fff3cd; padding: 15px; border-radius: 8px;'>";
    echo "<h4 style='color: #8b4513;'>🚨 Emergency Errors (" . count($emergency_errors) . ")</h4>";
    echo "<p>Issues related to emergency case recording</p>";
    echo "</div>";
    echo "<div style='flex: 1; background: #d1ecf1; padding: 15px; border-radius: 8px;'>";
    echo "<h4 style='color: #0c5460;'>📋 Other Errors (" . count($other_errors) . ")</h4>";
    echo "<p>Other PHP/system errors</p>";
    echo "</div>";
    echo "</div>";
    
} else {
    echo "<p style='color: #dc3545;'>❌ Error log file not found at: " . $error_log_path . "</p>";
}

// Test database connection and table
echo "<h3>🗄️ Database Test:</h3>";
try {
    require_once "../auth/database.php";
    require_once "core/BaseModel.php";
    require_once "models/EmergencyCase.php";
    
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    if ($db === null) {
        echo "<p style='color: #dc3545;'>❌ Database connection failed</p>";
    } else {
        echo "<p style='color: #28a745;'>✅ Database connection successful</p>";
        
        // Test emergency case table
        $result = $db->query("SHOW TABLES LIKE 'cm_emergency_cases'");
        $table_exists = $result->rowCount() > 0;
        
        if ($table_exists) {
            echo "<p style='color: #28a745;'>✅ cm_emergency_cases table exists</p>";
            
            // Check table structure
            $result = $db->query("DESCRIBE cm_emergency_cases");
            echo "<h4>Table Structure:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 12px;'>";
            echo "<tr style='background: #f8f9fa;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $field_name = $row['Field'];
                $row_style = in_array($field_name, ['case_id', 'patient_id', 'incident_date', 'chief_complaint']) ? "background: #ffebee;" : "";
                echo "<tr style='$row_style'>";
                echo "<td>{$field_name}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Test emergency case creation
            echo "<h4>🧪 Test Emergency Case Creation:</h4>";
            $emergency = new EmergencyCase($db);
            
            $test_data = [
                'case_id' => 'TEST' . time(),
                'patient_id' => 'TEST001',
                'incident_date' => date('Y-m-d H:i:s'),
                'incident_type' => 'Medical Emergency',
                'severity_level' => 'Medium',
                'chief_complaint' => 'Test from error check script',
                'initial_assessment' => 'Test assessment',
                'treatment_provided' => 'Test treatment',
                'attending_staff' => 'Test Staff',
                'ambulance_called' => 0,
                'contact_person' => 'Test Contact',
                'contact_phone' => '1234567890',
                'notes' => 'Test notes from error check',
                'created_by' => 'Error Check Script'
            ];
            
            echo "<h5>Test Data:</h5>";
            echo "<pre style='background: #f1f3f4; padding: 10px; border-radius: 5px;'>" . print_r($test_data, true) . "</pre>";
            
            $result = $emergency->create($test_data);
            
            if ($result) {
                echo "<p style='color: #28a745;'>✅ Test emergency case created successfully!</p>";
                // Clean up
                $db->query("DELETE FROM cm_emergency_cases WHERE case_id = '" . $test_data['case_id'] . "'");
                echo "<p style='color: #17a2b8;'>🧹 Test record cleaned up</p>";
                echo "<p style='color: #28a745; font-weight: bold;'>🎉 Emergency case recording should work!</p>";
            } else {
                echo "<p style='color: #dc3545;'>❌ Test emergency case creation failed</p>";
                
                // Get detailed error info
                $error_info = $db->errorInfo();
                echo "<h5>Database Error Info:</h5>";
                echo "<pre style='background: #ffebee; padding: 10px; border-radius: 5px;'>" . print_r($error_info, true) . "</pre>";
            }
            
        } else {
            echo "<p style='color: #dc3545;'>❌ cm_emergency_cases table does not exist</p>";
            echo "<p><strong>Solution:</strong> Run the database setup script to create the table.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: #dc3545;'>❌ Database test error: " . $e->getMessage() . "</p>";
    echo "<h4>Exception Details:</h4>";
    echo "<pre style='background: #ffebee; padding: 10px; border-radius: 5px;'>" . $e->getTraceAsString() . "</pre>";
}

echo "<h3>🔧 Quick Fixes:</h3>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px;'>";
echo "<h4>Common Issues & Solutions:</h4>";
echo "<ul>";
echo "<li><strong>Missing incident_date:</strong> Ensure form includes incident_date field</li>";
echo "<li><strong>Database permissions:</strong> Check MySQL user has INSERT privileges</li>";
echo "<li><strong>Table constraints:</strong> Verify all required fields are provided</li>";
echo "<li><strong>Session data:</strong> Ensure user session is active</li>";
echo "<li><strong>Form validation:</strong> Check all required fields are filled</li>";
echo "<li><strong>Primary key conflict:</strong> Verify case_id is unique</li>";
echo "</ul>";
echo "</div>";

echo "<p><a href='Emergency.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Emergency Module</a></p>";
?>
