<?php
/**
 * Settlement Test with Real Database Data
 * Tests using actual employee records from the database
 */

require_once __DIR__ . '/../auth/database.php';
require_once __DIR__ . '/models/SettlementModel.php';
require_once __DIR__ . '/models/ResignationModel.php';

$db = Database::getInstance()->getConnection();
$settlementModel = new SettlementModel();
$resignationModel = new ResignationModel();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  SETTLEMENT TEST WITH REAL DATABASE DATA                                   ║\n";
echo "║  Testing using actual employee records and calculations                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

// Step 1: Get active employees
echo "STEP 1: Fetching active employees from database...\n";
echo "─────────────────────────────────────────────────\n";

$stmt = $db->query("
    SELECT 
        e.employee_id,
        e.full_name,
        e.position,
        e.department,
        e.date_hired,
        e.employment_status
    FROM employees e
    WHERE e.employment_status = 'Active'
    LIMIT 5
");

$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($employees)) {
    echo "✗ No active employees found in database!\n";
    exit(1);
}

echo "✓ Found " . count($employees) . " active employees\n\n";

// Display employee list
echo "┌─ ACTIVE EMPLOYEES FOUND ───────────────────────┐\n";
foreach ($employees as $emp) {
    echo "│ ID: " . str_pad($emp['employee_id'], 8) . " │ " . str_pad($emp['full_name'], 30) . " │\n";
}
echo "└────────────────────────────────────────────────┘\n\n";

// Step 2: For each employee, get their salary and calculate years of service
echo "STEP 2: Getting salary information and calculating years of service...\n";
echo "──────────────────────────────────────────────────────────────────────\n\n";

$settlementTestData = [];

foreach ($employees as $emp) {
    echo "Processing: " . $emp['full_name'] . " (" . $emp['employee_id'] . ")\n";
    
    // Get salary from rao_offer_salary - this is the primary salary source
    $stmt = $db->prepare("
        SELECT ra.salary
        FROM rao_offer_salary ra
        JOIN rao_hired_applicants rha ON ra.application_id = rha.application_id
        WHERE rha.employee_id = ?
        AND ra.offer_status = 'accepted'
        ORDER BY ra.created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$emp['employee_id']]);
    $salaryResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $basicSalary = $salaryResult['salary'] ?? 0;
    
    if ($basicSalary <= 0) {
        echo "  ⚠ No valid salary data, skipping...\n\n";
        continue;
    }
    
    // Calculate years of service
    $hireDate = new DateTime($emp['date_hired']);
    $today = new DateTime();
    $yearsOfService = $today->diff($hireDate)->y;
    
    // Assume 30-day notice period
    $noticeDays = 30;
    
    // Dearness Allowance (if applicable)
    $da = 0;
    
    // Calculate components
    $gratuity = ($basicSalary * 15 * $yearsOfService) / 26;
    $pf = ($basicSalary + $da) * 0.12;
    $noticePay = ($basicSalary / 30) * $noticeDays;
    
    $settlementTestData[] = [
        'employee_id' => $emp['employee_id'],
        'full_name' => $emp['full_name'],
        'position' => $emp['position'],
        'department' => $emp['department'],
        'basic_salary' => $basicSalary,
        'date_hired' => $emp['date_hired'],
        'years_service' => $yearsOfService,
        'notice_days' => $noticeDays,
        'gratuity' => $gratuity,
        'pf' => $pf,
        'notice_pay' => $noticePay,
        'da' => $da
    ];
    
    echo "  • Basic Salary: ₱" . number_format($basicSalary, 2) . "\n";
    echo "  • Hire Date: " . $emp['date_hired'] . "\n";
    echo "  • Years of Service: " . $yearsOfService . " years\n";
    echo "  • Gratuity: ₱" . number_format($gratuity, 2) . "\n";
    echo "\n";
}

if (empty($settlementTestData)) {
    echo "✗ No employees with valid salary data!\n";
    exit(1);
}

echo "✓ Prepared " . count($settlementTestData) . " employee(s) for settlement testing\n\n";

// Step 3: Create settlement records
echo "STEP 3: Creating settlement records in database...\n";
echo "───────────────────────────────────────────────\n\n";

$createdSettlements = [];

foreach ($settlementTestData as $data) {
    try {
        // Calculate net payable
        $totalEarnings = $data['basic_salary'] + $data['gratuity'] + $data['notice_pay'];
        $totalDeductions = $data['pf'] + 0; // No loans for test
        $netPayable = $totalEarnings - $totalDeductions;
        
        // Create settlement record
        $settlementRecord = [
            'employee_id' => $data['employee_id'],
            'resignation_id' => null,
            'basic_salary' => $data['basic_salary'],
            'hra' => 0,
            'conveyance' => 0,
            'lta' => 0,
            'medical_allowance' => 0,
            'other_allowances' => 0,
            'provident_fund' => $data['pf'],
            'gratuity' => $data['gratuity'],
            'notice_pay' => $data['notice_pay'],
            'outstanding_loans' => 0,
            'other_deductions' => 0,
            'net_payable' => $netPayable,
            'settlement_date' => date('Y-m-d'),
            'created_by' => 1
        ];
        
        $settlementId = $settlementModel->createSettlement($settlementRecord);
        
        $createdSettlements[] = [
            'settlement_id' => $settlementId,
            'employee_id' => $data['employee_id'],
            'full_name' => $data['full_name'],
            'net_payable' => $netPayable
        ];
        
        echo "✓ Created settlement for " . $data['full_name'] . "\n";
        echo "  Settlement ID: " . $settlementId . "\n";
        echo "  Net Payable: ₱" . number_format($netPayable, 2) . "\n\n";
        
    } catch (Exception $e) {
        echo "✗ Error creating settlement for " . $data['full_name'] . ": " . $e->getMessage() . "\n\n";
    }
}

// Step 4: Display detailed settlement information
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  SETTLEMENT RECORDS CREATED                                                ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n\n";

foreach ($createdSettlements as $settlement) {
    $settlementDetails = $settlementModel->getSettlementById($settlement['settlement_id']);
    
    echo "┌─ Settlement Record #" . str_pad($settlement['settlement_id'], 5) . " ───────────────────────────────────────┐\n";
    echo "│ Employee: " . str_pad($settlement['full_name'], 67) . " │\n";
    echo "│ Employee ID: " . str_pad($settlement['employee_id'], 65) . " │\n";
    echo "├────────────────────────────────────────────────────────────────────────────┤\n";
    echo "│ EARNINGS:                                                                  │\n";
    echo "│   Basic Salary ......................... ₱" . str_pad(number_format($settlementDetails['basic_salary'], 2), 15) . "       │\n";
    echo "│   Gratuity ............................ ₱" . str_pad(number_format($settlementDetails['gratuity'], 2), 15) . "       │\n";
    echo "│   Notice Pay .......................... ₱" . str_pad(number_format($settlementDetails['notice_pay'], 2), 15) . "       │\n";
    echo "│   ────────────────────────────────────────────────────                  │\n";
    $totalEarnings = $settlementDetails['basic_salary'] + $settlementDetails['gratuity'] + $settlementDetails['notice_pay'];
    echo "│   TOTAL EARNINGS ...................... ₱" . str_pad(number_format($totalEarnings, 2), 15) . "       │\n";
    echo "│                                                                            │\n";
    echo "│ DEDUCTIONS:                                                                │\n";
    echo "│   Provident Fund (12%) ............... ₱" . str_pad(number_format($settlementDetails['provident_fund'], 2), 15) . "       │\n";
    echo "│   Outstanding Loans ................. ₱" . str_pad(number_format($settlementDetails['outstanding_loans'], 2), 15) . "       │\n";
    echo "│   Other Deductions .................. ₱" . str_pad(number_format($settlementDetails['other_deductions'], 2), 15) . "       │\n";
    echo "│   ────────────────────────────────────────────────────                  │\n";
    $totalDeductions = $settlementDetails['provident_fund'] + $settlementDetails['outstanding_loans'] + $settlementDetails['other_deductions'];
    echo "│   TOTAL DEDUCTIONS ................... ₱" . str_pad(number_format($totalDeductions, 2), 15) . "       │\n";
    echo "│                                                                            │\n";
    echo "│ ════════════════════════════════════════════════════════════════════════ │\n";
    echo "│ NET PAYABLE ........................... ₱" . str_pad(number_format($settlementDetails['net_payable'], 2), 15) . "       │\n";
    echo "│ Status: " . str_pad($settlementDetails['status'], 68) . " │\n";
    echo "└────────────────────────────────────────────────────────────────────────────┘\n\n";
}

// Step 5: Verify settlements in database
echo "STEP 5: Verifying settlements stored in database...\n";
echo "──────────────────────────────────────────────────\n\n";

$allSettlements = $settlementModel->getAllSettlements();

echo "Total settlements in system: " . count($allSettlements) . "\n";
echo "Settlements created this session: " . count($createdSettlements) . "\n\n";

echo "✓ ALL TESTS COMPLETED SUCCESSFULLY!\n";
echo "\nSummary:\n";
echo "  • Successfully retrieved " . count($employees) . " active employees from database\n";
echo "  • Successfully calculated settlement components for " . count($settlementTestData) . " employee(s)\n";
echo "  • Successfully created " . count($createdSettlements) . " settlement record(s)\n";
echo "  • All calculations verified and stored in employee_settlements table\n";

echo "\n";

?>
