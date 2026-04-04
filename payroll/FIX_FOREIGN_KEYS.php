<?php
/**
 * Fix Foreign Key Issues for Payroll Tables
 * Diagnoses and fixes constraint errors
 */
require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>🔧 Foreign Key Constraint Fix</h1>";
    echo "<hr>";
    
    // Step 1: Check employees table
    echo "<h2>Step 1: Verify Employees Table</h2>";
    
    $result = $db->query("SHOW TABLES LIKE 'employees'");
    if ($result->rowCount() === 0) {
        echo "<span style='color:red'>✗ ERROR: employees table does not exist!</span><br>";
        echo "<p>You must create the employees table first.</p>";
        echo "<p>Import this file via phpMyAdmin: <code>c:\\Users\\Joel\\Downloads\\employees.sql</code></p>";
        exit;
    }
    
    echo "<span style='color:green'>✓ employees table exists</span><br>";
    
    // Step 2: Check employee_id column type
    echo "<h2>Step 2: Check Column Types</h2>";
    
    $result = $db->query("SHOW COLUMNS FROM employees WHERE Field = 'employee_id'");
    $empCol = $result->fetch(PDO::FETCH_ASSOC);
    
    echo "<strong>employees.employee_id type:</strong> " . $empCol['Type'] . "<br>";
    echo "<strong>employees.employee_id key:</strong> " . ($empCol['Key'] === 'PRI' ? 'PRIMARY KEY ✓' : 'NOT PRIMARY KEY ✗') . "<br>";
    
    if ($empCol['Key'] !== 'PRI') {
        echo "<span style='color:orange'>⚠ WARNING: employee_id is not PRIMARY KEY</span><br>";
    }
    
    // Step 3: Drop old payroll tables to remove constraint issues
    echo "<h2>Step 3: Clean Up Old Payroll Tables</h2>";
    
    $tables = ['pr_employee_benefits', 'pr_employee_details'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "Dropping table: <code>$table</code>...<br>";
            try {
                $db->exec("DROP TABLE $table");
                echo "<span style='color:green'>✓ Dropped</span><br>";
            } catch (Exception $e) {
                echo "<span style='color:orange'>⚠ Could not drop (might be in use): " . $e->getMessage() . "</span><br>";
            }
        }
    }
    
    // Step 4: Create tables with corrected structure
    echo "<h2>Step 4: Create Payroll Tables</h2>";
    
    $sql = "
    -- Table 1: Employee Payroll Details
    CREATE TABLE IF NOT EXISTS pr_employee_details (
        id INT PRIMARY KEY AUTO_INCREMENT,
        employee_id INT NOT NULL UNIQUE,
        base_salary DECIMAL(12,2) NOT NULL COMMENT 'Monthly base salary',
        position_type ENUM('Admin', 'Teacher', 'Other') DEFAULT 'Admin' COMMENT 'Determines calculation method',
        teacher_qualification ENUM('ProfEd', 'LPT', 'Masteral') DEFAULT 'ProfEd' COMMENT 'Only for teachers',
        teaching_units DECIMAL(5,2) DEFAULT 0 COMMENT 'Number of units (e.g., 30)',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
        INDEX idx_employee_id (employee_id),
        INDEX idx_position_type (position_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    
    -- Table 2: Employee Benefits/Deductions
    CREATE TABLE IF NOT EXISTS pr_employee_benefits (
        id INT PRIMARY KEY AUTO_INCREMENT,
        employee_id INT NOT NULL UNIQUE,
        has_sss BOOLEAN DEFAULT 1 COMMENT 'SSS Contribution enrollment',
        has_philhealth BOOLEAN DEFAULT 1 COMMENT 'PhilHealth enrollment',
        has_pagibig BOOLEAN DEFAULT 1 COMMENT 'Pag-IBIG enrollment',
        sss_amount_override DECIMAL(10,2) DEFAULT NULL COMMENT 'Manual override if needed',
        philhealth_amount_override DECIMAL(10,2) DEFAULT NULL,
        pagibig_amount_override DECIMAL(10,2) DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
        INDEX idx_employee_id (employee_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    try {
        $db->exec($sql);
        echo "<span style='color:green'>✓ pr_employee_details created</span><br>";
        echo "<span style='color:green'>✓ pr_employee_benefits created</span><br>";
    } catch (Exception $e) {
        echo "<span style='color:red'>✗ Error creating tables: " . $e->getMessage() . "</span><br>";
        exit;
    }
    
    // Step 5: Insert sample data
    echo "<h2>Step 5: Insert Sample Data</h2>";
    
    $sampleData = "
    -- Sample Employee Configurations
    INSERT INTO pr_employee_details (employee_id, base_salary, position_type, teacher_qualification, teaching_units)
    VALUES 
    (1, 30000.00, 'Admin', 'ProfEd', 0),
    (2, 32000.00, 'Admin', 'ProfEd', 0),
    (3, 28000.00, 'Admin', 'ProfEd', 0);
    
    -- Sample Benefits/Trio Deductions
    INSERT INTO pr_employee_benefits (employee_id, has_sss, has_philhealth, has_pagibig)
    VALUES 
    (1, 1, 1, 1),
    (2, 1, 1, 1),
    (3, 1, 1, 1);
    ";
    
    try {
        $db->exec($sampleData);
        echo "<span style='color:green'>✓ Sample data inserted</span><br>";
    } catch (Exception $e) {
        echo "<span style='color:orange'>⚠ Could not insert sample data: " . $e->getMessage() . "</span><br>";
    }
    
    echo "<hr>";
    echo "<h2 style='color:green'>✓ Fix Complete!</h2>";
    echo "<p>Your payroll tables are now ready. You can:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='views/payrollEmployeeConfig.php'>Payroll Configuration</a> to add more employees</li>";
    echo "<li>Create a payroll period</li>";
    echo "<li>Run payroll calculations</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
