<?php
// keepalive.php - simple endpoint to refresh session activity
require_once __DIR__ . '/auth.php';

$auth = new Auth();

header('Content-Type: application/json');

if (!$auth->check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Refresh last activity timestamp using auth helper
$auth->refreshSessionActivity();

echo json_encode(['success' => true, 'message' => 'Session refreshed']);
