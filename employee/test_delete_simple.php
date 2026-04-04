<?php
session_start();
require_once "../auth/database.php";

echo "<h2>Simple DELETE Test</h2>";

// Test 1: Check if we can connect to database
try {
    $db = (new Database())->connect();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Check if employees table has data
try {
    $stmt = $db->query("SELECT employee_id, full_name FROM employees LIMIT 3");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>✓ Found " . count($employees) . " test employees:</p>";
    echo "<ul>";
    foreach ($employees as $emp) {
        echo "<li>" . htmlspecialchars($emp['employee_id']) . " - " . htmlspecialchars($emp['full_name']) . 
             " <a href='Employee Database.php?action=delete&id=" . urlencode($emp['employee_id']) . "' target='_blank'>Test Delete</a></li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error querying employees: " . $e->getMessage() . "</p>";
}

// Test 3: Test the actual deletion logic (without deleting)
echo "<h3>Testing Deletion Logic (Simulation)</h3>";
$testId = isset($employees[0]) ? $employees[0]['employee_id'] : null;

if ($testId) {
    echo "<p>Testing with employee ID: " . htmlspecialchars($testId) . "</p>";
    
    try {
        // Start transaction
        $db->beginTransaction();
        
        // Test deletion
        $stmt = $db->prepare("DELETE FROM employees WHERE employee_id = ?");
        $stmt->execute([$testId]);
        $deletedRows = $stmt->rowCount();
        
        echo "<p>Deletion would affect: $deletedRows row(s)</p>";
        
        // Rollback to not actually delete
        $db->rollBack();
        echo "<p style='color: green;'>✓ Deletion logic works (transaction rolled back)</p>";
        
    } catch (Exception $e) {
        $db->rollBack();
        echo "<p style='color: red;'>✗ Deletion test failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>No employees available for testing</p>";
}

echo "<h3>Manual Testing Instructions</h3>";
echo "<ol>";
echo "<li>Go to the Employee Database page</li>";
echo "<li>Click on any employee's delete button (red trash icon)</li>";
echo "<li>Confirm the deletion in the popup</li>";
echo "<li>Check if the employee disappears from the list</li>";
echo "<li>Check if success message appears</li>";
echo "</ol>";

echo "<p><a href='Employee Database.php'>Go to Employee Database</a></p>";
?>
