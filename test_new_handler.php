<?php
session_start();
$_SESSION['user']['name'] = 'Test User';

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/auth/database.php';

$pdo = Database::getInstance()->getConnection();

echo "=== TEST: Adding new teacher loads ===\n\n";

// Test 1: Try to add a combination that DEFINITELY doesn't exist
echo "TEST 1: Adding NEW combination (should succeed)\n";
echo "-" . str_repeat("-", 50) . "\n";

$testData = [
    'employee_id' => '6',  // Dr. Sophia Lim
    'academic_year' => '2027-2028',  // Future year
    'semester' => '1st',  // New combination
    'total_units' => '35'
];

echo "Data: Emp " . $testData['employee_id'] . ", " . $testData['academic_year'] . " " . $testData['semester'] . ", " . $testData['total_units'] . " units\n\n";

// Simulate the handler logic
$db = $pdo;

// Check for duplicates
$checkStmt = $db->prepare("
    SELECT id FROM pr_teacher_loads 
    WHERE employee_id = ? AND academic_year = ? AND semester = ?
");
$checkStmt->execute([$testData['employee_id'], $testData['academic_year'], $testData['semester']]);

if ($checkStmt->rowCount() > 0) {
    echo "❌ Found DUPLICATE - would show error\n";
} else {
    echo "✓ No duplicate found\n";
    
    // Try INSERT
    $stmt = $db->prepare("
        INSERT INTO pr_teacher_loads 
        (employee_id, academic_year, semester, total_units, created_by)
        VALUES (:eid, :year, :sem, :units, :creator)
    ");
    
    $result = $stmt->execute([
        ':eid' => $testData['employee_id'],
        ':year' => $testData['academic_year'],
        ':sem' => $testData['semester'],
        ':units' => $testData['total_units'],
        ':creator' => $_SESSION['user']['name']
    ]);
    
    if ($result) {
        echo "✓ INSERT successful - " . $stmt->rowCount() . " row(s) affected\n";
        $newId = $db->lastInsertId();
        echo "✓ New record ID: $newId\n";
    } else {
        echo "❌ INSERT failed: " . json_encode($stmt->errorInfo()) . "\n";
    }
}

// Test 2: Try to add a duplicate
echo "\n\nTEST 2: Adding DUPLICATE (should fail)\n";
echo "-" . str_repeat("-", 50) . "\n";

$dupData = [
    'employee_id' => '4',
    'academic_year' => '2025-2026',
    'semester' => '1st',
    'total_units' => '40'
];

echo "Data: Emp " . $dupData['employee_id'] . ", " . $dupData['academic_year'] . " " . $dupData['semester'] . ", " . $dupData['total_units'] . " units\n";
echo "(This employee/year/semester already exists in the database)\n\n";

$checkStmt = $db->prepare("
    SELECT id FROM pr_teacher_loads 
    WHERE employee_id = ? AND academic_year = ? AND semester = ?
");
$checkStmt->execute([$dupData['employee_id'], $dupData['academic_year'], $dupData['semester']]);

if ($checkStmt->rowCount() > 0) {
    echo "❌ Found DUPLICATE - would show error message\n";
    echo "   Error: 'This teacher already has a load assigned for 2025-2026 1st'\n";
} else {
    echo "✓ No duplicate - would insert\n";
}

// Show all records now
echo "\n\nFINAL: All teacher loads in database:\n";
echo str_repeat("-", 50) . "\n";
$finalStmt = $pdo->query("SELECT id, employee_id, academic_year, semester, total_units FROM pr_teacher_loads ORDER BY id DESC LIMIT 10");
$allRecords = $finalStmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($allRecords as $rec) {
    echo "  ID {$rec['id']}: Emp {$rec['employee_id']}, {$rec['academic_year']} {$rec['semester']}, {$rec['total_units']} units\n";
}
?>
