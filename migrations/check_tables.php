<?php
require_once __DIR__ . '/../auth/database.php';
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $tables = ['ta_shift_weekday_times', 'ta_custom_shifts', 'ta_custom_shift_times'];
    $out = [];
    foreach ($tables as $t) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?");
        $stmt->execute([$db->getConnection()->query('select database()')->fetchColumn(0), $t]);
        $row = $stmt->fetch();
        $out[$t] = ($row && $row['c'] > 0) ? true : false;
    }
    echo json_encode($out);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
