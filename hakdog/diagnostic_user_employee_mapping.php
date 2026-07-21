<?php
/**
 * Employee-User ID Mapping Diagnostic
 * Shows which user IDs have corresponding employee records
 * Shows gaps where employees need to be created
 */

require_once dirname(__DIR__) . "/auth/database.php";

try {
    $conn = Database::getInstance()->getConnection();
    
    echo "<h2>User-Employee Mapping Diagnostic Report</h2>";
    echo "<p>Generated: " . date('Y-m-d H:i:s') . "</p>";
    
    // Get all users
    $stmt = $conn->prepare("SELECT id, username, full_name, role FROM users ORDER BY id");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all employees
    $stmt = $conn->prepare("SELECT employee_id, full_name, department, position FROM employees ORDER BY employee_id");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create lookup arrays
    $employeeIds = array_column($employees, 'employee_id');
    $userIds = array_column($users, 'id');
    
    echo "<div style='margin: 20px; font-family: monospace;'>";
    
    // Show users without employee records
    echo "<h3>Users WITHOUT Employee Records (⚠️ Cannot use Time & Attendance)</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>User ID</th><th>Username</th><th>Full Name</th><th>Role</th></tr>";
    
    $usersWithoutEmployees = 0;
    foreach ($users as $user) {
        if (!in_array($user['id'], $employeeIds)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
            $usersWithoutEmployees++;
        }
    }
    echo "</table>";
    echo "<p><strong>Total users without employee records: $usersWithoutEmployees</strong></p>";
    
    // Show users with employee records
    echo "<h3>Users WITH Employee Records (✓ Can use Time & Attendance)</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>User ID</th><th>Username</th><th>User Full Name</th><th>Employee ID</th><th>Employee Name</th><th>Department</th><th>Position</th></tr>";
    
    $usersWithEmployees = 0;
    foreach ($users as $user) {
        if (in_array($user['id'], $employeeIds)) {
            $employee = array_values(array_filter($employees, function($e) use ($user) {
                return $e['employee_id'] == $user['id'];
            }))[0];
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($employee['employee_id']) . "</td>";
            echo "<td>" . htmlspecialchars($employee['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($employee['department']) . "</td>";
            echo "<td>" . htmlspecialchars($employee['position']) . "</td>";
            echo "</tr>";
            $usersWithEmployees++;
        }
    }
    echo "</table>";
    echo "<p><strong>Total users with employee records: $usersWithEmployees</strong></p>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
