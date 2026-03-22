<?php
session_start();
require_once '../../auth/database.php';

$database = Database::getInstance();
$db = $database->getConnection();

// Debug flexible schedules
$query = "SELECT * FROM flexible_schedules ORDER BY employee_id, schedule_date, start_time";
$stmt = $db->prepare($query);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Current Flexible Schedules in Database:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr>
  <th>ID</th>
  <th>Employee ID</th>
  <th>Schedule Date</th>
  <th>Day of Week</th>
  <th>Start Time</th>
  <th>End Time</th>
  <th>Repeat Until</th>
  <th>Notes</th>
  <th>Created At</th>
</tr>";

foreach ($schedules as $s) {
    echo "<tr>";
    echo "<td>" . $s['id'] . "</td>";
    echo "<td>" . htmlspecialchars($s['employee_id']) . "</td>";
    echo "<td>" . $s['schedule_date'] . "</td>";
    echo "<td>" . (isset($s['day_of_week']) ? $s['day_of_week'] : 'N/A') . "</td>";
    echo "<td>" . $s['start_time'] . "</td>";
    echo "<td>" . $s['end_time'] . "</td>";
    echo "<td>" . ($s['repeat_until'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($s['notes'] ?? '') . "</td>";
    echo "<td>" . ($s['created_at'] ?? 'N/A') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p>Total Records: " . count($schedules) . "</p>";

// Group by employee
echo "<h3>Schedules by Employee:</h3>";
$by_employee = [];
foreach ($schedules as $s) {
    $emp_id = $s['employee_id'];
    if (!isset($by_employee[$emp_id])) {
        $by_employee[$emp_id] = [];
    }
    $by_employee[$emp_id][] = $s;
}

foreach ($by_employee as $emp_id => $emp_schedules) {
    echo "<p><strong>Employee $emp_id:</strong> " . count($emp_schedules) . " schedules</p>";
    foreach ($emp_schedules as $sch) {
        echo "&nbsp;&nbsp;- " . $sch['schedule_date'] . " " . $sch['start_time'] . "-" . $sch['end_time'] . "<br>";
    }
}

// Show unique dates and times to see if they match
echo "<h3>Unique Schedule Combinations:</h3>";
$combinations = [];
foreach ($schedules as $s) {
    $combo = $s['schedule_date'] . " " . $s['start_time'] . "-" . $s['end_time'];
    if (!isset($combinations[$combo])) {
        $combinations[$combo] = [];
    }
    $combinations[$combo][] = $s['employee_id'];
}

foreach ($combinations as $combo => $employees) {
    echo "<p><strong>$combo:</strong> " . implode(", ", $employees) . "</p>";
}
?>
