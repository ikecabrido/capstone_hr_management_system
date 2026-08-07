<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once __DIR__ . '/auth.php';

try {
    $username = trim($_GET['username'] ?? '');
    $auth = new Auth();
    $ipAddress = $auth->getClientIp();

    // If username provided, check for username+ip block; otherwise rely on IP-based block
    $blockedSeconds = $auth->getActiveBlockRemainingSeconds($username, $ipAddress);

    echo json_encode([
        'success' => true,
        'blocked_seconds' => $blockedSeconds === null ? 0 : (int)$blockedSeconds
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
