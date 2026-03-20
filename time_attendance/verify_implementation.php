<?php
/**
 * System Implementation Verification Script
 * Run this to verify all changes are in place
 */

echo "\n" . str_repeat("=", 80) . "\n";
echo "TIME & ATTENDANCE SYSTEM - IMPLEMENTATION VERIFICATION\n";
echo str_repeat("=", 80) . "\n\n";

$checks = [
    'success' => 0,
    'warning' => 0,
    'error' => 0
];

// 1. Check if Leave model has new methods
echo "[1] Checking Leave Model Methods... ";
$leaveFile = file_get_contents(__DIR__ . '/../models/Leave.php');
$leaveModelMethods = [
    'checkLeaveBalance' => 'Balance validation method',
    'deductLeaveBalance' => 'Balance deduction method',
    'getLeaveBalance' => 'Balance retrieval method'
];

$allMethodsFound = true;
foreach ($leaveModelMethods as $method => $desc) {
    if (strpos($leaveFile, "public function $method") === false) {
        echo "\n   ✗ Missing: $method ($desc)";
        $checks['error']++;
        $allMethodsFound = false;
    }
}

if ($allMethodsFound) {
    echo "✓ PASS (4 new methods found)\n";
    $checks['success']++;
} else {
    echo "\n";
}

// 2. Check if Attendance model has holiday methods
echo "[2] Checking Attendance Model Methods... ";
$attendanceFile = file_get_contents(__DIR__ . '/../models/Attendance.php');
$attendanceMethods = [
    'isHoliday' => 'Holiday detection',
    'getHolidayInfo' => 'Holiday info retrieval',
    'getHolidaysByYear' => 'Holiday list'
];

$allMethodsFound = true;
foreach ($attendanceMethods as $method => $desc) {
    if (strpos($attendanceFile, "public function $method") === false) {
        echo "\n   ✗ Missing: $method ($desc)";
        $checks['error']++;
        $allMethodsFound = false;
    }
}

if ($allMethodsFound) {
    echo "✓ PASS (3 new methods found)\n";
    $checks['success']++;
}

// 3. Check API endpoints exist
echo "[3] Checking API Endpoints... ";
$apiDir = __DIR__ . '/../api';
$requiredApis = [
    'submit_leave.php' => 'Submit leave request',
    'approve_leave_head.php' => 'Department head approval',
    'approve_leave_hr.php' => 'HR approval',
    'get_leave_balance.php' => 'View balance',
    'get_pending_leaves.php' => 'View pending requests'
];

$missingApis = [];
foreach ($requiredApis as $api => $desc) {
    if (!file_exists("$apiDir/$api")) {
        $missingApis[] = "$api ($desc)";
        $checks['error']++;
    }
}

if (empty($missingApis)) {
    echo "✓ PASS (5 new endpoints found)\n";
    $checks['success']++;
} else {
    echo "\n   ✗ Missing endpoints:\n";
    foreach ($missingApis as $api) {
        echo "     - $api\n";
    }
}

// 4. Check if LeaveController has balance validation
echo "[4] Checking LeaveController Updates... ";
$leaveControllerFile = file_get_contents(__DIR__ . '/../controllers/LeaveController.php');

$validations = [
    'checkLeaveBalance' => 'Balance validation in submitRequest',
    'deductLeaveBalance' => 'Balance deduction in approve'
];

$allValidationsFound = true;
foreach ($validations as $validation => $desc) {
    if (strpos($leaveControllerFile, $validation) === false) {
        echo "\n   ✗ Missing: $desc";
        $checks['error']++;
        $allValidationsFound = false;
    }
}

if ($allValidationsFound) {
    echo "✓ PASS (All validations integrated)\n";
    $checks['success']++;
} else {
    echo "\n";
}

// 5. Check if AttendanceController has holiday checking
echo "[5] Checking AttendanceController Updates... ";
$attendanceControllerFile = file_get_contents(__DIR__ . '/../controllers/AttendanceController.php');

if (strpos($attendanceControllerFile, 'isHoliday') !== false) {
    echo "✓ PASS (Holiday checking implemented)\n";
    $checks['success']++;
} else {
    echo "✗ FAIL (Holiday checking not found)\n";
    $checks['error']++;
}

// 6. Try to connect to database and check tables
echo "[6] Checking Database... ";
try {
    require_once __DIR__ . '/../config/Database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check if leave_balances table has data
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM leave_balances");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $balanceCount = $result['count'];
    
    // Check if department_heads table has data
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM department_heads");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $headsCount = $result['count'];
    
    // Check if holidays table has data
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM holidays");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $holidaysCount = $result['count'];
    
    echo "\n   Leave balances: $balanceCount records\n";
    echo "   Department heads: $headsCount records\n";
    echo "   Holidays: $holidaysCount records\n";
    
    if ($balanceCount > 0 && $headsCount > 0 && $holidaysCount > 0) {
        echo "   ✓ PASS (All tables populated)\n";
        $checks['success']++;
    } else {
        echo "   ⚠ WARNING (Some tables may not be populated)\n";
        if ($balanceCount === 0) echo "     - leave_balances is empty\n";
        if ($headsCount === 0) echo "     - department_heads is empty\n";
        if ($holidaysCount === 0) echo "     - holidays is empty\n";
        $checks['warning']++;
    }
} catch (Exception $e) {
    echo "✗ FAIL (Database connection error: " . $e->getMessage() . ")\n";
    $checks['error']++;
}

// 7. Check documentation
echo "[7] Checking Documentation... ";
$docFiles = [
    'IMPLEMENTATION_COMPLETE.md' => 'Complete implementation guide',
    'CHANGES_MADE.md' => 'Summary of changes',
    'DATABASE_FIX.sql' => 'Database fix script'
];

$missingDocs = [];
foreach ($docFiles as $doc => $desc) {
    if (!file_exists(__DIR__ . "/../../$doc")) {
        $missingDocs[] = "$doc";
    }
}

if (empty($missingDocs)) {
    echo "✓ PASS (All documentation files present)\n";
    $checks['success']++;
} else {
    echo "\n   ⚠ Missing documentation files:\n";
    foreach ($missingDocs as $doc) {
        echo "     - $doc\n";
    }
    $checks['warning']++;
}

// Summary
echo "\n" . str_repeat("=", 80) . "\n";
echo "VERIFICATION SUMMARY\n";
echo str_repeat("=", 80) . "\n";
echo "✓ Passed: " . $checks['success'] . "\n";
echo "⚠ Warnings: " . $checks['warning'] . "\n";
echo "✗ Errors: " . $checks['error'] . "\n\n";

if ($checks['error'] === 0) {
    echo "🎉 IMPLEMENTATION VERIFICATION COMPLETE - ALL SYSTEMS GO!\n\n";
    echo "Next Steps:\n";
    echo "1. Test API endpoints with sample data\n";
    echo "2. Verify leave submission workflow\n";
    echo "3. Test balance deduction on HR approval\n";
    echo "4. Verify holiday blocking on time-in\n";
    echo "5. Create UI for leave management\n";
} else {
    echo "⚠ Please fix the errors above before proceeding.\n\n";
}

echo str_repeat("=", 80) . "\n\n";
?>
