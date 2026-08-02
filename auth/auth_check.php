<?php

require_once "auth.php";

$auth = new Auth();

if (!$auth->check()) {
    $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    $acceptHeader = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
    $wantsJson = $requestedWith === 'xmlhttprequest' || strpos($acceptHeader, 'application/json') !== false;

    if ($wantsJson) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
        exit;
    }

    header("Location: ../login_form.php");
    exit;
}
