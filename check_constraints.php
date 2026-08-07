<?php
require_once __DIR__ . '/auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Check foreign key constraints
    $stmt = $db->query('
        SELECT
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_NAME = "employees"
        AND TABLE_SCHEMA = DATABASE()
    ');

    echo "Tables with foreign keys to employees:\n";
    $constraints = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['TABLE_NAME'] . '.' . $row['COLUMN_NAME'] . ' -> employees.' . $row['REFERENCED_COLUMN_NAME'] . "\n";
        $constraints[] = $row;
    }

    if (empty($constraints)) {
        echo "No foreign key constraints found.\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>