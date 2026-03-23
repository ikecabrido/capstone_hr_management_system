<?php
require_once 'auth/database.php';
try {
    $db = Database::getInstance()->getConnection();

    // Check if enrollments table exists
    $stmt = $db->query("SHOW TABLES LIKE 'enrollments'");
    $tableExists = $stmt->rowCount() > 0;

    if (!$tableExists) {
        echo "ERROR: enrollments table does not exist!\n";
        exit;
    }

    // Check total enrollments
    $stmt = $db->query('SELECT COUNT(*) as count FROM enrollments');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Total enrollments: ' . $result['count'] . "\n";

    // Check if courses table has data
    $stmt = $db->query('SELECT COUNT(*) as count FROM courses');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Total courses: ' . $result['count'] . "\n";

    // Check if users table has data
    $stmt = $db->query('SELECT COUNT(*) as count FROM users');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Total users: ' . $result['count'] . "\n";

    // Try the same query as the model
    $stmt = $db->prepare("SELECT e.*, c.title as course_title, c.instructor FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.employee_id = ? ORDER BY e.enrolled_at DESC");
    $stmt->execute([1]); // Test with user ID 1
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo 'Enrollments for user ID 1: ' . count($enrollments) . "\n";
    foreach ($enrollments as $enrollment) {
        echo '- ' . $enrollment['course_title'] . ' (Status: ' . $enrollment['status'] . ")\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
