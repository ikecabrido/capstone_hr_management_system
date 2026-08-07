<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== pr_teacher_loads table structure ===\n";
$stmt = $pdo->query("DESC pr_teacher_loads");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "  {$col['Field']} ({$col['Type']}) - Null: {$col['Null']}, Key: {$col['Key']}, Default: {$col['Default']}\n";
}

echo "\n=== Current data in pr_teacher_loads ===\n";
$stmt = $pdo->query("SELECT * FROM pr_teacher_loads");
$loads = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total records: " . count($loads) . "\n";
if (count($loads) > 0) {
    echo "Sample:\n";
    print_r($loads[0]);
}
?>
