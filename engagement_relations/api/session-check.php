<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate, private');
header('Pragma: no-cache');
header('Expires: -1');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'message' => 'Session expired'
    ]);
    exit;
}

echo json_encode([
    'authenticated' => true,
    'user_id' => $_SESSION['user']['id'],
    'user_name' => $_SESSION['user']['name']
]);
