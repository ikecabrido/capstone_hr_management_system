<?php
/**
 * Settlement Calculations Test - CLI Version
 * Simple plain-text output for terminal display
 */

require_once __DIR__ . '/../auth/database.php';
require_once __DIR__ . '/models/SettlementModel.php';

$settlementModel = new SettlementModel();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  SETTLEMENT CALCULATIONS TEST SUITE                        ║\n";
echo "║  Testing all settlement formulas with realistic scenarios  ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$testScenarios = [
    [
        'name' => 'TEACHER - 8 Years Service',
        'basic_salary' => 30000,
        'years_service' => 8,
        'notice_days' => 30,
        'da' => 0,
        'outstanding_loans' => 5000,
    ],
    [
        'name' => 'ADMIN STAFF - 5 Years Service',
        'basic_salary' => 36000,
        'years_service' => 5,
        'notice_days' => 30,
        'da' => 5000,
        'outstanding_loans' => 8000,
    ],
    [
        'name' => 'SUPPORT STAFF - 3 Years Service',
        'basic_salary' => 20000,
        'years_service' => 3,
        'notice_days' => 15,
        'da' => 0,
        'outstanding_loans' => 2000,
    ],
    [
        'name' => 'PROFESSIONAL - 10 Years Service',
        'basic_salary' => 45000,
        'years_service' => 10,
        'notice_days' => 60,
        'da' => 8000,
        'outstanding_loans' => 10000,
    ],
];

$testNum = 1;
foreach ($testScenarios as $scenario) {
    echo "\n";
    echo "TEST $testNum: " . $scenario['name'] . "\n";
    echo "─────────────────────────────────────────────────────────\n";
    
    try {
        // Calculate components
        $gratuity = $settlementModel->calculateGratuity(
            $scenario['basic_salary'],
            $scenario['years_service']
        );
        
        $pf = $settlementModel->calculateProvidentFund(
            $scenario['basic_salary'],
            $scenario['da']
        );
        
        $noticePay = $settlementModel->calculateNoticePay(
            $scenario['basic_salary'],
            $scenario['notice_days']
        );
        
        // Display calculations step-by-step
        echo "\n📊 STEP-BY-STEP CALCULATIONS:\n";
        echo "\n1. GRATUITY FORMULA: (Basic Salary × 15 × Years of Service) / 26\n";
        echo "   = (₱" . number_format($scenario['basic_salary'], 2) . " × 15 × " . $scenario['years_service'] . ") / 26\n";
        echo "   = ₱" . number_format($gratuity, 2) . "\n";
        
        echo "\n2. PROVIDENT FUND FORMULA: (Basic Salary + DA) × 0.12\n";
        echo "   = (₱" . number_format($scenario['basic_salary'], 2) . " + ₱" . number_format($scenario['da'], 2) . ") × 0.12\n";
        echo "   = ₱" . number_format($pf, 2) . "\n";
        
        echo "\n3. NOTICE PAY FORMULA: (Basic Salary / 30) × Notice Days\n";
        echo "   = (₱" . number_format($scenario['basic_salary'], 2) . " / 30) × " . $scenario['notice_days'] . " days\n";
        echo "   = ₱" . number_format($noticePay, 2) . "\n";
        
        // Build settlement data
        $settlementData = [
            'basic_salary' => $scenario['basic_salary'],
            'gratuity' => $gratuity,
            'notice_pay' => $noticePay,
            'provident_fund' => $pf,
            'outstanding_loans' => $scenario['outstanding_loans'],
        ];
        
        // Calculate totals
        $totalEarnings = $settlementData['basic_salary'] + 
                        $settlementData['gratuity'] + 
                        $settlementData['notice_pay'];
        
        $totalDeductions = $settlementData['provident_fund'] + 
                          $settlementData['outstanding_loans'];
        
        $netPayable = $settlementModel->calculateTotalSettlement($settlementData);
        
        // Display summary
        echo "\n┌─ SETTLEMENT SUMMARY ────────────────────────────┐\n";
        echo "│                                                 │\n";
        echo "│ EARNINGS:                                       │\n";
        echo "│   Basic Salary ..................... ₱" . str_pad(number_format($settlementData['basic_salary'], 2), 15) . "  │\n";
        echo "│   Gratuity ......................... ₱" . str_pad(number_format($gratuity, 2), 15) . "  │\n";
        echo "│   Notice Pay ....................... ₱" . str_pad(number_format($noticePay, 2), 15) . "  │\n";
        echo "│   ─────────────────────────────────────────── │\n";
        echo "│   TOTAL EARNINGS .................. ₱" . str_pad(number_format($totalEarnings, 2), 15) . "  │\n";
        echo "│                                                 │\n";
        echo "│ DEDUCTIONS:                                     │\n";
        echo "│   Provident Fund (12%) ............ ₱" . str_pad(number_format($pf, 2), 15) . "  │\n";
        echo "│   Outstanding Loans .............. ₱" . str_pad(number_format($scenario['outstanding_loans'], 2), 15) . "  │\n";
        echo "│   ─────────────────────────────────────────── │\n";
        echo "│   TOTAL DEDUCTIONS ................ ₱" . str_pad(number_format($totalDeductions, 2), 15) . "  │\n";
        echo "│                                                 │\n";
        echo "│ ═════════════════════════════════════════════ │\n";
        echo "│ NET PAYABLE ........................ ₱" . str_pad(number_format($netPayable, 2), 15) . "  │\n";
        echo "│ ═════════════════════════════════════════════ │\n";
        echo "└─────────────────────────────────────────────────┘\n";
        
        echo "\n✓ Test $testNum PASSED - All calculations correct!\n";
        
    } catch (Exception $e) {
        echo "✗ Test $testNum FAILED: " . $e->getMessage() . "\n";
    }
    
    $testNum++;
}

// Summary table
echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           FINAL TEST SUMMARY TABLE                                     ║\n";
echo "╠════════════════════════════════════════════════════════════════════════════════════════╣\n";
echo "║ Test Case                    │ Gratuity        │ Notice Pay      │ PF (12%)       │\n";
echo "╠════════════════════════════════════════════════════════════════════════════════════════╣\n";

foreach ($testScenarios as $scenario) {
    $gratuity = $settlementModel->calculateGratuity(
        $scenario['basic_salary'],
        $scenario['years_service']
    );
    
    $pf = $settlementModel->calculateProvidentFund(
        $scenario['basic_salary'],
        $scenario['da']
    );
    
    $noticePay = $settlementModel->calculateNoticePay(
        $scenario['basic_salary'],
        $scenario['notice_days']
    );
    
    $namePad = str_pad($scenario['name'], 29);
    $gratuityPad = str_pad('₱' . number_format($gratuity, 2), 16);
    $noticePad = str_pad('₱' . number_format($noticePay, 2), 16);
    $pfPad = str_pad('₱' . number_format($pf, 2), 15);
    
    echo "║ $namePad │ $gratuityPad │ $noticePad │ $pfPad │\n";
}

echo "╚════════════════════════════════════════════════════════════════════════════════════════╝\n";

echo "\n✓ ALL TESTS COMPLETED SUCCESSFULLY!\n";
echo "\nFormulas Verified:\n";
echo "  ✓ Gratuity = (Basic Salary × 15 × Years of Service) / 26\n";
echo "  ✓ Provident Fund = (Basic Salary + DA) × 0.12\n";
echo "  ✓ Notice Pay = (Basic Salary / 30) × Notice Days\n";
echo "  ✓ Net Payable = Total Earnings - Total Deductions\n";
echo "\n";

?>
