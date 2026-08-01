<?php
require_once 'auth/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT id, employee_id, status FROM exit_interviews";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Remaining interviews:\n";
foreach($results as $row) {
    echo "ID: " . $row['id'] . ", Employee ID: " . $row['employee_id'] . ", Status: " . $row['status'] . "\n";
}
?>