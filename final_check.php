<?php
require_once 'auth/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT COUNT(*) as total FROM exit_interviews";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total interviews in database: " . $result['total'] . "\n";

$sql = "SELECT COUNT(*) as scheduled FROM exit_interviews WHERE status = 'scheduled'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Scheduled interviews: " . $result['scheduled'] . "\n";
?>