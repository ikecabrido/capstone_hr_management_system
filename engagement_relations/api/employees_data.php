<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query('SELECT employee_id AS id, full_name AS name, email, position AS role, employment_status AS status FROM employees ORDER BY full_name ASC');
    $employees = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'employees' => $employees,
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Unable to load employees data.',
        'details' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
