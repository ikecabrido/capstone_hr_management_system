<?php
/**
 * Fix collation mismatch between exit_management tables and employees table
 * This script changes all utf8mb4_unicode_ci to utf8mb4_general_ci for consistency
 */

session_start();
require_once "../auth/database.php";

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
    
    // List of tables to fix
    $tablesToFix = [
        'resignations',
        'exit_interviews',
        'knowledge_transfer_plans',
        'template_response_sections_sections',
        'settlements',
        'exit_documentation',
        'exit_surveys'
    ];
    
    $results = [];
    
    foreach ($tablesToFix as $table) {
        try {
            // Check current collation
            $checkStmt = $db->query("SHOW CREATE TABLE `$table`");
            $createSql = $checkStmt->fetch(PDO::FETCH_ASSOC)['Create Table'];
            
            if (strpos($createSql, 'utf8mb4_unicode_ci') !== false) {
                // Table needs to be fixed
                $db->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                $results[$table] = 'Fixed - converted to utf8mb4_general_ci';
            } else {
                $results[$table] = 'Already using correct collation';
            }
        } catch (Exception $e) {
            $results[$table] = 'Error: ' . $e->getMessage();
        }
    }
    
    // Also check and fix character columns specifically
    $fixColumnStmt = $db->query("
        SELECT TABLE_NAME, COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND COLUMN_NAME = 'employee_id' 
        AND COLLATION_NAME != 'utf8mb4_general_ci'
    ");
    
    $columnResults = [];
    foreach ($fixColumnStmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        try {
            $db->exec("ALTER TABLE `{$col['TABLE_NAME']}` MODIFY COLUMN `{$col['COLUMN_NAME']}` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $columnResults[] = $col['TABLE_NAME'] . '.' . $col['COLUMN_NAME'] . ' - Fixed';
        } catch (Exception $e) {
            $columnResults[] = $col['TABLE_NAME'] . '.' . $col['COLUMN_NAME'] . ' - Error: ' . $e->getMessage();
        }
    }
    
    // Test the JOIN after fixing
    $testStmt = $db->query("
        SELECT r.id, e.full_name, r.resignation_type 
        FROM resignations r 
        JOIN employees e ON r.employee_id = e.employee_id 
        LIMIT 1
    ");
    
    $testResult = $testStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Collation fixed successfully',
        'tables_fixed' => $results,
        'columns_fixed' => $columnResults,
        'join_test_result' => count($testResult) > 0 ? $testResult : 'No resignations to test',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
