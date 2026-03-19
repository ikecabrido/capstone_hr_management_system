<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query('SELECT id, employee_id, subject, description, status, priority, assigned_to, created_at, updated_at FROM grievances ORDER BY id DESC');
    $grievances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'grievances' => $grievances], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
