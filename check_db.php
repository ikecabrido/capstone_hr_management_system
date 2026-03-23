<?php
require_once 'auth/database.php';
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query('SELECT id, username, full_name, role FROM users LIMIT 10');
    echo "Users in database:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['id'] . ': ' . $row['username'] . ' (' . $row['full_name'] . ') - ' . $row['role'] . "\n";
    }

    echo "\nEnrollments:\n";
    $stmt = $db->query('SELECT e.*, c.title as course_title FROM enrollments e JOIN courses c ON e.course_id = c.id');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo 'Employee ' . $row['employee_id'] . ' enrolled in: ' . $row['course_title'] . ' (Status: ' . $row['status'] . ")\n";
    }
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
