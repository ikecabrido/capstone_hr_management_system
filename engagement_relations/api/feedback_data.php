<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query('SELECT id, employee_id, feedback_text, is_anonymous, status, created_at FROM feedback ORDER BY created_at DESC');
    $feedback = $stmt->fetchAll();

    echo json_encode(['success' => true, 'feedback' => $feedback], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load feedback data.', 'details' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
