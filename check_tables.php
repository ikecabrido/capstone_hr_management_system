<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== rao_hired_applicants structure ===\n";
$stmt = $pdo->query("DESC rao_hired_applicants");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "  {$col['Field']} ({$col['Type']})\n";
}

echo "\n=== rao_hired_applicants sample data ===\n";
$stmt = $pdo->query("SELECT * FROM rao_hired_applicants LIMIT 3");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($data) . " records\n";
if (count($data) > 0) {
    echo "First record:\n";
    print_r($data[0]);
}

echo "\n=== Checking if we need to join with applicants or applications ===\n";
$tables = ['applicants', 'applications', 'rao_applicants', 'rao_applications'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $t");
        $count = $stmt->fetchColumn();
        echo "Table '$t' exists with $count records\n";
    } catch (Exception $e) {
        echo "Table '$t' doesn't exist\n";
    }
}
?>
