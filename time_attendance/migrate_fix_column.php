<?php
/**
 * Direct Database Migration - Fix flexible_schedules employee_id column
 */
session_start();
require_once(__DIR__ . '/app/config/Database.php');

$database = new Database();
$db = $database->getConnection();

echo "<h1>Database Migration - Fix Flexible Schedules</h1>";

echo "<h2>Step 1: Check Current Column Type</h2>";
try {
    $result = $db->query("DESCRIBE flexible_schedules employee_id")->fetch(PDO::FETCH_ASSOC);
    echo "<p>Current column type: <strong>" . htmlspecialchars($result['Type']) . "</strong></p>";
    if (strpos($result['Type'], 'INT') !== false) {
        echo "<p style='color: red;'>❌ Column is INT - needs to be changed to VARCHAR(50)</p>";
    } else {
        echo "<p style='color: green;'>✓ Column is already " . htmlspecialchars($result['Type']) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Step 2: Fix Column Type</h2>";
try {
    echo "<p>Executing: ALTER TABLE flexible_schedules MODIFY employee_id VARCHAR(50) NOT NULL;</p>";
    $db->exec("ALTER TABLE flexible_schedules MODIFY employee_id VARCHAR(50) NOT NULL");
    echo "<p style='color: green; font-weight: bold;'>✓ SUCCESS: Column changed to VARCHAR(50)</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Step 3: Verify Column Type</h2>";
try {
    $result = $db->query("DESCRIBE flexible_schedules employee_id")->fetch(PDO::FETCH_ASSOC);
    echo "<p>New column type: <strong>" . htmlspecialchars($result['Type']) . "</strong></p>";
    if (strpos($result['Type'], 'VARCHAR') !== false) {
        echo "<p style='color: green; font-weight: bold;'>✓ VERIFIED: Column is now VARCHAR(50)</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Step 4: Delete Corrupted Records</h2>";
try {
    $before = $db->query("SELECT COUNT(*) as count FROM flexible_schedules WHERE employee_id = '0' OR employee_id = 0")->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p>Corrupted records found: <strong>$before</strong></p>";
    
    if ($before > 0) {
        $db->exec("DELETE FROM flexible_schedules WHERE employee_id = '0' OR employee_id = 0");
        echo "<p style='color: green; font-weight: bold;'>✓ Deleted $before corrupted records</p>";
    } else {
        echo "<p>No corrupted records to delete</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Step 5: Summary</h2>";
try {
    $total = $db->query("SELECT COUNT(*) as count FROM flexible_schedules")->fetch(PDO::FETCH_ASSOC)['count'];
    $employees = $db->query("SELECT DISTINCT employee_id FROM flexible_schedules ORDER BY employee_id")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total flexible schedules: <strong>$total</strong></p>";
    if (!empty($employees)) {
        $emp_list = implode(", ", array_map(fn($e) => htmlspecialchars($e['employee_id']), $employees));
        echo "<p>Employees with schedules: <strong>$emp_list</strong></p>";
    } else {
        echo "<p>No flexible schedules in database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p style='background: #e6f3ff; padding: 15px; border-radius: 5px;'>";
echo "<strong>✓ Migration Complete!</strong><br>";
echo "Now you can:<br>";
echo "1. Go to <a href='public/shifts.php'>Shifts Management</a><br>";
echo "2. Create a new flexible schedule<br>";
echo "3. The employee_id should now be saved correctly!<br>";
echo "</p>";
?>
