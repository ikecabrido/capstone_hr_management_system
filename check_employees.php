<?php
require_once 'auth/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT employee_id, full_name FROM employees LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Employees in database:\n";
foreach($results as $row) {
    echo $row['employee_id'] . ' - ' . $row['full_name'] . "\n";
}
?>