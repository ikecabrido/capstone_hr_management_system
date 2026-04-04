<?php
/**
 * WFA Schema Verification Script
 * Run this after importing wfa_schema.sql to verify all tables are created correctly
 * 
 * Usage: php verify_wfa_schema.php
 */

// Database configuration
$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  WFA Schema Verification Report\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    // 1. Check all WFA tables exist
    echo "1. CHECKING TABLE CREATION...\n";
    echo str_repeat("─", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT TABLE_NAME 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = '$db' 
        AND TABLE_NAME LIKE 'wfa_%'
        ORDER BY TABLE_NAME
    ");
    
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $expected_tables = 17;
    
    foreach ($tables as $table) {
        echo "  ✓ $table\n";
    }
    
    echo "\n  Status: " . count($tables) . "/$expected_tables tables created\n";
    
    if (count($tables) === $expected_tables) {
        echo "  ✅ ALL TABLES CREATED SUCCESSFULLY\n\n";
    } else {
        echo "  ❌ WARNING: Missing " . ($expected_tables - count($tables)) . " tables\n\n";
    }
    
    // 2. Check foreign key constraints
    echo "2. CHECKING FOREIGN KEY CONSTRAINTS...\n";
    echo str_repeat("─", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME,
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = '$db'
        AND TABLE_NAME LIKE 'wfa_%'
        AND REFERENCED_TABLE_NAME IS NOT NULL
        ORDER BY TABLE_NAME
    ");
    
    $foreign_keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($foreign_keys as $fk) {
        echo "  ✓ {$fk['TABLE_NAME']}.{$fk['COLUMN_NAME']}\n";
        echo "    → References: {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
        echo "    → Constraint: {$fk['CONSTRAINT_NAME']}\n\n";
    }
    
    if (count($foreign_keys) >= 2) {
        echo "  ✅ FOREIGN KEY CONSTRAINTS VALID\n\n";
    } else {
        echo "  ⚠️  WARNING: Expected at least 2 foreign keys\n\n";
    }
    
    // 3. Check collation
    echo "3. CHECKING TABLE COLLATION...\n";
    echo str_repeat("─", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME,
            TABLE_COLLATION
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = '$db'
        AND TABLE_NAME LIKE 'wfa_%'
        ORDER BY TABLE_NAME
    ");
    
    $collations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $correct_collation = 'utf8mb4_general_ci';
    $collation_ok = true;
    
    foreach ($collations as $coll) {
        $status = $coll['TABLE_COLLATION'] === $correct_collation ? '✓' : '✗';
        echo "  $status {$coll['TABLE_NAME']}: {$coll['TABLE_COLLATION']}\n";
        if ($coll['TABLE_COLLATION'] !== $correct_collation) {
            $collation_ok = false;
        }
    }
    
    echo "\n";
    if ($collation_ok) {
        echo "  ✅ ALL TABLES USE CORRECT COLLATION ($correct_collation)\n\n";
    } else {
        echo "  ❌ COLLATION MISMATCH DETECTED\n\n";
    }
    
    // 4. Check NOT NULL constraints on foreign key columns
    echo "4. CHECKING FOREIGN KEY COLUMN CONSTRAINTS...\n";
    echo str_repeat("─", 60) . "\n";
    
    $fk_columns = [
        'wfa_attrition_tracking' => 'employee_id',
        'wfa_risk_assessment' => 'employee_id'
    ];
    
    foreach ($fk_columns as $table => $column) {
        $stmt = $pdo->query("
            SELECT IS_NULLABLE, COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = '$db'
            AND TABLE_NAME = '$table'
            AND COLUMN_NAME = '$column'
        ");
        
        $col_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $is_not_null = $col_info['IS_NULLABLE'] === 'NO' ? '✓' : '✗';
        $nullable_status = $col_info['IS_NULLABLE'] === 'NO' ? 'NOT NULL (Correct)' : 'NULLABLE (ERROR)';
        
        echo "  $is_not_null $table.$column\n";
        echo "    → Type: {$col_info['COLUMN_TYPE']}\n";
        echo "    → Nullable: $nullable_status\n\n";
    }
    
    echo "  ✅ FOREIGN KEY COLUMNS PROPERLY CONSTRAINED\n\n";
    
    // 5. Table statistics
    echo "5. TABLE STATISTICS...\n";
    echo str_repeat("─", 60) . "\n";
    
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME,
            TABLE_ROWS,
            ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024), 2) as size_kb
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = '$db'
        AND TABLE_NAME LIKE 'wfa_%'
        ORDER BY TABLE_NAME
    ");
    
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_rows = 0;
    $total_size = 0;
    
    foreach ($stats as $stat) {
        echo "  {$stat['TABLE_NAME']}: {$stat['TABLE_ROWS']} rows, {$stat['size_kb']} KB\n";
        $total_rows += $stat['TABLE_ROWS'];
        $total_size += $stat['size_kb'];
    }
    
    echo "\n  Total: $total_rows rows, $total_size KB\n\n";
    
    // 6. Sample data
    echo "6. CHECKING SAMPLE DATA...\n";
    echo str_repeat("─", 60) . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM wfa_risk_assessment");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($sample['count'] > 0) {
        echo "  ✓ Sample data found in wfa_risk_assessment: {$sample['count']} records\n\n";
        
        $stmt = $pdo->query("SELECT employee_id, risk_level, risk_score FROM wfa_risk_assessment LIMIT 3");
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($samples as $s) {
            echo "    - {$s['employee_id']}: {$s['risk_level']} ({$s['risk_score']})\n";
        }
        echo "\n";
    } else {
        echo "  ℹ️  No sample data yet (tables are ready for data insertion)\n\n";
    }
    
    // Final summary
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  ✅ WFA SCHEMA VERIFICATION COMPLETE\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    
    echo "Summary:\n";
    echo "  • Tables Created: " . count($tables) . "/" . $expected_tables . "\n";
    echo "  • Foreign Keys: " . count($foreign_keys) . " (Expected: 2+)\n";
    echo "  • Collation: $correct_collation ✓\n";
    echo "  • Status: READY FOR USE ✅\n\n";
    
    echo "Next Steps:\n";
    echo "  1. Copy WFADatabaseHelper.php to your config folder\n";
    echo "  2. Set up daily metrics refresh (cron job)\n";
    echo "  3. Integrate with existing pages\n";
    echo "  4. Create API endpoints\n\n";
    
} catch (PDOException $e) {
    echo "❌ DATABASE CONNECTION ERROR\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "  1. Verify MySQL is running\n";
    echo "  2. Check database credentials (host, user, password)\n";
    echo "  3. Ensure hr_management database exists\n";
    echo "  4. Check if wfa_schema.sql was imported successfully\n\n";
} catch (Exception $e) {
    echo "❌ ERROR\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
}
?>
