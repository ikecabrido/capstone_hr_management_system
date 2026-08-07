<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Remove base_salary column from employees table
try {
    $pdo->exec('ALTER TABLE employees DROP COLUMN base_salary');
    echo "✓ Removed base_salary column from employees table\n";
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n";
}
?>
