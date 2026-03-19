<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query('SELECT id, employee_id, suggestion_text, status, created_at FROM suggestions ORDER BY created_at DESC');
    $suggestions = $stmt->fetchAll();

    echo json_encode(['success' => true, 'suggestions' => $suggestions], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load suggestions data.', 'details' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
