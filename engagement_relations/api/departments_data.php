<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query('SELECT id, name FROM departments ORDER BY name ASC');
    $departments = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'departments' => $departments,
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Unable to load departments data.',
        'details' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
