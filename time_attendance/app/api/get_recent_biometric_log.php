<?php
require_once '../models/BiometricModule.php';
require_once '../../../auth/database.php';

header('Content-Type: application/json');

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    $module = new BiometricModule($db);
    $module->ensureTables();

    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 1;
    $logs = $module->getRecentLogs($limit);

    echo json_encode(['success' => true, 'logs' => $logs]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
