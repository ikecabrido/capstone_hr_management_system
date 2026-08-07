<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== pr_teacher_loads detailed table info ===\n";

// Get keys
$stmt = $pdo->query("SHOW KEYS FROM pr_teacher_loads");
$keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Keys/Indexes:\n";
foreach ($keys as $key) {
    echo "  {$key['Key_name']}: {$key['Column_name']} (Unique: " . ($key['Non_unique'] == 0 ? 'YES' : 'NO') . ")\n";
}

// Show create statement
echo "\n=== CREATE TABLE statement ===\n";
$stmt = $pdo->query("SHOW CREATE TABLE pr_teacher_loads");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo $result['Create Table'] . "\n";
?>
