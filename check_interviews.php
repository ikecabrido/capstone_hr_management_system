<?php
require_once 'auth/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

try {
    $sql = "SELECT id, employee_id, status FROM exit_interviews WHERE status = 'scheduled'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Scheduled interviews: " . count($results) . "\n";
    foreach($results as $row) {
        echo "ID: " . $row['id'] . ", Employee ID: " . $row['employee_id'] . ", Status: " . $row['status'] . "\n";
    }

    // Check if employees exist
    echo "\nChecking if employees exist:\n";
    foreach($results as $row) {
        $sql = "SELECT COUNT(*) as count FROM employees WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$row['employee_id']]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Employee ID " . $row['employee_id'] . " exists: " . ($exists > 0 ? 'YES' : 'NO') . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>