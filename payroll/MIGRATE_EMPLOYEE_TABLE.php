<?php
/**
 * PAYROLL EMPLOYEE TABLE MIGRATION SCRIPT
 * Migrates data from old VARCHAR(50) employee_id to new INT employee_id structure
 */
require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>🔄 Payroll Employee Table Migration</h1>";
    echo "<hr>";
    
    // Step 1: Check if old tables exist
    echo "<h2>Step 1: Check Current Structure</h2>";
    
    $result = $db->query("SHOW COLUMNS FROM pr_employee_details WHERE Field = 'employee_id'");
    $columns = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($columns) {
        echo "<strong>Current employee_id type:</strong> " . $columns['Type'] . "<br>";
        
        if (strpos($columns['Type'], 'INT') !== false) {
            echo "<span style='color:green'>✓ Already using INT - no migration needed</span><br>";
            exit;
        }
    } else {
        echo "<span style='color:orange'>⚠ pr_employee_details table not found</span><br>";
        echo "Run this first: Import payroll/payroll_employee_config.sql<br>";
        exit;
    }
    
    // Step 2: Check for existing data
    echo "<h2>Step 2: Check for Existing Data</h2>";
    
    $result = $db->query("SELECT COUNT(*) as cnt FROM pr_employee_details");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $dataCount = $row['cnt'];
    
    echo "Records in pr_employee_details: <strong>$dataCount</strong><br>";
    echo "Records in pr_employee_benefits: ";
    
    $result = $db->query("SELECT COUNT(*) as cnt FROM pr_employee_benefits");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<strong>" . $row['cnt'] . "</strong><br>";
    
    if ($dataCount == 0) {
        echo "<span style='color:green'>✓ No data to migrate - can safely alter tables</span><br>";
        echo "<p>Your new employee table structure is ready to use!</p>";
        exit;
    }
    
    // Step 3: If data exists, backup and convert
    echo "<h2>Step 3: Data Migration</h2>";
    
    echo "<p style='background:#fff3cd; padding:10px; border-radius:5px;'>";
    echo "<strong>⚠ WARNING:</strong> Existing employee configuration data will be converted.<br>";
    echo "Backup your database before proceeding!<br>";
    echo "</p>";
    
    echo "<p>If you have existing payroll configurations:</p>";
    echo "<ol>";
    echo "<li>The system will attempt to match employee_id (as text) to employees.employee_id (as INT)</li>";
    echo "<li>If some employee_ids are not found, those records may have issues</li>";
    echo "<li>Recommended: Delete old payroll config and re-enter via the UI</li>";
    echo "</ol>";
    
    echo "<form method='POST'>";
    echo "<button type='submit' name='migrate' class='btn btn-warning' style='padding:10px 20px; background:#ffc107; border:none; border-radius:5px; cursor:pointer;'>";
    echo "Proceed with Migration";
    echo "</button>";
    echo "<p style='color:red; font-size:12px;'>Click only after backing up your database!</p>";
    echo "</form>";
    
    // Step 4: Perform migration if requested
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['migrate'])) {
        echo "<h2>Step 4: Executing Migration</h2>";
        
        try {
            // Drop foreign keys
            echo "Removing foreign key constraints...<br>";
            $db->exec("ALTER TABLE pr_employee_details DROP FOREIGN KEY pr_employee_details_ibfk_1");
            $db->exec("ALTER TABLE pr_employee_benefits DROP FOREIGN KEY pr_employee_benefits_ibfk_1");
            echo "<span style='color:green'>✓ Foreign keys removed</span><br>";
            
            // Alter column type
            echo "Converting employee_id to INT...<br>";
            $db->exec("ALTER TABLE pr_employee_details MODIFY employee_id INT NOT NULL");
            $db->exec("ALTER TABLE pr_employee_benefits MODIFY employee_id INT NOT NULL");
            echo "<span style='color:green'>✓ Columns converted to INT</span><br>";
            
            // Recreate foreign keys
            echo "Re-adding foreign key constraints...<br>";
            $db->exec("ALTER TABLE pr_employee_details ADD CONSTRAINT pr_employee_details_ibfk_1 FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE");
            $db->exec("ALTER TABLE pr_employee_benefits ADD CONSTRAINT pr_employee_benefits_ibfk_1 FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE");
            echo "<span style='color:green'>✓ Foreign keys restored</span><br>";
            
            echo "<h2 style='color:green'>✓ Migration Complete!</h2>";
            echo "<p>Your payroll system is now using the new employee table structure (INT employee_id).</p>";
            echo "<p>You can now safely:</p>";
            echo "<ul>";
            echo "<li>Add new employees and configure their payroll</li>";
            echo "<li>Run payroll calculations</li>";
            echo "<li>Future employee table changes won't affect payroll</li>";
            echo "</ul>";
            
        } catch (Exception $e) {
            echo "<h2 style='color:red'>✗ Migration Failed</h2>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
            echo "<p>Your database may be partially migrated. Check and retry or restore from backup.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
?>
