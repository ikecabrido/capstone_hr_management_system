<?php
/**
 * Payroll Period Table Diagnostic and Fix Script
 * Fixes AUTO_INCREMENT issues with pr_periods table
 */
require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Diagnosing pr_periods Table...</h2>";
    echo "<hr>";
    
    // Check if table exists
    $result = $db->query("SHOW TABLES LIKE 'pr_periods'");
    $tableExists = $result->rowCount() > 0;
    
    echo "<strong>Table exists:</strong> " . ($tableExists ? "✓ YES" : "✗ NO") . "<br>";
    
    if (!$tableExists) {
        echo "<h3 style='color:red'>ERROR: pr_periods table does not exist!</h3>";
        echo "<p>You must import the database schema first:</p>";
        echo "<ol>";
        echo "<li>Open phpMyAdmin</li>";
        echo "<li>Select your database</li>";
        echo "<li>Go to the SQL tab</li>";
        echo "<li>Paste the contents of: <code>payroll/payroll_employee_config.sql</code></li>";
        echo "<li>Click Execute</li>";
        echo "</ol>";
        exit;
    }
    
    // Check table structure
    echo "<h3>Table Structure:</h3>";
    $result = $db->query("DESCRIBE pr_periods");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check AUTO_INCREMENT settings
    echo "<h3>Auto Increment Status:</h3>";
    $result = $db->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'pr_periods'");
    $autoIncrement = $result->fetch(PDO::FETCH_ASSOC);
    echo "<strong>Current AUTO_INCREMENT value:</strong> " . ($autoIncrement['AUTO_INCREMENT'] ?? 'NULL') . "<br>";
    
    // Check existing rows
    echo "<h3>Existing Records:</h3>";
    $result = $db->query("SELECT * FROM pr_periods");
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "<strong>Row count:</strong> " . count($rows) . "<br>";
    
    if (count($rows) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        foreach (array_keys($rows[0]) as $key) {
            echo "<th>$key</th>";
        }
        echo "</tr>";
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $val) {
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>Fixes to Apply:</h3>";
    
    // Try to fix
    $issues = [];
    $fixes = [];
    
    // Check if period_id has AUTO_INCREMENT
    $periodIdHasAutoInc = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'period_id' && strpos($col['Extra'], 'auto_increment') !== false) {
            $periodIdHasAutoInc = true;
        }
    }
    
    if (!$periodIdHasAutoInc) {
        $issues[] = "period_id column does not have AUTO_INCREMENT";
        $fixes[] = [
            'description' => 'Add AUTO_INCREMENT to period_id',
            'sql' => 'ALTER TABLE pr_periods MODIFY period_id INT AUTO_INCREMENT'
        ];
    }
    
    if (count($rows) > 0) {
        $maxId = max(array_column($rows, 'period_id'));
        $currentAutoInc = $autoIncrement['AUTO_INCREMENT'] ?? 1;
        
        if ($currentAutoInc <= $maxId) {
            $issues[] = "AUTO_INCREMENT value ($currentAutoInc) is not greater than MAX(period_id) ($maxId)";
            $newAutoInc = $maxId + 1;
            $fixes[] = [
                'description' => "Reset AUTO_INCREMENT to $newAutoInc",
                'sql' => "ALTER TABLE pr_periods AUTO_INCREMENT = $newAutoInc"
            ];
        }
    }
    
    if (empty($issues)) {
        echo "<span style='color:green'>✓ No issues found! Table structure looks good.</span>";
    } else {
        echo "<span style='color:red'>Found " . count($issues) . " issue(s):</span><br>";
        echo "<ul>";
        foreach ($issues as $issue) {
            echo "<li>$issue</li>";
        }
        echo "</ul>";
        
        echo "<h4>Applying Fixes:</h4>";
        foreach ($fixes as $fix) {
            try {
                echo "<strong>" . $fix['description'] . "</strong>...<br>";
                echo "SQL: <code>" . $fix['sql'] . "</code><br>";
                $db->exec($fix['sql']);
                echo "<span style='color:green'>✓ Success</span><br>";
            } catch (Exception $e) {
                echo "<span style='color:red'>✗ Error: " . $e->getMessage() . "</span><br>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Test Creating a Period:</h3>";
    echo "<p>Try creating a new period now. If you still get an error, <a href='VERIFY_TABLES.php'>check the verification report</a>.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
?>
