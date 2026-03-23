<?php
require_once 'auth/database.php';
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'All tables found: ' . implode(', ', $tables) . PHP_EOL;

    // Check learning-related tables
    $learningTables = array_filter($tables, function($table) {
        return in_array($table, ['training_programs', 'courses', 'enrollments', 'certifications', 'elearning_modules', 'skill_gap_analyses', 'virtual_sessions']);
    });

    echo 'Learning tables found: ' . implode(', ', $learningTables) . PHP_EOL;

    // Check if tables have data
    if (in_array('training_programs', $tables)) {
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM training_programs');
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo 'Training programs: ' . $count['count'] . PHP_EOL;
    }

    if (in_array('courses', $tables)) {
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM courses');
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo 'Courses: ' . $count['count'] . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>