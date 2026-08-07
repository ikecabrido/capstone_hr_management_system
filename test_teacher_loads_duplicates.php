<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Current pr_teacher_loads records ===\n";
$stmt = $pdo->query("SELECT id, employee_id, academic_year, semester, total_units, created_by FROM pr_teacher_loads ORDER BY id DESC");
$loads = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total records: " . count($loads) . "\n\n";

foreach ($loads as $load) {
    echo "ID: {$load['id']} | Emp: {$load['employee_id']} | Year: {$load['academic_year']} | Sem: {$load['semester']} | Units: {$load['total_units']} | By: {$load['created_by']}\n";
}

// Check for duplicates on (employee_id, academic_year, semester)
echo "\n=== Checking for duplicate combinations ===\n";
$stmt = $pdo->query("
    SELECT employee_id, academic_year, semester, COUNT(*) as count
    FROM pr_teacher_loads
    GROUP BY employee_id, academic_year, semester
    HAVING count > 1
");
$dupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($dupes) > 0) {
    echo "Found duplicate combinations:\n";
    print_r($dupes);
} else {
    echo "No duplicate combinations\n";
}

// Try inserting a completely new record
echo "\n=== Testing INSERT with new combination ===\n";
$newEmpId = 6;  // Dr. Sophia Lim
$newYear = '2026-2027';
$newSem = '2nd';
$newUnits = 25;

$stmt = $pdo->prepare("
    INSERT INTO pr_teacher_loads 
    (employee_id, academic_year, semester, total_units, created_by)
    VALUES (:eid, :year, :sem, :units, :creator)
    ON DUPLICATE KEY UPDATE
    total_units = :units,
    updated_at = CURRENT_TIMESTAMP
");

$result = $stmt->execute([
    ':eid' => $newEmpId,
    ':year' => $newYear,
    ':sem' => $newSem,
    ':units' => $newUnits,
    ':creator' => 'Test User'
]);

echo "INSERT Result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
echo "Rows affected: " . $stmt->rowCount() . "\n";

if ($result) {
    echo "\n=== Verifying new record ===\n";
    $stmt = $pdo->query("SELECT * FROM pr_teacher_loads WHERE employee_id = 6 AND academic_year = '2026-2027' AND semester = '2nd'");
    $newRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($newRecord) {
        echo "✓ New record found:\n";
        print_r($newRecord);
    } else {
        echo "❌ No record found\n";
    }
}
?>
