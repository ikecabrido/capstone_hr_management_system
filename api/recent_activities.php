<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

session_start();

require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$limit = max(1, min(50, $limit));

$activities = [];

try {
    $stmt = $db->prepare("
        SELECT 
            id,
            activity_type,
            description,
            status,
            table_name,
            record_id,
            performed_by,
            created_at
        FROM re_activity_log
        ORDER BY created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('recent_activities.php error: ' . $e->getMessage());
}

echo json_encode([
    'success' => true,
    'activities' => $activities
]);
