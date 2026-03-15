<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/Auth.php';

try {
    Auth::requireAuth();
    Auth::requirePermission('grievances', 'view');

    // Fallback for MySQL versions without JSON_ARRAYAGG/JSON_OBJECT support
    $stmt = $pdo->query('SELECT g.id, g.employee_id, g.subject, g.description, g.status, g.priority, g.assigned_to, g.created_at, ga.id AS action_id, ga.action_taken, ga.action_by, ga.action_date FROM grievances g LEFT JOIN grievance_actions ga ON g.id = ga.grievance_id ORDER BY g.created_at DESC, ga.action_date ASC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grievancesMap = [];
    foreach ($rows as $row) {
        $gId = $row['id'];

        if (!isset($grievancesMap[$gId])) {
            $grievancesMap[$gId] = [
                'id' => $row['id'],
                'employee_id' => $row['employee_id'],
                'subject' => $row['subject'],
                'description' => $row['description'],
                'status' => $row['status'],
                'priority' => $row['priority'],
                'assigned_to' => $row['assigned_to'],
                'created_at' => $row['created_at'],
                'actions' => []
            ];
        }

        if (!empty($row['action_id'])) {
            $grievancesMap[$gId]['actions'][] = [
                'id' => $row['action_id'],
                'action_taken' => $row['action_taken'],
                'action_by' => $row['action_by'],
                'action_date' => $row['action_date']
            ];
        }
    }

    $grievances = array_values($grievancesMap);

    echo json_encode(['success' => true, 'grievances' => $grievances], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Grievances API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Unable to load grievances data.', 'details' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
