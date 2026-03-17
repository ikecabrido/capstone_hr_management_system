<?php
require_once "auth/database.php";

$db = Database::getInstance()->getConnection();

// Get all resignations with employee data
$sql = "SELECT r.id, r.employee_id, r.resignation_type, e.employee_id as emp_exists, e.full_name 
        FROM resignations r 
        LEFT JOIN employees e ON r.employee_id = e.employee_id
        ORDER BY r.id";

$stmt = $db->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Resignation ID</th><th>Employee ID</th><th>Type</th><th>Employee Exists</th><th>Employee Name</th></tr>";

foreach ($results as $row) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['employee_id'] . "</td>";
    echo "<td>" . $row['resignation_type'] . "</td>";
    echo "<td>" . ($row['emp_exists'] ? 'YES' : 'NO') . "</td>";
    echo "<td>" . ($row['full_name'] ?: 'MISSING') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check for orphaned resignations
echo "<h3>Summary:</h3>";
$orphaned = $db->query("SELECT COUNT(*) as count FROM resignations r LEFT JOIN employees e ON r.employee_id = e.employee_id WHERE e.employee_id IS NULL");
$orphanedCount = $orphaned->fetch(PDO::FETCH_ASSOC)['count'];
echo "Orphaned resignations (missing employee): " . $orphanedCount . "<br>";

$total = $db->query("SELECT COUNT(*) as count FROM resignations")->fetch(PDO::FETCH_ASSOC)['count'];
echo "Total resignations: " . $total . "<br>";
?>
