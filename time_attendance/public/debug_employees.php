<?php
session_start();
require_once(__DIR__ . '/../app/config/Database.php');

$database = new Database();
$db = $database->getConnection();

echo "<h2>Employee Debug Info</h2>";

// Check employees table
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM employees");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Total Employees:</strong> " . $result['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error checking employees table:</strong> " . $e->getMessage() . "</p>";
}

// List employees
echo "<h3>Employees List:</h3>";
try {
    $stmt = $db->query("SELECT employee_id, full_name FROM employees ORDER BY full_name LIMIT 20");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($employees)) {
        echo "<p style='color: orange;'>No employees found in database</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Employee ID</th><th>Full Name</th></tr>";
        foreach ($employees as $emp) {
            echo "<tr><td>" . htmlspecialchars($emp['employee_id']) . "</td><td>" . htmlspecialchars($emp['full_name']) . "</td></tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error fetching employees:</strong> " . $e->getMessage() . "</p>";
}

// Check flexible_schedules table structure
echo "<h3>Flexible Schedules Table Structure:</h3>";
try {
    $stmt = $db->query("DESCRIBE flexible_schedules");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>Error describing table:</strong> " . $e->getMessage() . "</p>";
}
?>
