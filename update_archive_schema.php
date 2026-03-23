<?php
require_once 'auth/database.php';
$db = Database::getInstance();

try {
    // Alter the enum to include 'certification'
    $alterQuery = "ALTER TABLE ld_archive MODIFY COLUMN archive_type ENUM('course', 'program', 'certification') NOT NULL";
    $db->getConnection()->exec($alterQuery);

    echo "Successfully updated ld_archive table to include 'certification' type\n";

    // Verify the change
    $stmt = $db->getConnection()->query('DESCRIBE ld_archive');
    echo "\nUpdated ld_archive table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === 'archive_type') {
            echo "- {$row['Field']}: {$row['Type']} ({$row['Null']})\n";
            break;
        }
    }

} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage() . '\n';
}
?>