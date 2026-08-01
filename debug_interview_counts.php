<?php
require_once 'auth/database.php';
$db = new Database();
$conn = $db->getConnection();

try {
    // Dashboard count
    $sql = "SELECT COUNT(*) as total FROM exit_interviews WHERE status = 'scheduled'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $dashboard_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Table count (with employee join)
    $sql = "SELECT COUNT(ei.id) as total FROM exit_interviews ei JOIN employees e ON ei.employee_id = e.employee_id WHERE ei.status = 'scheduled'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $table_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Orphaned count
    $sql = "SELECT COUNT(*) as total FROM exit_interviews WHERE status = 'scheduled' AND employee_id NOT IN (SELECT employee_id FROM employees)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $orphaned_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo "Dashboard count (all scheduled): $dashboard_count\n";
    echo "Table count (with employee join): $table_count\n";
    echo "Orphaned interviews: $orphaned_count\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>