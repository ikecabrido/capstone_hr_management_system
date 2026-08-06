<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();

require_once __DIR__ . '/../auth/database.php';

function jsonResponse($data) {
    echo json_encode($data);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
}

function timeAgo($datetime) {
    $time = new DateTime($datetime);
    $now = new DateTime('now');
    $diff = $now->getTimestamp() - $time->getTimestamp();

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 7200) return '1 hour ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 172800) return 'Yesterday';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';

    return $time->format('M j, Y');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    if ($action === 'clear_all') {
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        $stmt->execute();
        jsonResponse(['success' => true, 'message' => 'All notifications cleared']);
    }

    jsonResponse(['success' => false, 'message' => 'Invalid action']);
}

try {
    $stmt = $db->query("SHOW TABLES LIKE 'notifications'");
    $hasNotifications = $stmt->rowCount() > 0;

    if (!$hasNotifications) {
        jsonResponse([
            'success' => true,
            'notifications' => [],
            'unread_count' => 0
        ]);
    }

    $stmt = $db->prepare("
        SELECT id, icon_class, color_theme, message, type, is_read, created_at
        FROM notifications
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $notifications = [];
    $unreadCount = 0;

    foreach ($rows as $row) {
        $notifications[] = [
            'id' => (int)$row['id'],
            'icon_class' => $row['icon_class'],
            'message_text' => $row['message'],
            'status_color' => $row['color_theme'],
            'is_unread' => (bool)((int)$row['is_read'] === 0),
            'time_ago' => timeAgo($row['created_at']),
            'created_at' => $row['created_at']
        ];

        if ((int)$row['is_read'] === 0) {
            $unreadCount++;
        }
    }

    jsonResponse([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
