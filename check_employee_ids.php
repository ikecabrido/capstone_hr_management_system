<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== employees table structure ===\n";
$stmt = $pdo->query("DESC employees");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    if ($col['Field'] == 'employee_id') {
        echo "  {$col['Field']} ({$col['Type']}) - Null: {$col['Null']}, Key: {$col['Key']}\n";
    }
}

echo "\n=== Sample employees ===\n";
$stmt = $pdo->query("SELECT employee_id, full_name, position FROM employees WHERE employment_status='Active' LIMIT 5");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($employees as $emp) {
    echo "ID: {$emp['employee_id']} (type: " . gettype($emp['employee_id']) . "), Name: {$emp['full_name']}, Pos: {$emp['position']}\n";
}

// Check getTeacherEmployees equivalent
echo "\n=== Teacher employees (simulating getTeacherEmployees) ===\n";
$positions = ['Teacher', 'Assistant Teacher', 'Instructor', 'Professor', 'Associate Professor'];
$placeholders = implode(',', array_fill(0, count($positions), '?'));
$stmt = $pdo->prepare("
    SELECT e.employee_id as id, e.full_name AS name, e.position
    FROM employees e
    WHERE e.employment_status = 'Active'
      AND e.position IN ($placeholders)
    ORDER BY e.full_name
");
$stmt->execute($positions);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($teachers as $t) {
    echo "ID: {$t['id']} (type: " . gettype($t['id']) . "), Name: {$t['name']}\n";
}
?>
