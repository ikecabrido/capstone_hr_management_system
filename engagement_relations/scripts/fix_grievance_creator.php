<?php
require_once __DIR__ . '/../../auth/database.php';

// This script sets NULL created_by_user_id in eer_grievances to the hr_engagement user id
// Usage: php fix_grievance_creator.php

try {
    $db = Database::getInstance()->getConnection();

    // find hr_engagement user id
    $stmt = $db->prepare("SELECT id FROM users WHERE username = 'hr_engagement' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row && !empty($row['id'])) {
        $creatorId = $row['id'];
    } else {
        $stmt = $db->prepare("SELECT id FROM users WHERE role = 'engagement_relations' LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row && !empty($row['id'])) {
            $creatorId = $row['id'];
        } else {
            throw new Exception('Could not find an engagement_relations user (hr_engagement) in users table.');
        }
    }

    // show count before
    $before = $db->query("SELECT COUNT(*) AS c FROM eer_grievances WHERE created_by_user_id IS NULL")->fetch();
    echo "Grievances with NULL created_by_user_id before: " . ($before['c'] ?? 0) . PHP_EOL;

    // update rows
    $update = $db->prepare("UPDATE eer_grievances SET created_by_user_id = :creator WHERE created_by_user_id IS NULL");
    $update->execute(['creator' => $creatorId]);
    echo "Updated rows: " . $update->rowCount() . PHP_EOL;

    // show count after
    $after = $db->query("SELECT COUNT(*) AS c FROM eer_grievances WHERE created_by_user_id IS NULL")->fetch();
    echo "Grievances with NULL created_by_user_id after: " . ($after['c'] ?? 0) . PHP_EOL;

    echo "Creator id used: {$creatorId}\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

return 0;
