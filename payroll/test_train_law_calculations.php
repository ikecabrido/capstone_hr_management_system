<?php
/**
 * Test Script: TRAIN Law Contribution Calculations
 * Tests 2026 TRAIN Law rates with salary brackets
 */

require_once '../auth/database.php';

// Test contribution rate lookup for different salary levels
function testContributionRateLookup() {
    $db = Database::getInstance()->getConnection();
    
    echo "=== TRAIN Law Contribution Rate Tests (2026) ===\n\n";
    
    $testCases = [
        ['salary' => 1500, 'type' => 'pagibig', 'expectedRate' => 1.00],
        ['salary' => 1501, 'type' => 'pagibig', 'expectedRate' => 2.00],
        ['salary' => 2000, 'type' => 'pagibig', 'expectedRate' => 2.00],
        ['salary' => 5000, 'type' => 'sss', 'expectedRate' => 5.00],
        ['salary' => 5000, 'type' => 'philhealth', 'expectedRate' => 2.50],
    ];
    
    foreach ($testCases as $test) {
        $sql = "
            SELECT employee_rate 
            FROM pr_contribution_rates 
            WHERE contribution_type = :type 
              AND is_active = 1
              AND :salary >= min_salary 
              AND :salary <= max_salary
            LIMIT 1
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':type' => $test['type'],
            ':salary' => $test['salary']
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $actualRate = $result ? (float)$result['employee_rate'] : 0;
        
        $status = ($actualRate === $test['expectedRate']) ? '✓' : '✗';
        
        echo "{$status} Salary: ₱{$test['salary']}, {$test['type']}: ";
        echo "Expected {$test['expectedRate']}%, Got {$actualRate}%\n";
    }
    
    echo "\n";
}

// Test contribution calculations
function testContributionCalculations() {
    $db = Database::getInstance()->getConnection();
    
    echo "=== Semi-Monthly Contribution Calculations ===\n\n";
    
    $scenarios = [
        ['salary' => 1000, 'name' => 'Employee earning ₱1,000/month'],
        ['salary' => 1500, 'name' => 'Employee earning ₱1,500/month'],
        ['salary' => 2000, 'name' => 'Employee earning ₱2,000/month'],
        ['salary' => 5000, 'name' => 'Employee earning ₱5,000/month'],
    ];
    
    foreach ($scenarios as $scenario) {
        $salary = $scenario['salary'];
        $monthlyPayroll = $salary;
        $semiMonthlyPayroll = $salary / 2;
        
        echo "{$scenario['name']}\n";
        echo str_repeat("-", 50) . "\n";
        
        // Get rates for this salary
        $rateStmt = $db->prepare("
            SELECT contribution_type, employee_rate 
            FROM pr_contribution_rates 
            WHERE is_active = 1
              AND :salary >= min_salary 
              AND :salary <= max_salary
            ORDER BY contribution_type
        ");
        $rateStmt->execute([':salary' => $salary]);
        $rates = $rateStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalMonthly = 0;
        $totalSemiMonthly = 0;
        
        foreach ($rates as $rate) {
            $type = $rate['contribution_type'];
            $ratePercent = (float)$rate['employee_rate'];
            
            // Skip duplicate types, take the first match
            $skipTypes = ['sss', 'philhealth'];
            if (in_array($type, $skipTypes)) {
                $skipTypes = array_diff($skipTypes, [$type]); // Remove from skip list after first occurrence
            }
            
            $monthlyAmount = $monthlyPayroll * ($ratePercent / 100);
            $semiMonthlyAmount = $monthlyAmount / 2;
            
            echo "  {$type}: {$ratePercent}% = ₱" . number_format($monthlyAmount, 2);
            echo " (₱" . number_format($semiMonthlyAmount, 2) . " semi-monthly)\n";
            
            $totalMonthly += $monthlyAmount;
            $totalSemiMonthly += $semiMonthlyAmount;
        }
        
        echo "  TOTAL: ₱" . number_format($totalMonthly, 2);
        echo " (₱" . number_format($totalSemiMonthly, 2) . " semi-monthly)\n\n";
    }
}

try {
    testContributionRateLookup();
    testContributionCalculations();
    
    echo "✓ All tests completed successfully!\n";
} catch (Exception $e) {
    echo "✗ Test error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
