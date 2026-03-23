<?php
require_once 'auth/database.php';
$db = Database::getInstance();
try {
    $stmt = $db->getConnection()->query('SHOW TABLES LIKE "ld_archive"');
    if ($stmt->rowCount() > 0) {
        echo 'ld_archive table exists\n';
    } else {
        echo 'ld_archive table does NOT exist\n';
    }
} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage() . '\n';
}
?>