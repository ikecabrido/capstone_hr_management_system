<?php
// Test script for emergency database connection and table structure
require_once "../auth/database.php";
require_once "core/BaseModel.php";
require_once "models/EmergencyCase.php";

echo "<h2>Emergency Database Test</h2>";

// Test database connection
$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
}

// Test table existence
try {
    $result = $db->query("SHOW TABLES LIKE 'cm_emergency_cases'");
    $table_exists = $result->rowCount() > 0;
    
    if ($table_exists) {
        echo "<p style='color: green;'>✅ cm_emergency_cases table exists</p>";
        
        // Show table structure
        $result = $db->query("DESCRIBE cm_emergency_cases");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
        
        // Test insert
        $emergency = new EmergencyCase($db);
        $test_data = [
            'case_id' => 'TEST001',
            'patient_id' => 'P001',
            'incident_date' => date('Y-m-d H:i:s'),
            'incident_type' => 'Medical Emergency',
            'severity_level' => 'Medium',
            'chief_complaint' => 'Test complaint',
            'initial_assessment' => 'Test assessment',
            'treatment_provided' => 'Test treatment',
            'attending_staff' => 'Test Staff',
            'ambulance_called' => 0,
            'contact_person' => 'Test Contact',
            'contact_phone' => '1234567890',
            'notes' => 'Test notes',
            'created_by' => 'Test User'
        ];
        
        echo "<h3>Test Insert Data:</h3>";
        echo "<pre>" . print_r($test_data, true) . "</pre>";
        
        $result = $emergency->create($test_data);
        if ($result) {
            echo "<p style='color: green;'>✅ Test insert successful</p>";
            
            // Clean up test record
            $db->query("DELETE FROM cm_emergency_cases WHERE case_id = 'TEST001'");
            echo "<p style='color: blue;'>🧹 Test record cleaned up</p>";
        } else {
            echo "<p style='color: red;'>❌ Test insert failed</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ cm_emergency_cases table does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>PHP Error Log:</h3>";
echo "<pre>" . file_get_contents(ini_get('error_log')) . "</pre>";
?>
