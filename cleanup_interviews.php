<?php
require_once 'auth/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "DELETE FROM exit_interviews WHERE employee_id NOT IN (SELECT employee_id FROM employees)";
$stmt = $conn->prepare($sql);
$result = $stmt->execute();

echo "Deleted orphaned interviews. Rows affected: " . $stmt->rowCount() . "\n";

// Check remaining scheduled interviews
$sql = "SELECT COUNT(*) as count FROM exit_interviews WHERE status = 'scheduled'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

echo "Remaining scheduled interviews: " . $count . "\n";
?>