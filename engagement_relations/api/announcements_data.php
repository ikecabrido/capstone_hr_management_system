<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query('SELECT a.id, a.title, a.content, a.created_by, a.created_at, IFNULL(ar.read_at, NULL) AS read_at FROM announcements a LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id ORDER BY a.created_at DESC');
    $rows = $stmt->fetchAll();

    // Convert read_at values into boolean status
    $announcements = array_map(function ($row) {
        return [
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'created_by' => $row['created_by'],
            'created_at' => $row['created_at'],
            'is_read' => !is_null($row['read_at']),
            'read_at' => $row['read_at'],
        ];
    }, $rows);

    echo json_encode(['success' => true, 'announcements' => $announcements], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load announcements.', 'details' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
