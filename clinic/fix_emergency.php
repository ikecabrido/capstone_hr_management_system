<?php
require_once "../auth/database.php";

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    if ($db === null) {
        die("Database connection failed.");
    }
    
    echo "Starting database update...<br>";
    
    // Check if columns exist
    $stmt = $db->query("SHOW COLUMNS FROM cm_emergency_cases");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $queries = [];
    
    // Add parents_notified
    if (!in_array('parents_notified', $columns)) {
        $queries[] = "ALTER TABLE cm_emergency_cases ADD COLUMN parents_notified BOOLEAN DEFAULT FALSE AFTER ambulance_arrival_time";
    }
    
    // Add parent_notification_time
    if (!in_array('parent_notification_time', $columns)) {
        $queries[] = "ALTER TABLE cm_emergency_cases ADD COLUMN parent_notification_time DATETIME AFTER parents_notified";
    }
    
    // Add witness_names
    if (!in_array('witness_names', $columns)) {
        $queries[] = "ALTER TABLE cm_emergency_cases ADD COLUMN witness_names TEXT AFTER parent_notification_time";
    }
    
    // Update severity_level ENUM
    $queries[] = "ALTER TABLE cm_emergency_cases MODIFY COLUMN severity_level ENUM('Low', 'Medium', 'High', 'Critical', 'Minor')";
    
    // Update case_status ENUM
    $queries[] = "ALTER TABLE cm_emergency_cases MODIFY COLUMN case_status ENUM('Active', 'Resolved', 'Transferred', 'Closed', 'Open') DEFAULT 'Active'";
    
    foreach ($queries as $sql) {
        try {
            $db->exec($sql);
            echo "Executed: $sql <br>";
        } catch (PDOException $e) {
            echo "Error executing $sql: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "Database update completed successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
