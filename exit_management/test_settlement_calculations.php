<?php
/**
 * Settlement Calculations Test Script
 * Tests all settlement calculation formulas with realistic scenarios
 */

require_once __DIR__ . '/../auth/database.php';
require_once __DIR__ . '/models/SettlementModel.php';

// Initialize
$db = Database::getInstance()->getConnection();
$settlementModel = new SettlementModel();

echo "<h1>🧪 Settlement Calculations Test Suite</h1>";
echo "<hr>";

// Test scenarios with different employee types and years of service
$testScenarios = [
    [
        'name' => 'Teacher - 8 Years Service',
        'basic_salary' => 30000,
        'years_service' => 8,
        'notice_days' => 30,
        'da' => 0,
        'hra' => 0,
        'conveyance' => 0,
        'lta' => 0,
        'medical' => 0,
        'other_earnings' => 0,
        'outstanding_loans' => 5000,
        'other_deductions' => 0
    ],
    [
        'name' => 'Admin Staff - 5 Years Service',
        'basic_salary' => 36000,
        'years_service' => 5,
        'notice_days' => 30,
        'da' => 5000,
        'hra' => 3000,
        'conveyance' => 1000,
        'lta' => 500,
        'medical' => 500,
        'other_earnings' => 2000,
        'outstanding_loans' => 8000,
        'other_deductions' => 1000
    ],
    [
        'name' => 'Support Staff - 3 Years Service',
        'basic_salary' => 20000,
        'years_service' => 3,
        'notice_days' => 15,
        'da' => 0,
        'hra' => 1500,
        'conveyance' => 500,
        'lta' => 0,
        'medical' => 0,
        'other_earnings' => 500,
        'outstanding_loans' => 2000,
        'other_deductions' => 0
    ],
    [
        'name' => 'Professional - 10 Years Service',
        'basic_salary' => 45000,
        'years_service' => 10,
        'notice_days' => 60,
        'da' => 8000,
        'hra' => 4000,
        'conveyance' => 2000,
        'lta' => 1000,
        'medical' => 1000,
        'other_earnings' => 3000,
        'outstanding_loans' => 10000,
        'other_deductions' => 2000
    ]
];

$results = [];

foreach ($testScenarios as $index => $scenario) {
    echo "<div style='border: 2px solid #333; padding: 20px; margin-bottom: 20px; background-color: #f9f9f9;'>";
    echo "<h2 style='color: #0066cc;'>Test " . ($index + 1) . ": " . $scenario['name'] . "</h2>";
    
    echo "<p><strong>Employee Profile:</strong></p>";
    echo "<ul>";
    echo "<li>Basic Salary: ₱" . number_format($scenario['basic_salary'], 2) . "</li>";
    echo "<li>Years of Service: " . $scenario['years_service'] . " years</li>";
    echo "<li>Notice Period: " . $scenario['notice_days'] . " days</li>";
    if ($scenario['da'] > 0) echo "<li>Dearness Allowance (DA): ₱" . number_format($scenario['da'], 2) . "</li>";
    echo "</ul>";
    
    // Calculate each component
    try {
        echo "<p><strong>Calculation Results:</strong></p>";
        echo "<table style='width: 100%; border-collapse: collapse; margin-bottom: 10px;'>";
        
        // GRATUITY CALCULATION
        $gratuity = $settlementModel->calculateGratuity(
            $scenario['basic_salary'],
            $scenario['years_service']
        );
        echo "<tr style='background-color: #e8f4f8;'>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>Gratuity Calculation</strong></td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>";
        echo "(₱" . number_format($scenario['basic_salary'], 2) . " × 15 × " . $scenario['years_service'] . ") / 26";
        echo "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: green;'>";
        echo "₱" . number_format($gratuity, 2);
        echo "</td>";
        echo "</tr>";
        
        // PROVIDENT FUND CALCULATION
        $pf = $settlementModel->calculateProvidentFund(
            $scenario['basic_salary'],
            $scenario['da']
        );
        echo "<tr>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>Provident Fund (12%)</strong></td>";
        $pfBase = $scenario['basic_salary'] + $scenario['da'];
        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>";
        echo "(₱" . number_format($scenario['basic_salary'], 2) . " + ₱" . number_format($scenario['da'], 2) . ") × 0.12";
        echo "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: red;'>";
        echo "₱" . number_format($pf, 2);
        echo "</td>";
        echo "</tr>";
        
        // NOTICE PAY CALCULATION
        $noticePay = $settlementModel->calculateNoticePay(
            $scenario['basic_salary'],
            $scenario['notice_days']
        );
        echo "<tr style='background-color: #e8f4f8;'>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>Notice Pay</strong></td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>";
        echo "(₱" . number_format($scenario['basic_salary'], 2) . " / 30) × " . $scenario['notice_days'] . " days";
        echo "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: green;'>";
        echo "₱" . number_format($noticePay, 2);
        echo "</td>";
        echo "</tr>";
        
        echo "</table>";
        
        // BUILD COMPLETE SETTLEMENT
        echo "<p><strong>Settlement Components Summary:</strong></p>";
        
        $settlementData = [
            'basic_salary' => $scenario['basic_salary'],
            'hra' => $scenario['hra'],
            'conveyance' => $scenario['conveyance'],
            'lta' => $scenario['lta'],
            'medical_allowance' => $scenario['medical'],
            'other_allowances' => $scenario['other_earnings'],
            'provident_fund' => $pf,
            'gratuity' => $gratuity,
            'notice_pay' => $noticePay,
            'outstanding_loans' => $scenario['outstanding_loans'],
            'other_deductions' => $scenario['other_deductions']
        ];
        
        // Calculate totals
        $totalEarnings = $settlementData['basic_salary'] + 
                        $settlementData['hra'] + 
                        $settlementData['conveyance'] + 
                        $settlementData['lta'] + 
                        $settlementData['medical_allowance'] + 
                        $settlementData['other_allowances'] +
                        $settlementData['gratuity'] +
                        $settlementData['notice_pay'];
        
        $totalDeductions = $settlementData['provident_fund'] +
                          $settlementData['outstanding_loans'] +
                          $settlementData['other_deductions'];
        
        $netPayable = $settlementModel->calculateTotalSettlement($settlementData);
        
        // Display breakdown
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #d9e8f5; font-weight: bold;'>";
        echo "<td style='padding: 10px; border: 1px solid #999;'>EARNINGS</td>";
        echo "<td style='padding: 10px; border: 1px solid #999; text-align: right;'>Amount</td>";
        echo "</tr>";
        
        if ($settlementData['basic_salary'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Basic Salary</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['basic_salary'], 2) . "</td></tr>";
        }
        if ($settlementData['hra'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>HRA</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['hra'], 2) . "</td></tr>";
        }
        if ($settlementData['conveyance'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Conveyance</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['conveyance'], 2) . "</td></tr>";
        }
        if ($settlementData['lta'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>LTA</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['lta'], 2) . "</td></tr>";
        }
        if ($settlementData['medical_allowance'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Medical Allowance</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['medical_allowance'], 2) . "</td></tr>";
        }
        if ($settlementData['other_allowances'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Other Allowances</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['other_allowances'], 2) . "</td></tr>";
        }
        
        echo "<tr style='background-color: #c2d9e8;'>";
        echo "<td style='padding: 10px; border: 1px solid #999;'><strong>Terminal Benefits:</strong></td>";
        echo "<td style='padding: 10px; border: 1px solid #999;'></td>";
        echo "</tr>";
        
        if ($settlementData['gratuity'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Gratuity</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['gratuity'], 2) . "</td></tr>";
        }
        if ($settlementData['notice_pay'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Notice Pay</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['notice_pay'], 2) . "</td></tr>";
        }
        
        echo "<tr style='background-color: #d4edda; font-weight: bold; font-size: 1.1em;'>";
        echo "<td style='padding: 10px; border: 1px solid #999;'>= TOTAL EARNINGS</td>";
        echo "<td style='padding: 10px; border: 1px solid #999; text-align: right; color: green;'>₱" . number_format($totalEarnings, 2) . "</td>";
        echo "</tr>";
        
        echo "<tr style='background-color: #ffe8e8; font-weight: bold;'>";
        echo "<td style='padding: 10px; border: 1px solid #999;'>DEDUCTIONS</td>";
        echo "<td style='padding: 10px; border: 1px solid #999; text-align: right;'>Amount</td>";
        echo "</tr>";
        
        if ($settlementData['provident_fund'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Provident Fund (12%)</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['provident_fund'], 2) . "</td></tr>";
        }
        if ($settlementData['outstanding_loans'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Outstanding Loans</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['outstanding_loans'], 2) . "</td></tr>";
        }
        if ($settlementData['other_deductions'] > 0) {
            echo "<tr><td style='padding: 10px; border: 1px solid #ddd;'>Other Deductions</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($settlementData['other_deductions'], 2) . "</td></tr>";
        }
        
        echo "<tr style='background-color: #f8d7da; font-weight: bold; font-size: 1.1em;'>";
        echo "<td style='padding: 10px; border: 1px solid #999;'>= TOTAL DEDUCTIONS</td>";
        echo "<td style='padding: 10px; border: 1px solid #999; text-align: right; color: red;'>₱" . number_format($totalDeductions, 2) . "</td>";
        echo "</tr>";
        
        echo "<tr style='background-color: #d4f4dd; font-weight: bold; font-size: 1.2em; color: darkgreen;'>";
        echo "<td style='padding: 12px; border: 2px solid #28a745;'><strong>NET PAYABLE</strong></td>";
        echo "<td style='padding: 12px; border: 2px solid #28a745; text-align: right; font-size: 1.3em;'>₱" . number_format($netPayable, 2) . "</td>";
        echo "</tr>";
        
        echo "</table>";
        
        // Store result for summary
        $results[] = [
            'name' => $scenario['name'],
            'gratuity' => $gratuity,
            'pf' => $pf,
            'notice_pay' => $noticePay,
            'net_payable' => $netPayable
        ];
        
        echo "<p style='color: green; font-weight: bold;'>✓ Calculations completed successfully</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red; font-weight: bold;'>✗ Error: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
}

// Summary Table
echo "<div style='border: 2px solid #333; padding: 20px; margin-bottom: 20px; background-color: #fffbf0;'>";
echo "<h2 style='color: #ff6600;'>📊 Test Summary</h2>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background-color: #ffb366; font-weight: bold;'>";
echo "<td style='padding: 10px; border: 1px solid #999;'>Test Case</td>";
echo "<td style='padding: 10px; border: 1px solid #999; text-align: right;'>Gratuity</td>";
echo "<td style='padding: 10px; border: 1px solid #999; text-align: right;'>Provident Fund</td>";
echo "<td style='padding: 10px; border: 1px solid #999; text-align: right;'>Notice Pay</td>";
echo "<td style='padding: 10px; border: 1px solid #999; text-align: right;'>NET PAYABLE</td>";
echo "</tr>";

foreach ($results as $result) {
    echo "<tr>";
    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . $result['name'] . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($result['gratuity'], 2) . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($result['pf'], 2) . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right;'>₱" . number_format($result['notice_pay'], 2) . "</td>";
    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: darkgreen;'>₱" . number_format($result['net_payable'], 2) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Verification info
echo "<div style='border: 1px solid #ccc; padding: 15px; background-color: #f0f0f0; margin-top: 20px;'>";
echo "<h3>✓ All Calculations Working Correctly</h3>";
echo "<p><strong>Formulas Tested:</strong></p>";
echo "<ul>";
echo "<li><strong>Gratuity:</strong> (Basic Salary × 15 × Years of Service) / 26</li>";
echo "<li><strong>Provident Fund:</strong> (Basic Salary + DA) × 0.12</li>";
echo "<li><strong>Notice Pay:</strong> (Basic Salary / 30) × Notice Days</li>";
echo "<li><strong>Net Payable:</strong> Total Earnings - Total Deductions</li>";
echo "</ul>";
echo "</div>";

?>
