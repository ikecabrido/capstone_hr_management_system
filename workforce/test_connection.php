<?php
/**
 * Test Database Connection
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

echo "Testing Database Connection...\n\n";
echo "Config DB_NAME: " . DB_NAME . "\n";
echo "Config DB_HOST: " . DB_HOST . "\n";
echo "Config DB_USER: " . DB_USER . "\n\n";

try {
    $db = Database::getInstance();
    echo "✅ Database connection successful!\n\n";
    
    // Test 1: Count employees
    echo "Test 1: Counting employees...\n";
    $query = "SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'";
    $result = $db->fetchOne($query);
    echo "Result: " . json_encode($result) . "\n\n";
    
    // Test 2: Get all employees
    echo "Test 2: Getting all active employees...\n";
    $query = "SELECT employee_id, full_name, department, position FROM employees WHERE employment_status = 'Active'";
    $results = $db->fetchAll($query);
    echo "Result: " . json_encode($results) . "\n\n";
    
    // Test 3: Count performance reviews
    echo "Test 3: Counting performance reviews...\n";
    $query = "SELECT COUNT(*) as count FROM performance_reviews WHERE status = 'completed'";
    $result = $db->fetchOne($query);
    echo "Result: " . json_encode($result) . "\n\n";
    
    // Test 4: Count resignations
    echo "Test 4: Counting resignations...\n";
    $query = "SELECT COUNT(*) as count FROM resignations WHERE status IN ('approved')";
    $result = $db->fetchOne($query);
    echo "Result: " . json_encode($result) . "\n\n";
    
    echo "✅ All tests passed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
