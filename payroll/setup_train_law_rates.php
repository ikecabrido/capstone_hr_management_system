<?php
/**
 * Setup TRAIN Law Contribution Rates Table
 * Applies the migration and seeds the database with official TRAIN Law rates
 * 
 * Run this once to initialize the contribution rates table
 */

require_once 'models/payrollModel.php';
require_once '../auth/database.php';

$db = Database::getInstance()->getConnection();

echo "=== TRAIN Law Contribution Rates Setup ===\n\n";

try {
    // Read and execute the migration SQL
    $migrationFile = __DIR__ . '/migrations/001_add_contribution_rates_table.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)), function($s) { return !empty($s); });
    
    foreach ($statements as $statement) {
        echo "Executing: " . substr($statement, 0, 60) . "...\n";
        $db->exec($statement);
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
    // Verify the table and data
    $result = $db->query("SELECT * FROM pr_contribution_rates WHERE is_active = 1 ORDER BY contribution_type, min_salary");
    $rates = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n=== Current 2026 TRAIN Law Rates ===\n\n";
    
    $previousType = '';
    foreach ($rates as $rate) {
        $desc = '';
        if ($rate['contribution_type'] === 'sss') {
            $desc = 'Social Security System (SSS)';
        } elseif ($rate['contribution_type'] === 'philhealth') {
            $desc = 'Philippine Health Insurance (PhilHealth)';
        } elseif ($rate['contribution_type'] === 'pagibig') {
            $desc = 'Home Development Mutual Fund (Pag-IBIG)';
        }
        
        // Print header only once per contribution type
        if ($rate['contribution_type'] !== $previousType) {
            echo "{$desc}\n";
            $previousType = $rate['contribution_type'];
        }
        
        echo "  Employee Rate: {$rate['employee_rate']}%\n";
        if ($rate['min_salary'] > 0 || $rate['max_salary'] < 9999999) {
            echo "  Salary Range: ₱{$rate['min_salary']} - ₱{$rate['max_salary']}\n";
        }
        echo "  Active: " . ($rate['is_active'] ? 'Yes' : 'No') . "\n\n";
    }
    
    echo "✓ Setup complete! 2026 TRAIN Law contribution rates are now active in the database.\n";
    echo "\nNote: Payroll calculations will now use 2026 rates:\n";
    echo "  - SSS: 5% of monthly salary (flat rate)\n";
    echo "  - PhilHealth: 2.5% of monthly salary (flat rate)\n";
    echo "  - Pag-IBIG: 1% for salaries ≤₱1,500, 2% for salaries >₱1,500\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
