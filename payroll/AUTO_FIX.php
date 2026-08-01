<?php
/**
 * Automatic Payroll Period Table Fix
 * Fixes AUTO_INCREMENT issues and creates pr_periods table if missing
 */
require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h1>🔧 Payroll Period Table Fix</h1>";
    echo "<hr>";
    
    // Check if table exists
    $result = $db->query("SHOW TABLES LIKE 'pr_periods'");
    $tableExists = $result->rowCount() > 0;
    
    if (!$tableExists) {
        echo "<h2>Creating pr_periods table...</h2>";
        
        $createTableSQL = "
        CREATE TABLE pr_periods (
            period_id INT PRIMARY KEY AUTO_INCREMENT,
            period_name VARCHAR(100) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            pay_date DATE NOT NULL,
            status VARCHAR(50) DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_start_date (start_date),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        
        try {
            $db->exec($createTableSQL);
            echo "<span style='color:green; font-size:16px;'>✓ Table created successfully</span><br><br>";
        } catch (Exception $e) {
            echo "<span style='color:red;'>✗ Error creating table: " . $e->getMessage() . "</span><br>";
            exit;
        }
    } else {
        echo "<h2>Repairing existing pr_periods table...</h2>";
        
        // Get current table info
        $result = $db->query("SELECT * FROM pr_periods LIMIT 0");
        $columns = [];
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $meta = $result->getColumnMeta($i);
            $columns[$meta['name']] = $meta;
        }
        
        // Check if period_id exists and has AUTO_INCREMENT
        $result = $db->query("DESCRIBE pr_periods");
        $tableColumns = $result->fetchAll(PDO::FETCH_ASSOC);
        
        $periodIdExists = false;
        $periodIdHasAutoInc = false;
        
        foreach ($tableColumns as $col) {
            if ($col['Field'] === 'period_id') {
                $periodIdExists = true;
                if (strpos($col['Extra'], 'auto_increment') !== false) {
                    $periodIdHasAutoInc = true;
                }
            }
        }
        
        if (!$periodIdExists) {
            echo "ERROR: period_id column not found. Recreating table...<br><br>";
            
            $db->exec("DROP TABLE pr_periods");
            echo "Dropped old table<br>";
            
            $createTableSQL = "
            CREATE TABLE pr_periods (
                period_id INT PRIMARY KEY AUTO_INCREMENT,
                period_name VARCHAR(100) NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                pay_date DATE NOT NULL,
                status VARCHAR(50) DEFAULT 'open',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_start_date (start_date),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ";
            
            $db->exec($createTableSQL);
            echo "<span style='color:green;'>✓ Table recreated successfully</span><br><br>";
        } else {
            echo "period_id column found<br>";
            echo "Has AUTO_INCREMENT: " . ($periodIdHasAutoInc ? "YES" : "NO") . "<br>";
            
            if (!$periodIdHasAutoInc) {
                echo "Adding AUTO_INCREMENT to period_id...<br>";
                $db->exec("ALTER TABLE pr_periods MODIFY period_id INT AUTO_INCREMENT");
                echo "<span style='color:green;'>✓ AUTO_INCREMENT added</span><br>";
            }
            
            // Get max period_id
            $result = $db->query("SELECT MAX(period_id) as max_id FROM pr_periods");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $maxId = $row['max_id'] ?? 0;
            
            echo "Current MAX(period_id): $maxId<br>";
            
            // Reset auto increment to max_id + 1
            $newAutoInc = max(1, $maxId + 1);
            echo "Setting AUTO_INCREMENT to $newAutoInc...<br>";
            
            $db->exec("ALTER TABLE pr_periods AUTO_INCREMENT = $newAutoInc");
            echo "<span style='color:green;'>✓ AUTO_INCREMENT reset</span><br>";
        }
    }
    
    echo "<hr>";
    echo "<h2>✓ Fix Complete!</h2>";
    echo "<p>You can now create payroll periods. Go to:</p>";
    echo "<p><a href='views/periodManager.php' style='font-size:16px; padding:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px;'>→ Period Manager</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
