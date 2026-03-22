<?php
require_once __DIR__ . '/../auth/database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();

    // Counts
    $counts = [];
    $counts['employees'] = (int)$db->query('SELECT COUNT(*) FROM employees')->fetchColumn();
    $counts['resignations'] = (int)$db->query('SELECT COUNT(*) FROM resignations')->fetchColumn();
    $counts['exit_interviews'] = (int)$db->query('SELECT COUNT(*) FROM exit_interviews')->fetchColumn();

    // Sample resignations with employee join
    $stmt = $db->prepare(
        'SELECT r.id, r.employee_id, e.full_name as employee_name, r.resignation_type, r.notice_date, r.last_working_date, r.status
         FROM resignations r
         LEFT JOIN employees e ON r.employee_id = e.employee_id
         ORDER BY r.created_at DESC
         LIMIT 100'
    );
    $stmt->execute();
    $resignations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sample employees
    $stmt2 = $db->prepare('SELECT employee_id, full_name, email FROM employees ORDER BY full_name LIMIT 100');
    $stmt2->execute();
    $employees = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'counts' => $counts,
        'sample_resignations' => $resignations,
        'sample_employees' => $employees
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
