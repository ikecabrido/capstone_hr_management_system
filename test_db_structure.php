<?php
require_once 'auth/database.php';
$db = Database::getInstance();

try {
    // Check if ld_archive table has the right structure
    $stmt = $db->getConnection()->query('DESCRIBE ld_archive');
    echo "ld_archive table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']} ({$row['Null']})\n";
    }

    // Check if there are any archived items
    $stmt = $db->getConnection()->query('SELECT COUNT(*) as count FROM ld_archive');
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nTotal archived items: {$count['count']}\n";

    // Check if courses table exists and has status column
    $stmt = $db->getConnection()->query('DESCRIBE courses');
    echo "\nCourses table has status column: ";
    $hasStatus = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === 'status') {
            $hasStatus = true;
            break;
        }
    }
    echo ($hasStatus ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage() . '\n';
}
?>