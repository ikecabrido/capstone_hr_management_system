<?php

// Set headers FIRST before any output
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Create a simple response function
function sendResponse($success, $message, $statusCode = 200, $redirect = null) {
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
    // Require after headers are set
    require_once "auth/auth.php";
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        sendResponse(false, 'Username and password are required', 400);
    }

    // Initialize Auth - this might throw an exception
    $auth = new Auth();
    
    // Try to login
    $loginResult = $auth->login($username, $password);
    
    if ($loginResult) {
        // Check if there's a QR token to process
        $qrToken = trim($_POST['qr_token'] ?? '');
        
        if (!empty($qrToken)) {
            // Redirect to QR scan handler with token
            sendResponse(true, 'Login successful', 200, 'time_attendance/public/qr_scan.php?token=' . urlencode($qrToken));
        } else {
            // Normal login redirect
            sendResponse(true, 'Login successful', 200, 'router.php');
        }
    } else {
        sendResponse(false, 'Invalid username or password', 401);
    }

} catch (Exception $e) {
    error_log('Login Exception: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
    sendResponse(false, 'Server error: ' . $e->getMessage(), 500);
} catch (Throwable $t) {
    error_log('Login Throwable: ' . $t->getMessage() . ' - ' . $t->getTraceAsString());
    sendResponse(false, 'Server error: ' . $t->getMessage(), 500);
}

?>
