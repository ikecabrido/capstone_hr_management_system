<?php

// Set headers FIRST before any output
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Create a simple response function
function sendResponse($success, $message, $statusCode = 200, $redirect = null)
{
    http_response_code($statusCode);
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($redirect) {
        $response['redirect'] = $redirect;
    }
    echo json_encode($response);
    exit;
}

// Handle non-POST requests early
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Method not allowed', 405);
}
try {
    require_once "auth/auth.php";

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        sendResponse(false, 'Username and password are required', 400);
    }

    $auth = new Auth();
    $result = $auth->login($username, $password);

    if (!$result['success']) {
        sendResponse(false, $result['message'], 401);
    }

    // Successful login
    $qrToken = trim($_POST['qr_token'] ?? '');

    if (!empty($qrToken)) {
        sendResponse(
            true,
            'Login successful',
            200,
            'time_attendance/public/qr_scan.php?token=' . urlencode($qrToken)
        );
    }

    sendResponse(true, 'Login successful', 200, 'router.php');
} catch (Exception $e) {
    error_log($e->getMessage());
    sendResponse(false, 'Server error', 500);
}
