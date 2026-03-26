<?php
session_start();
require_once "../auth/database.php";

// Test the DELETE functionality
$db = (new Database())->connect();

echo "<h2>Testing Employee DELETE Functionality</h2>";

// 1. Check if employees table exists and has data
echo "<h3>1. Checking employees table</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM employees");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p>Total employees: <strong>$count</strong></p>";
    
    // Show first few employees
    $stmt = $db->query("SELECT employee_id, full_name FROM employees LIMIT 5");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Sample employees:</p>";
    echo "<ul>";
    foreach ($employees as $emp) {
        echo "<li>" . htmlspecialchars($emp['employee_id']) . " - " . htmlspecialchars($emp['full_name']) . "</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking employees table: " . $e->getMessage() . "</p>";
}

// 2. Test deletion with a test employee (if exists)
echo "<h3>2. Testing deletion process</h3>";
$testId = 'EMP001'; // Change this to an existing employee ID

try {
    // Check if test employee exists
    $stmt = $db->prepare("SELECT employee_id, full_name FROM employees WHERE employee_id = ?");
    $stmt->execute([$testId]);
    $testEmployee = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testEmployee) {
        echo "<p>Found test employee: " . htmlspecialchars($testEmployee['employee_id']) . " - " . htmlspecialchars($testEmployee['full_name']) . "</p>";
        
        // Test the deletion logic (without actually deleting)
        echo "<h4>Simulating deletion process:</h4>";
        
        // Start transaction
        $db->beginTransaction();
        
        try {
            // 1. Delete from main employees table
            $stmt = $db->prepare("DELETE FROM employees WHERE employee_id = ?");
            $result = $stmt->execute([$testId]);
            $deletedRows = $stmt->rowCount();
            echo "<p>Employees table deletion: $deletedRows row(s) affected</p>";
            
            if ($deletedRows === 0) {
                throw new Exception("Employee not found");
            }
            
            // Rollback since we're just testing
            $db->rollBack();
            echo "<p style='color: green;'>✓ Deletion logic works correctly (rolled back for testing)</p>";
            
        } catch (Exception $e) {
            $db->rollBack();
            echo "<p style='color: red;'>✗ Deletion failed: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p>Test employee '$testId' not found. Please update the test ID.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error during test: " . $e->getMessage() . "</p>";
}

// 3. Check related tables that might be causing issues
echo "<h3>3. Checking related tables</h3>";
$tables_to_check = [
    'goals',
    'performance_reports', 
    'patients',
    'employee_accounts',
    'user_sessions',
    'user_login_attempts'
];

foreach ($tables_to_check as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table LIMIT 1");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>✓ Table '$table' exists and has $count row(s)</p>";
    } catch (Exception $e) {
        echo "<p>⚠ Table '$table' doesn't exist or is not accessible: " . $e->getMessage() . "</p>";
    }
}

// 4. Check database connection and permissions
echo "<h3>4. Database connection check</h3>";
try {
    $stmt = $db->query("SELECT USER() as user, DATABASE() as database");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Connected as: " . htmlspecialchars($info['user']) . "</p>";
    echo "<p>Database: " . htmlspecialchars($info['database']) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Connection info error: " . $e->getMessage() . "</p>";
}

echo "<h3>5. Recommendations</h3>";
echo "<ul>";
echo "<li>If deletion is not working, check the browser's developer console for JavaScript errors</li>";
echo "<li>Check PHP error logs for detailed error messages</li>";
echo "<li>Ensure the DELETE link is properly formed and clickable</li>";
echo "<li>Verify that session messages are being displayed properly</li>";
echo "</ul>";

?>
